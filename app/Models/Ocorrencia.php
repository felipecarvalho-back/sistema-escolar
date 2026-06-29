<?php

namespace App\Models;

use Core\Database\Model;

class Ocorrencia extends Model
{
    protected ?string $table = 'ocorrencias';
    protected array $fillable = [
        'aluno_id',
        'turma_id',
        'autor_id',
        'descricao',
        'status',
        'aprovado_por_id'
    ];

    public function aluno(): Aluno
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    public function turma(): Turma
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    public function autor(): Usuario
    {
        return $this->belongsTo(Usuario::class, 'autor_id');
    }

    public function aprovadoPor(): ?Usuario
    {
        if (!$this->aprovado_por_id) {
            return null;
        }
        return $this->belongsTo(Usuario::class, 'aprovado_por_id');
    }
}
