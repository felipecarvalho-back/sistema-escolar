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
        
        // 1. Criar Usuários
        $secId = $usuarioModel->insert([
            'nome' => 'Secretária Ana',
            'email' => 'secretaria@escola.com',
            'senha' => password_hash('senha123', PASSWORD_DEFAULT),
            'perfil' => 'secretaria'
        ]);

        $coordId = $usuarioModel->insert([
            'nome' => 'Prof. Carlos Coordenador',
            'email' => 'coordenador@escola.com',
            'senha' => password_hash('senha123', PASSWORD_DEFAULT),
            'perfil' => 'professor'
        ]);

        $profId = $usuarioModel->insert([
            'nome' => 'Prof. Marcos Comum',
            'email' => 'professor@escola.com',
            'senha' => password_hash('senha123', PASSWORD_DEFAULT),
            'perfil' => 'professor'
        ]);

        $paiId = $usuarioModel->insert([
            'nome' => 'Seu José (Pai)',
            'email' => 'pai@escola.com',
            'senha' => password_hash('senha123', PASSWORD_DEFAULT),
            'perfil' => 'responsavel'
        ]);

        // 2. Criar Turma
        $turmaModel = new \App\Models\Turma();
        $turmaId = $turmaModel->insert([
            'nome' => '3º Ano A - Ensino Médio',
            'professor_coordenador_id' => $coordId
        ]);

        // 3. Criar Aluno
        $alunoModel = new \App\Models\Aluno();
        $alunoId = $alunoModel->insert([
            'nome' => 'Felipe Carvalho',
            'data_nascimento' => '2008-05-15'
        ]);

        // 4. Vincular Aluno à Turma
        $db->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, ano_letivo, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())")
           ->execute([$alunoId, $turmaId, 2026]);

        // 5. Vincular Aluno ao Responsável (Pai)
        $db->prepare("INSERT INTO responsaveis_alunos (aluno_id, responsavel_id, parentesco, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())")
           ->execute([$alunoId, $paiId, 'Pai']);
    }
}
