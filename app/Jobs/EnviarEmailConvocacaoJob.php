<?php

namespace App\Jobs;

use Core\Queue\Job;
use Core\Mail\MailManager;

class EnviarEmailConvocacaoJob extends Job
{
    /**
     * Define o número de tentativas se o job falhar.
     */
    public int $tries = 3;

    /**
     * O tempo de espera (segundos) entre as tentativas.
     */
    public int $backoff = 60;

    public string $alunoNome;
    public array $emails;

    public function __construct(string $alunoNome, array $emails)
    {
        $this->alunoNome = $alunoNome;
        $this->emails = $emails;
    }

    /**
     * Local onde a mágica acontece.
     */
    public function handle(): void
    {
        $subject = "Convocação Escolar - Aluno: " . $this->alunoNome;
        
        $body = "<h2>Convocação de Responsáveis</h2>";
        $body .= "<p>Prezado(a) responsável,</p>";
        $body .= "<p>O aluno <strong>" . htmlspecialchars($this->alunoNome) . "</strong> atingiu o limite de 3 ocorrências aprovadas no sistema escolar.</p>";
        $body .= "<p>Solicitamos o comparecimento dos responsáveis à escola para reunião de alinhamento pedagógico e disciplinar.</p>";
        $body .= "<br><p>Atenciosamente,<br>Direção e Secretaria Escolar</p>";

        foreach ($this->emails as $email) {
            MailManager::send($email, $subject, $body);
        }
    }
}
