<?php

namespace App\Middleware;

use Closure;
use Core\Contracts\MiddlewareInterface;
use Core\Http\Request;
use Core\Http\Response;

class VerificarPerfilMiddleware implements MiddlewareInterface
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$perfis
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$perfis)
    {
        $user = session()->get('user');
        
        if (!$user) {
            if ($request->isHtmx()) {
                return response()->hxRedirect('/login');
            }
            if ($request->isAjax()) {
                return response()->json(['error' => 'Sessão expirada.'], 401);
            }
            return Response::makeRedirect('/login');
        }

        $perfilUsuario = $user['perfil'] ?? '';

        // Se o perfil do usuário não estiver na lista de perfis permitidos
        if (!in_array($perfilUsuario, $perfis)) {
            if ($request->isAjax() || $request->isHtmx()) {
                return response()->json(['error' => 'Acesso negado para seu perfil.'], 403);
            }
            return response('Acesso negado para o seu perfil.', 403);
        }

        return $next($request);
    }
}
