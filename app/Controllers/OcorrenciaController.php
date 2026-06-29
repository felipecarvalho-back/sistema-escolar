<?php

namespace App\Controllers;

use Core\Http\Controller;
use Core\Http\Response;
use App\Services\OcorrenciaService;
use App\Models\Aluno;
use App\Models\Turma;
use App\Services\TurmaService;

class OcorrenciaController extends Controller
{
    private OcorrenciaService $ocorrenciaService;
    private TurmaService $turmaService;

    public function __construct()
    {
        $this->ocorrenciaService = new OcorrenciaService();
        $this->turmaService = new TurmaService();
    }

    public function index()
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        $perfil = $user['perfil'];
        $data = ['user' => $user];

        if ($perfil === 'professor') {
            $data['alunos'] = (new Aluno())->all();
            $data['turmas'] = (new Turma())->all();
            $data['minhas_ocorrencias'] = $this->ocorrenciaService->getOcorrenciasPorAutor($user['id']);
            
            $turmasCoordenadas = $this->turmaService->getTurmasCoordenadas($user['id']);
            $data['turmas_coordenadas'] = $turmasCoordenadas;
            $data['ocorrencias_pendentes'] = $this->ocorrenciaService->getOcorrenciasPendentesCoordenador($turmasCoordenadas);
        } elseif ($perfil === 'secretaria') {
            $data['ocorrencias_pendentes'] = $this->ocorrenciaService->getOcorrenciasPendentesGlobais();
        } elseif ($perfil === 'responsavel') {
            return Response::makeRedirect('/dashboard'); // Responsável vê no dashboard
        }

        return view('ocorrencias/index', $data);
    }

    public function store()
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        $alunoId = (int) request()->get('aluno_id');
        $turmaId = (int) request()->get('turma_id');
        $descricao = trim((string) request()->get('descricao'));

        if (!$alunoId || !$turmaId || !$descricao) {
            fail_validation(['descricao' => 'Todos os campos são obrigatórios.']);
        }

        $status = $this->ocorrenciaService->registrarOcorrencia(
            $alunoId, 
            $turmaId, 
            $descricao, 
            $user['id'], 
            $user['perfil']
        );

        session()->flash('success', 'Ocorrência registrada com sucesso' . ($status === 'pendente' ? ' (aguardando aprovação).' : '.'));
        
        return Response::makeRedirect('/ocorrencias');
    }

    public function approve(int $id)
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        try {
            if ($this->ocorrenciaService->aprovarOcorrencia($id, $user)) {
                session()->flash('success', 'Ocorrência aprovada com sucesso!');
            } else {
                throw new \Exception('Ocorrência não encontrada.');
            }
        } catch (\Exception $e) {
            throw new \Core\Exceptions\HttpException($e->getMessage(), 403);
        }

        return Response::makeRedirect('/ocorrencias');
    }

    public function reject(int $id)
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        try {
            if ($this->ocorrenciaService->rejeitarOcorrencia($id, $user)) {
                session()->flash('success', 'Ocorrência rejeitada.');
            } else {
                throw new \Exception('Ocorrência não encontrada.');
            }
        } catch (\Exception $e) {
            throw new \Core\Exceptions\HttpException($e->getMessage(), 403);
        }

        return Response::makeRedirect('/ocorrencias');
    }
}
