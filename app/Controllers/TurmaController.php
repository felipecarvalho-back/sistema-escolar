<?php

namespace App\Controllers;

use Core\Http\Controller;
use Core\Http\Response;
use App\Services\TurmaService;
use App\Models\Usuario;

class TurmaController extends Controller
{
    private TurmaService $turmaService;

    public function __construct()
    {
        $this->turmaService = new TurmaService();
    }

    public function index()
    {
        $user = session()->get('user');
        if (!$user) {
            return Response::makeRedirect('/login');
        }

        if ($user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $turmas = $this->turmaService->getAllTurmas();
        $professores = (new Usuario())->where('perfil', '=', 'professor')->get();

        return view('turmas/index', [
            'user' => $user,
            'turmas' => $turmas,
            'professores' => $professores
        ]);
    }

    public function store()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $nome = trim((string) request()->get('nome'));
        $professorCoordenadorId = (int) request()->get('professor_coordenador_id');

        if (!$nome) {
            fail_validation(['nome' => 'O nome da turma é obrigatório.']);
        }

        $this->turmaService->criarTurma($nome, $professorCoordenadorId);

        session()->flash('success', 'Turma criada com sucesso!');
        return Response::makeRedirect('/turmas');
    }
}
