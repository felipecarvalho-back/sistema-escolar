<?php

namespace App\Models;

use Core\Database\Model;

class Aluno extends Model
{
    protected ?string $table = 'alunos';
    protected array $fillable = ['nome', 'data_nascimento'];

    public function ocorrencias(): array
    {
        return $this->hasMany(Ocorrencia::class, 'aluno_id') ?? [];
    }

    public function responsaveis(): array
    {
        $sql = "SELECT u.*, ra.parentesco FROM usuarios u 
                JOIN responsaveis_alunos ra ON ra.responsavel_id = u.id 
                WHERE ra.aluno_id = :aluno_id";
        $results = $this->query($sql, ['aluno_id' => $this->id]);
        return array_map(function ($data) {
            $u = new Usuario();
            foreach ($data as $key => $value) {
                $u->$key = $value;
            }
            return $u;
        }, $results);
    }

    public function turmas(): array
    {
        $sql = "SELECT t.*, at.ano_letivo FROM turmas t 
                JOIN alunos_turmas at ON at.turma_id = t.id 
                WHERE at.aluno_id = :aluno_id";
        $results = $this->query($sql, ['aluno_id' => $this->id]);
        return array_map(function ($data) {
            $t = new Turma();
            foreach ($data as $key => $value) {
                $t->$key = $value;
            }
            return $t;
        }, $results);
    }
}
