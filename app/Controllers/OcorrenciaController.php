<?php

namespace App\Controllers;

use Core\Http\Controller;
use Core\Http\Response;
use App\Models\Ocorrencia;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Usuario;
use App\Jobs\EnviarEmailConvocacaoJob;
use Core\Queue\QueueManager;

class OcorrenciaController extends Controller
{
    /**
     * Salva uma nova ocorrência
     */
    public function store()
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        $autorId = $user['id'];
        $perfilAutor = $user['perfil'];

        $alunoId = (int) request()->get('aluno_id');
        $turmaId = (int) request()->get('turma_id');
        $descricao = trim((string) request()->get('descricao'));

        if (!$alunoId || !$turmaId || !$descricao) {
            fail_validation(['descricao' => 'Todos os campos são obrigatórios.']);
        }

        // Determinar o status
        $status = 'pendente';
        $aprovadoPorId = null;

        if ($perfilAutor === 'secretaria') {
            $status = 'aprovada';
            $aprovadoPorId = $autorId;
        } elseif ($perfilAutor === 'professor') {
            $turma = (new Turma())->find($turmaId);
            if ($turma && (int)$turma->professor_coordenador_id === (int)$autorId) {
                $status = 'aprovada';
                $aprovadoPorId = $autorId;
            }
        }

        $ocorrencia = new Ocorrencia();
        $ocorrencia->insert([
            'aluno_id' => $alunoId,
            'turma_id' => $turmaId,
            'autor_id' => $autorId,
            'descricao' => $descricao,
            'status' => $status,
            'aprovado_por_id' => $aprovadoPorId
        ]);

        if ($status === 'aprovada') {
            $this->verificarELancarConvocacao($alunoId);
        }

        session()->flash('success', 'Ocorrência registrada com sucesso' . ($status === 'pendente' ? ' (aguardando aprovação do coordenador).' : '.'));
        
        return Response::makeRedirect('/dashboard');
    }

    /**
     * Aprova uma ocorrência pendente
     */
    public function approve(int $id)
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        $ocorrenciaModel = new Ocorrencia();
        $ocorrencia = $ocorrenciaModel->find($id);

        if (!$ocorrencia) {
            throw new \Core\Exceptions\HttpException('Ocorrência não encontrada.', 404);
        }

        $turma = (new Turma())->find($ocorrencia->turma_id);

        // Apenas secretaria ou coordenador da turma podem aprovar
        if ($user['perfil'] !== 'secretaria' && (!$turma || (int)$turma->professor_coordenador_id !== (int)$user['id'])) {
            throw new \Core\Exceptions\HttpException('Acesso negado para esta ação.', 403);
        }

        $ocorrenciaModel->update($id, [
            'status' => 'aprovada',
            'aprovado_por_id' => $user['id']
        ]);

        $this->verificarELancarConvocacao((int)$ocorrencia->aluno_id);

        session()->flash('success', 'Ocorrência aprovada com sucesso!');
        return Response::makeRedirect('/dashboard');
    }

    /**
     * Rejeita uma ocorrência pendente
     */
    public function reject(int $id)
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        $ocorrenciaModel = new Ocorrencia();
        $ocorrencia = $ocorrenciaModel->find($id);

        if (!$ocorrencia) {
            throw new \Core\Exceptions\HttpException('Ocorrência não encontrada.', 404);
        }

        $turma = (new Turma())->find($ocorrencia->turma_id);

        // Apenas secretaria ou coordenador da turma podem rejeitar
        if ($user['perfil'] !== 'secretaria' && (!$turma || (int)$turma->professor_coordenador_id !== (int)$user['id'])) {
            throw new \Core\Exceptions\HttpException('Acesso negado para esta ação.', 403);
        }

        $ocorrenciaModel->update($id, [
            'status' => 'rejeitada'
        ]);

        session()->flash('success', 'Ocorrência rejeitada.');
        return Response::makeRedirect('/dashboard');
    }

    /**
     * Verifica as ocorrências do aluno e despacha o job de convocação caso atinja 3
     */
    private function verificarELancarConvocacao(int $alunoId): void
    {
        $aluno = (new Aluno())->find($alunoId);
        if (!$aluno) {
            return;
        }

        $count = (new Ocorrencia())
            ->where('aluno_id', '=', $alunoId)
            ->where('status', '=', 'aprovada')
            ->count();

        if ($count === 3) {
            $responsaveis = $aluno->responsaveis();
            $emails = array_map(fn($r) => $r->email, $responsaveis);

            if (!empty($emails)) {
                // Despacha o Job para a fila
                QueueManager::push(new EnviarEmailConvocacaoJob($aluno->nome, $emails));
            }
        }
    }
}
