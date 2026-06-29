<?php

namespace App\Controllers;

use Core\Http\Controller;
use Core\Http\Response;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Ocorrencia;
use App\Models\Usuario;
use App\DTOs\Auth\RegisterDTO;
use App\Services\AuthService;
class DashboardController extends Controller
{
    public function index()
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        $perfil = $user['perfil'] ?? '';
        $data = ['user' => $user];

        if ($perfil === 'responsavel') {
            // Buscar alunos sob a responsabilidade deste usuário
            $sql = "SELECT a.*, ra.parentesco FROM alunos a 
                    JOIN responsaveis_alunos ra ON ra.aluno_id = a.id 
                    WHERE ra.responsavel_id = :responsavel_id";
            $alunosData = (new Aluno())->query($sql, ['responsavel_id' => $user['id']]);

            $alunos = [];
            foreach ($alunosData as $aData) {
                $aluno = new Aluno();
                foreach ($aData as $key => $val) {
                    $aluno->$key = $val;
                }

                // Buscar ocorrências aprovadas do aluno
                $sqlOcorr = "SELECT o.*, t.nome as turma_nome, u.nome as autor_nome FROM ocorrencias o 
                             JOIN turmas t ON t.id = o.turma_id 
                             JOIN usuarios u ON u.id = o.autor_id 
                             WHERE o.aluno_id = :aluno_id AND o.status = 'aprovada'
                             ORDER BY o.created_at DESC";
                $ocorrencias = (new Ocorrencia())->query($sqlOcorr, ['aluno_id' => $aluno->id]);

                $alunos[] = [
                    'model' => $aluno,
                    'ocorrencias' => $ocorrencias,
                    'parentesco' => $aData['parentesco']
                ];
            }
            $data['alunos'] = $alunos;

        } elseif ($perfil === 'professor') {
            // Buscar todos os alunos e turmas (para o formulário de cadastro de ocorrências)
            $data['alunos'] = (new Aluno())->all();
            $data['turmas'] = (new Turma())->all();

            // Buscar ocorrências cadastradas por este professor
            $sqlMinhas = "SELECT o.*, a.nome as aluno_nome, t.nome as turma_nome FROM ocorrencias o 
                          JOIN alunos a ON a.id = o.aluno_id 
                          JOIN turmas t ON t.id = o.turma_id 
                          WHERE o.autor_id = :autor_id 
                          ORDER BY o.created_at DESC";
            $data['minhas_ocorrencias'] = (new Ocorrencia())->query($sqlMinhas, ['autor_id' => $user['id']]);

            // Buscar turmas que este professor coordena
            $turmasCoordenadas = (new Turma())->where('professor_coordenador_id', '=', $user['id'])->get();
            $data['turmas_coordenadas'] = $turmasCoordenadas;

            // Se coordena alguma turma, buscar as ocorrências pendentes daquela turma
            $pendentes = [];
            if (!empty($turmasCoordenadas)) {
                $turmasIds = array_map(fn($t) => $t->id, $turmasCoordenadas);
                $idsPlaceholder = implode(',', $turmasIds);
                
                $sqlPendentes = "SELECT o.*, a.nome as aluno_nome, t.nome as turma_nome, u.nome as autor_nome 
                                 FROM ocorrencias o 
                                 JOIN alunos a ON a.id = o.aluno_id 
                                 JOIN turmas t ON t.id = o.turma_id 
                                 JOIN usuarios u ON u.id = o.autor_id 
                                 WHERE o.status = 'pendente' AND o.turma_id IN ($idsPlaceholder)
                                 ORDER BY o.created_at ASC";
                $pendentes = (new Ocorrencia())->query($sqlPendentes);
            }
            $data['ocorrencias_pendentes'] = $pendentes;

        } elseif ($perfil === 'secretaria') {
            // Buscar todas as turmas para criação de ocorrências e turmas
            $data['turmas'] = (new Turma())->all();

            // Buscar ocorrências pendentes globais
            $sqlPendentes = "SELECT o.*, a.nome as aluno_nome, t.nome as turma_nome, u.nome as autor_nome 
                             FROM ocorrencias o 
                             JOIN alunos a ON a.id = o.aluno_id 
                             JOIN turmas t ON t.id = o.turma_id 
                             JOIN usuarios u ON u.id = o.autor_id 
                             WHERE o.status = 'pendente'
                             ORDER BY o.created_at ASC";
            $data['ocorrencias_pendentes'] = (new Ocorrencia())->query($sqlPendentes);

            // Buscar todos os alunos com suas turmas e ocorrências
            $alunos = (new Aluno())->all();
            $alunosComDetalhes = [];
            foreach ($alunos as $aluno) {
                // Turma atual
                $sqlTurma = "SELECT t.nome FROM turmas t 
                             JOIN alunos_turmas at ON at.turma_id = t.id 
                             WHERE at.aluno_id = :aluno_id LIMIT 1";
                $turmaResult = (new Turma())->query($sqlTurma, ['aluno_id' => $aluno->id]);
                $turmaNome = !empty($turmaResult) ? $turmaResult[0]['nome'] : 'Sem turma';

                // Ocorrências aprovadas
                $countOcorrencias = (new Ocorrencia())
                    ->where('aluno_id', '=', $aluno->id)
                    ->where('status', '=', 'aprovada')
                    ->count();

                // Responsáveis
                $responsaveis = $aluno->responsaveis();

                $alunosComDetalhes[] = [
                    'model' => $aluno,
                    'turma' => $turmaNome,
                    'ocorrencias_count' => $countOcorrencias,
                    'responsaveis' => $responsaveis
                ];
            }
            $data['alunos_detalhes'] = $alunosComDetalhes;

            // Lista de todos os usuários cadastrados como responsáveis (para vincular a alunos)
            $data['responsaveis_disponiveis'] = (new Usuario())->where('perfil', '=', 'responsavel')->get();
            // Lista de professores (para associar à coordenação de turmas)
            $data['professores'] = (new Usuario())->where('perfil', '=', 'professor')->get();
        }

