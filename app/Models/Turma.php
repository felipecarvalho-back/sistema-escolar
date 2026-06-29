<?php

namespace App\Models;

use Core\Database\Model;

class Turma extends Model
{
    protected ?string $table = 'turmas';
    protected array $fillable = ['nome', 'professor_coordenador_id'];

    public function coordenador(): ?Usuario
    {
        if (!$this->professor_coordenador_id) {
            return null;
        }
        return $this->belongsTo(Usuario::class, 'professor_coordenador_id');
    }

    public function alunos(): array
    {
        $sql = "SELECT a.* FROM alunos a 
                JOIN alunos_turmas at ON at.aluno_id = a.id 
                WHERE at.turma_id = :turma_id";
        $results = $this->query($sql, ['turma_id' => $this->id]);
        return array_map(function ($data) {
            $a = new Aluno();
            foreach ($data as $key => $value) {
                $a->$key = $value;
            }
            return $a;
        }, $results);
    }
}
