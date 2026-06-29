<?php

namespace App\Database\Migrations;

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateOcorrenciasTable
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->integer('aluno_id')->unsigned();
            $table->integer('turma_id')->unsigned();
            $table->integer('autor_id')->unsigned();
            $table->text('descricao');
            $table->enum('status', ['pendente', 'aprovada', 'rejeitada']);
            $table->integer('aprovado_por_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('CASCADE');
            $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('CASCADE');
            $table->foreign('autor_id')->references('id')->on('usuarios')->onDelete('CASCADE');
            $table->foreign('aprovado_por_id')->references('id')->on('usuarios')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocorrencias');
    }
}
