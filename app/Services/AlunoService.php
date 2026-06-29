<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Ocorrencia;

class AlunoService
{
    /**
     * Retorna alunos vinculados a um responsável.
     */
    public function getAlunosPorResponsavel(int $responsavelId): array
    {
        $alunos = (new Aluno())
            ->select('alunos.*, responsaveis_alunos.parentesco')
            ->join('responsaveis_alunos', 'responsaveis_alunos.aluno_id = alunos.id')
            ->where('responsaveis_alunos.responsavel_id', '=', $responsavelId)
            ->get();

        $resultado = [];
        foreach ($alunos as $aluno) {
            // Buscar ocorrências aprovadas do aluno usando ORM
            $ocorrencias = (new Ocorrencia())
                ->with(['turma', 'autor'])
                ->where('aluno_id', '=', $aluno->id)
                ->where('status', '=', 'aprovada')
                ->orderBy('created_at', 'DESC')
                ->get();

            $resultado[] = [
                'model' => $aluno,
                'ocorrencias' => $ocorrencias,
                'parentesco' => $aluno->parentesco ?? null
            ];
        }

        return $resultado;
    }

    /**
     * Retorna todos os alunos com detalhes adicionais (Turma, Responsáveis, Ocorrências Aprovadas).
     */
    public function getAllAlunosComDetalhes(): array
    {
        $alunos = (new Aluno())->all();
        $alunosComDetalhes = [];
        
        foreach ($alunos as $aluno) {
            // Turma atual usando ORM Join
            $turma = (new Turma())
                ->select('turmas.nome')
                ->join('alunos_turmas', 'alunos_turmas.turma_id = turmas.id')
                ->where('alunos_turmas.aluno_id', '=', $aluno->id)
                ->get();
                
            $turmaNome = !empty($turma) ? $turma[0]->nome : 'Sem turma';

            // Ocorrências aprovadas
            $countOcorrencias = (new Ocorrencia())
                ->where('aluno_id', '=', $aluno->id)
                ->where('status', '=', 'aprovada')
                ->count();

            // Responsáveis
            $responsaveis = $aluno->responsaveis();

            $alunosComDetalhes[] = [
                'model' => $aluno,
                'turma' => $turmaNome,
                'ocorrencias_count' => $countOcorrencias,
                'responsaveis' => $responsaveis
            ];
        }
        return $alunosComDetalhes;
    }

    /**
     * Cadastra um novo aluno
     */
    public function criarAluno(string $nome, string $dataNascimento): bool
    {
        $aluno = new Aluno();
        $aluno->nome = $nome;
        $aluno->data_nascimento = $dataNascimento ?: null;
        return $aluno->save();
    }

    /**
     * Vincula o aluno a uma turma
     */
    public function vincularTurma(int $alunoId, int $turmaId): void
    {
        $db = \Core\Database\Database::getInstance()->getConnection();

        // O framework não possui Model para a tabela pivô alunos_turmas pronta, 
        // então aqui usamos Query bruta apenas para gerenciar o relacionamento puro Many-to-Many
        $stmtDel = $db->prepare("DELETE FROM alunos_turmas WHERE aluno_id = :aluno_id");
        $stmtDel->execute(['aluno_id' => $alunoId]);

        $stmtIns = $db->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, created_at, updated_at) VALUES (:aluno_id, :turma_id, NOW(), NOW())");
        $stmtIns->execute(['aluno_id' => $alunoId, 'turma_id' => $turmaId]);
    }

    /**
     * Vincula o aluno a um responsável
     */
    public function vincularResponsavel(int $alunoId, int $responsavelId, string $parentesco): void
    {
        $db = \Core\Database\Database::getInstance()->getConnection();

        $stmtCheck = $db->prepare("SELECT 1 FROM responsaveis_alunos WHERE aluno_id = :aluno_id AND responsavel_id = :resp_id");
        $stmtCheck->execute(['aluno_id' => $alunoId, 'resp_id' => $responsavelId]);
        
        if (!$stmtCheck->fetch()) {
            $stmtInsert = $db->prepare("INSERT INTO responsaveis_alunos (aluno_id, responsavel_id, parentesco, created_at, updated_at) VALUES (:aluno_id, :resp_id, :parentesco, NOW(), NOW())");
            $stmtInsert->execute([
                'aluno_id' => $alunoId,
                'resp_id' => $responsavelId,
                'parentesco' => $parentesco
            ]);
        }
    }
}
