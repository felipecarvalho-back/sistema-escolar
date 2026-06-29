<?php

namespace App\Database\Migrations;

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateResponsaveisAlunosTable
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('responsaveis_alunos', function (Blueprint $table) {
            $table->id();
            $table->integer('responsavel_id')->unsigned();
            $table->integer('aluno_id')->unsigned();
            $table->string('parentesco')->nullable();
            $table->timestamps();

            $table->foreign('responsavel_id')->references('id')->on('usuarios')->onDelete('CASCADE');
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsaveis_alunos');
    }
}
