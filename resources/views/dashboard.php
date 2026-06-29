<?php $this->layout('layouts/app', ['title' => 'Painel - Guardian']); ?>

<?php $this->section('content'); ?>
    <?php if ($user['perfil'] === 'responsavel'): ?>
        <?= $this->include('dashboard/responsavel', [
            'user' => $user,
            'alunos' => $alunos
        ]) ?>
    <?php elseif ($user['perfil'] === 'professor'): ?>
        <?= $this->include('dashboard/professor', [
            'user' => $user,
            'alunos' => $alunos,
            'turmas' => $turmas,
            'minhas_ocorrencias' => $minhas_ocorrencias,
            'turmas_coordenadas' => $turmas_coordenadas,
            'ocorrencias_pendentes' => $ocorrencias_pendentes
        ]) ?>
    <?php elseif ($user['perfil'] === 'secretaria'): ?>
        <?= $this->include('dashboard/secretaria', [
            'user' => $user,
            'turmas' => $turmas,
            'ocorrencias_pendentes' => $ocorrencias_pendentes,
            'alunos_detalhes' => $alunos_detalhes,
            'responsaveis_disponiveis' => $responsaveis_disponiveis,
            'professores' => $professores
        ]) ?>
    <?php endif; ?>
<?php $this->endSection(); ?>
