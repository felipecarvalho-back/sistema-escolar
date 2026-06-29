<?php $this->layout('layouts/app', ['title' => 'Gestão de Ocorrências - Guardian']); ?>

<?php $this->section('content'); ?>
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-5">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Ocorrências</h2>
        <p class="text-slate-600 mt-1">
            <?= $user['perfil'] === 'professor' ? 'Registre novas ocorrências e acompanhe aprovações.' : 'Gerencie as aprovações de ocorrências pendentes no sistema.' ?>
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <?php if ($user['perfil'] === 'professor'): ?>
        <!-- Coluna da Esquerda: Ações do Professor -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Lançar Nova Ocorrência -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center space-x-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">📝</span>
                    <span>Lançar Nova Ocorrência</span>
                </h3>

                <form action="/ocorrencias" method="POST" class="space-y-4">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="aluno_id">Aluno</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="aluno_id" name="aluno_id" required>
                            <option value="">Selecione o Aluno...</option>
                            <?php foreach ($alunos as $aluno): ?>
                                <option value="<?= $aluno->id ?>"><?= htmlspecialchars($aluno->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="turma_id">Turma</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="turma_id" name="turma_id" required>
                            <option value="">Selecione a Turma...</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?= $turma->id ?>"><?= htmlspecialchars($turma->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="descricao">Fato / Descrição da Ocorrência</label>
                        <textarea class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 h-24" id="descricao" name="descricao" placeholder="Descreva os fatos detalhadamente..." required></textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition duration-300">
                        Gravar Ocorrência
                    </button>
                </form>
            </div>
            
            <!-- Minhas Ocorrências (Abertas pelo Professor) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-md font-bold text-slate-900 mb-4">Ocorrências que você abriu</h3>
                <?php if (empty($minhas_ocorrencias)): ?>
                    <p class="text-xs text-slate-500">Você não abriu nenhuma ocorrência.</p>
                <?php else: ?>
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                        <?php foreach ($minhas_ocorrencias as $ocorr): ?>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($ocorr->aluno?->nome ?? 'Desconhecido') ?></span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase border 
                                        <?= $ocorr->status === 'aprovada' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : ($ocorr->status === 'pendente' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-rose-50 text-rose-600 border-rose-200') ?>">
                                        <?= htmlspecialchars($ocorr->status) ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2"><?= htmlspecialchars($ocorr->descricao) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Coluna da Direita (ou central se secretaria): Aprovações Pendentes -->
        <div class="<?= $user['perfil'] === 'professor' ? 'lg:col-span-2' : 'lg:col-span-3' ?>">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-4">Aprovações Pendentes</h3>
                <?php if (empty($ocorrencias_pendentes)): ?>
                    <div class="p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-300">
                        <p class="text-sm text-slate-500">Tudo em dia! Nenhuma ocorrência necessita de aprovação no momento.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($ocorrencias_pendentes as $ocorr): ?>
                            <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-3 shadow-sm relative flex flex-col justify-between">
                                <div class="text-xs space-y-1">
                                    <p class="font-bold text-slate-900 text-base mb-2"><?= htmlspecialchars($ocorr->aluno?->nome ?? 'Desconhecido') ?></p>
                                    <p class="text-slate-600"><span class="font-semibold text-slate-500">Turma:</span> <?= htmlspecialchars($ocorr->turma?->nome ?? 'Desconhecida') ?></p>
                                    <p class="text-slate-600"><span class="font-semibold text-slate-500">Autor:</span> Prof. <?= htmlspecialchars($ocorr->autor?->nome ?? 'Desconhecido') ?></p>
                                </div>
                                <div class="text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-slate-700 flex-grow">
                                    <?= htmlspecialchars($ocorr->descricao) ?>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <form action="/ocorrencias/<?= $ocorr->id ?>/aprovar" method="POST" class="flex-grow">
                                        <?= csrf_field() ?? '' ?>
                                        <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition duration-200 shadow-sm">
                                            Aprovar
                                        </button>
                                    </form>
                                    <form action="/ocorrencias/<?= $ocorr->id ?>/rejeitar" method="POST" class="flex-grow">
                                        <?= csrf_field() ?? '' ?>
                                        <button type="submit" class="w-full py-1.5 bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-600 hover:text-white font-bold text-xs rounded-lg transition duration-200">
                                            Rejeitar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
<?php $this->endSection(); ?>
