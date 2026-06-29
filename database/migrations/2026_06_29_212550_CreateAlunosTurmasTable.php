<?php

namespace App\Database\Migrations;

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateAlunosTurmasTable
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alunos_turmas', function (Blueprint $table) {
            $table->id();
            $table->integer('aluno_id')->unsigned();
            $table->integer('turma_id')->unsigned();
            $table->integer('ano_letivo');
            $table->timestamps();

            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('CASCADE');
            $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alunos_turmas');
    }
}
