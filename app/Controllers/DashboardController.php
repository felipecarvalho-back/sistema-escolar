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
use App\Services\OcorrenciaService;
use App\Services\TurmaService;

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

        if ($perfil === 'secretaria') {
            $data['total_alunos'] = (new Aluno())->count();
            $data['total_turmas'] = (new Turma())->count();
            $data['ocorrencias_pendentes'] = (new Ocorrencia())->where('status', '=', 'pendente')->count();
        } elseif ($perfil === 'professor') {
            $turmasCoordenadas = (new TurmaService())->getTurmasCoordenadas($user['id']);
            $turmasIds = array_map(fn($t) => $t->id, $turmasCoordenadas);
            
            $data['total_turmas_coordenadas'] = count($turmasCoordenadas);
            if (!empty($turmasIds)) {
                $data['ocorrencias_pendentes'] = (new Ocorrencia())
                    ->where('status', '=', 'pendente')
                    ->whereIn('turma_id', $turmasIds)
                    ->count();
            } else {
                $data['ocorrencias_pendentes'] = 0;
            }
        } elseif ($perfil === 'responsavel') {
            $alunoService = new \App\Services\AlunoService();
            $alunos = $alunoService->getAlunosPorResponsavel($user['id']);
            $data['alunos'] = $alunos;
            $data['total_filhos'] = count($alunos);
        }

        return view('dashboard/index', $data);
    }

    public function storeUsuario()
    {
        $user = session()->get('user');
        if (!$user || ($user['perfil'] ?? '') !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $dto = new RegisterDTO($_POST);
        
        $authService = new AuthService();
        if (!isset($_POST['senha_confirmacao'])) {
            $_POST['senha_confirmacao'] = $_POST['senha'] ?? '';
            $dto = new RegisterDTO($_POST);
        }

        $authService->registrar($dto);

        session()->flash('success', 'Usuário criado com sucesso!');
        return Response::makeRedirect('/dashboard'); // Ou talvez uma view de usuários
    }
}