        return view('dashboard', $data);
    }

    /**
     * Cadastra um novo aluno (Ação da Secretaria)
     */
    public function storeAluno()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return response('Não autorizado', 403);
        }

        $nome = trim((string)request()->get('nome'));
        $dataNasc = trim((string)request()->get('data_nascimento'));

        if (!$nome) {
            fail_validation(['nome' => 'O nome do aluno é obrigatório.']);
        }

        $aluno = new Aluno();
        $alunoId = $aluno->insert([
            'nome' => $nome,
            'data_nascimento' => $dataNasc ?: null
        ]);

        session()->flash('success', 'Aluno cadastrado com sucesso!');
        return Response::makeRedirect('/dashboard');
    }

    /**
     * Cadastra uma nova turma (Ação da Secretaria)
     */
    public function storeTurma()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return response('Não autorizado', 403);
        }

        $nome = trim((string)request()->get('nome'));
        $coordId = (int)request()->get('professor_coordenador_id');

        if (!$nome) {
            fail_validation(['nome' => 'O nome da turma é obrigatório.']);
        }

        $turma = new Turma();
        $turma->insert([
            'nome' => $nome,
            'professor_coordenador_id' => $coordId ?: null
        ]);

        session()->flash('success', 'Turma cadastrada com sucesso!');
        return Response::makeRedirect('/dashboard');
    }

    /**
     * Associa um aluno a uma turma (Ação da Secretaria)
     */
    public function vincularTurma()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return response('Não autorizado', 403);
        }

        $alunoId = (int)request()->get('aluno_id');
        $turmaId = (int)request()->get('turma_id');
        $ano = (int)request()->get('ano_letivo') ?: (int)date('Y');

        if (!$alunoId || !$turmaId) {
            fail_validation(['turma_id' => 'Aluno e Turma são obrigatórios.']);
        }

        // Deleta vínculos anteriores para simplificar
        $db = \Core\Database\Connection::getInstance();
        $stmt = $db->prepare("DELETE FROM alunos_turmas WHERE aluno_id = :aluno_id");
        $stmt->execute(['aluno_id' => $alunoId]);

        // Insere novo vínculo
        $stmtInsert = $db->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, ano_letivo, created_at, updated_at) VALUES (:aluno_id, :turma_id, :ano, NOW(), NOW())");
        $stmtInsert->execute([
            'aluno_id' => $alunoId,
            'turma_id' => $turmaId,
            'ano' => $ano
        ]);

        session()->flash('success', 'Aluno vinculado à turma com sucesso!');
        return Response::makeRedirect('/dashboard');
    }

    /**
     * Associa um aluno a um responsável (Ação da Secretaria)
     */
    public function vincularResponsavel()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return response('Não autorizado', 403);
        }

        $alunoId = (int)request()->get('aluno_id');
        $respId = (int)request()->get('responsavel_id');
        $parentesco = trim((string)request()->get('parentesco')) ?: 'Responsável';

        if (!$alunoId || !$respId) {
            fail_validation(['responsavel_id' => 'Aluno e Responsável são obrigatórios.']);
        }

        $db = \Core\Database\Connection::getInstance();
        
        // Verifica se vínculo já existe
        $stmtCheck = $db->prepare("SELECT 1 FROM responsaveis_alunos WHERE aluno_id = :aluno_id AND responsavel_id = :resp_id");
        $stmtCheck->execute(['aluno_id' => $alunoId, 'resp_id' => $respId]);
        
        if (!$stmtCheck->fetch()) {
            $stmtInsert = $db->prepare("INSERT INTO responsaveis_alunos (aluno_id, responsavel_id, parentesco, created_at, updated_at) VALUES (:aluno_id, :resp_id, :parentesco, NOW(), NOW())");
            $stmtInsert->execute([
                'aluno_id' => $alunoId,
                'resp_id' => $respId,
                'parentesco' => $parentesco
            ]);
        }

        session()->flash('success', 'Responsável vinculado ao aluno com sucesso!');
        return Response::makeRedirect('/dashboard');
    }

    public function storeUsuario()
    {
        $user = session()->get('user');
        if (!$user || ($user['perfil'] ?? '') !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $dto = new RegisterDTO($_POST);
        
        // As AuthService validates, it might throw or exit on fail, but let's assume it works via fail_validation() returning back
        $authService = new AuthService();
        // Since we removed 'senha_confirmacao' from the frontend form, we should mock it for the DTO if it's missing.
        if (!isset($_POST['senha_confirmacao'])) {
            $_POST['senha_confirmacao'] = $_POST['senha'] ?? '';
            $dto = new RegisterDTO($_POST);
        }

        $authService->registrar($dto);

        session()->flash('success', 'Usuário criado com sucesso!');
        return Response::makeRedirect('/dashboard');
    }
}
