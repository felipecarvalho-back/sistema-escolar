<?php

namespace App\Database\Seeders;

class SistemaOcorrenciasSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $db = \Core\Database\Connection::getInstance();

        // Limpar dados anteriores
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $db->exec("TRUNCATE TABLE ocorrencias");
        $db->exec("TRUNCATE TABLE alunos_turmas");
        $db->exec("TRUNCATE TABLE responsaveis_alunos");
        $db->exec("TRUNCATE TABLE turmas");
        $db->exec("TRUNCATE TABLE usuarios");
        $db->exec("TRUNCATE TABLE alunos");
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");

        $usuarioModel = new \App\Models\Usuario();
        
        // 1. Criar Usuário (Apenas a Secretaria)
        $secId = $usuarioModel->insert([
            'nome' => 'Secretária Ana',
            'email' => 'secretaria@escola.com',
            'senha' => password_hash('senha123', PASSWORD_DEFAULT),
            'perfil' => 'secretaria'
        ]);
    }
}
