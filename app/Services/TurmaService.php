<?php

namespace App\Services;

use App\Models\Turma;

class TurmaService
{
    /**
     * Retorna todas as turmas.
     */
    public function getAllTurmas(): array
    {
        return (new Turma())->all();
    }

    /**
     * Busca as turmas coordenadas por um professor específico.
     */
    public function getTurmasCoordenadas(int $professorId): array
    {
        return (new Turma())->where('professor_coordenador_id', '=', $professorId)->get();
    }

    /**
     * Cadastra uma nova turma.
     */
    public function criarTurma(string $nome, ?int $professorCoordenadorId = null): bool
    {
        $turma = new Turma();
        $turma->nome = $nome;
        $turma->professor_coordenador_id = $professorCoordenadorId ?: null;
        return $turma->save();
    }
}
