<?php

namespace App\Services;

use App\Models\Ocorrencia;
use App\Models\Turma;
use App\Models\Aluno;
use App\Jobs\EnviarEmailConvocacaoJob;
use Core\Queue\QueueManager;

class OcorrenciaService
{
    /**
     * Lista ocorrências registradas por um professor usando ORM Eager Loading.
     */
    public function getOcorrenciasPorAutor(int $autorId): array
    {
        return (new Ocorrencia())
            ->with(['aluno', 'turma'])
            ->where('autor_id', '=', $autorId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Lista ocorrências pendentes globais (para Secretaria) usando ORM Eager Loading.
     */
    public function getOcorrenciasPendentesGlobais(): array
    {
        return (new Ocorrencia())
            ->with(['aluno', 'turma', 'autor'])
            ->where('status', '=', 'pendente')
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    /**
     * Lista ocorrências pendentes apenas das turmas que o professor coordena usando ORM.
     */
    public function getOcorrenciasPendentesCoordenador(array $turmasCoordenadas): array
    {
        if (empty($turmasCoordenadas)) {
            return [];
        }

        $turmasIds = array_map(fn($t) => $t->id, $turmasCoordenadas);
        
        return (new Ocorrencia())
            ->with(['aluno', 'turma', 'autor'])
            ->where('status', '=', 'pendente')
            ->whereIn('turma_id', $turmasIds)
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    /**
     * Salva uma nova ocorrência
     */
    public function registrarOcorrencia(int $alunoId, int $turmaId, string $descricao, int $autorId, string $perfilAutor): string
    {
        $status = 'pendente';
        $aprovadoPorId = null;

        if ($perfilAutor === 'secretaria') {
            $status = 'aprovada';
            $aprovadoPorId = $autorId;
        } elseif ($perfilAutor === 'professor') {
            $turma = (new Turma())->find($turmaId);
            if ($turma && (int)$turma->professor_coordenador_id === (int)$autorId) {
                $status = 'aprovada';
                $aprovadoPorId = $autorId;
            }
        }

        $ocorrencia = new Ocorrencia();
        $ocorrencia->aluno_id = $alunoId;
        $ocorrencia->turma_id = $turmaId;
        $ocorrencia->autor_id = $autorId;
        $ocorrencia->descricao = $descricao;
        $ocorrencia->status = $status;
        $ocorrencia->aprovado_por_id = $aprovadoPorId;
        $ocorrencia->save();

        if ($status === 'aprovada') {
            $this->verificarELancarConvocacao($alunoId);
        }

        return $status;
    }

    /**
     * Aprova uma ocorrência. Retorna true se sucesso.
     */
    public function aprovarOcorrencia(int $id, array $user): bool
    {
        $ocorrencia = (new Ocorrencia())->find($id);

        if (!$ocorrencia) {
            return false;
        }

        $turma = (new Turma())->find($ocorrencia->turma_id);

        if ($user['perfil'] !== 'secretaria' && (!$turma || (int)$turma->professor_coordenador_id !== (int)$user['id'])) {
            throw new \Exception('Acesso negado para esta ação.');
        }

        $ocorrencia->status = 'aprovada';
        $ocorrencia->aprovado_por_id = $user['id'];
        $ocorrencia->save();

        $this->verificarELancarConvocacao((int)$ocorrencia->aluno_id);
        
        return true;
    }

    /**
     * Rejeita uma ocorrência. Retorna true se sucesso.
     */
    public function rejeitarOcorrencia(int $id, array $user): bool
    {
        $ocorrencia = (new Ocorrencia())->find($id);

        if (!$ocorrencia) {
            return false;
        }

        $turma = (new Turma())->find($ocorrencia->turma_id);

        if ($user['perfil'] !== 'secretaria' && (!$turma || (int)$turma->professor_coordenador_id !== (int)$user['id'])) {
            throw new \Exception('Acesso negado para esta ação.');
        }

        $ocorrencia->status = 'rejeitada';
        $ocorrencia->save();

        return true;
    }

    /**
     * Verifica as ocorrências do aluno e despacha o job de convocação caso atinja 3
     */
    private function verificarELancarConvocacao(int $alunoId): void
    {
        $aluno = (new Aluno())->find($alunoId);
        if (!$aluno) {
            return;
        }

        $count = (new Ocorrencia())
            ->where('aluno_id', '=', $alunoId)
            ->where('status', '=', 'aprovada')
            ->count();

        if ($count === 3) {
            $responsaveis = $aluno->responsaveis();
            $emails = array_map(fn($r) => $r->email, $responsaveis);

            if (!empty($emails)) {
                // Despacha o Job para a fila
                QueueManager::push(new EnviarEmailConvocacaoJob($aluno->nome, $emails));
            }
        }
    }
}
