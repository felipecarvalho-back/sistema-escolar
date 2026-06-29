<?php

namespace App\Controllers;

use Core\Http\Controller;
use Core\Http\Response;
use App\Services\AlunoService;
use App\Models\Usuario;
use App\Models\Turma;

class AlunoController extends Controller
{
    private AlunoService $alunoService;

    public function __construct()
    {
        $this->alunoService = new AlunoService();
    }

    public function index()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $alunosDetalhes = $this->alunoService->getAllAlunosComDetalhes();
        $turmas = (new Turma())->all();
        $responsaveisDisponiveis = (new Usuario())->where('perfil', '=', 'responsavel')->get();

        return view('alunos/index', [
            'user' => $user,
            'alunos_detalhes' => $alunosDetalhes,
            'turmas' => $turmas,
            'responsaveis_disponiveis' => $responsaveisDisponiveis
        ]);
    }

    public function store()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $nome = trim((string) request()->get('nome'));
        $dataNascimento = trim((string) request()->get('data_nascimento'));

        if (!$nome) {
            fail_validation(['nome' => 'O nome do aluno é obrigatório.']);
        }

        $this->alunoService->criarAluno($nome, $dataNascimento);

        session()->flash('success', 'Aluno cadastrado com sucesso!');
        return Response::makeRedirect('/alunos');
    }

    public function vincularTurma()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $alunoId = (int) request()->get('aluno_id');
        $turmaId = (int) request()->get('turma_id');

        if (!$alunoId || !$turmaId) {
            fail_validation(['erro' => 'Selecione o aluno e a turma.']);
        }

        $this->alunoService->vincularTurma($alunoId, $turmaId);

        session()->flash('success', 'Aluno matriculado na turma com sucesso!');
        return Response::makeRedirect('/alunos');
    }

    public function vincularResponsavel()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $alunoId = (int) request()->get('aluno_id');
        $respId = (int) request()->get('responsavel_id');
        $parentesco = trim((string) request()->get('parentesco'));

        if (!$alunoId || !$respId || !$parentesco) {
            fail_validation(['erro' => 'Preencha todos os campos do vínculo.']);
        }

        $this->alunoService->vincularResponsavel($alunoId, $respId, $parentesco);

        session()->flash('success', 'Responsável vinculado ao aluno com sucesso!');
        return Response::makeRedirect('/alunos');
    }
}
