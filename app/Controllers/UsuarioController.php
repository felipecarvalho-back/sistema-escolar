<?php

namespace App\Controllers;

use Core\Http\Controller;
use Core\Http\Response;
use App\Models\Usuario;
use App\DTOs\Auth\RegisterDTO;
use App\Services\AuthService;

class UsuarioController extends Controller
{
    public function index()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        $usuarios = (new Usuario())->all();

        return view('usuarios/index', [
            'user' => $user,
            'usuarios' => $usuarios
        ]);
    }

    public function store()
    {
        $user = session()->get('user');
        if (!$user || $user['perfil'] !== 'secretaria') {
            return Response::makeRedirect('/dashboard');
        }

        if (!isset($_POST['senha_confirmacao'])) {
            $_POST['senha_confirmacao'] = $_POST['senha'] ?? '';
        }

        $dto = new RegisterDTO($_POST);
        $authService = new AuthService();
        $authService->registrar($dto);

        session()->flash('success', 'Usuário cadastrado com sucesso!');
        return Response::makeRedirect('/usuarios');
    }
}
