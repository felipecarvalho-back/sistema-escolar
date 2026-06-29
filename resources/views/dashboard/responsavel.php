<div class="space-y-8">
    <div class="border-b border-slate-200 pb-5">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Seus Filhos / Dependentes</h2>
        <p class="text-slate-600 mt-1">Abaixo estão listadas as ocorrências registradas e aprovadas para seus filhos.</p>
    </div>

    <?php if (empty($alunos)): ?>
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-slate-200">
            <p class="text-slate-600 text-lg">Nenhum aluno está associado ao seu cadastro de responsável no momento.</p>
            <p class="text-slate-500 text-sm mt-2">Por favor, entre em contato com a secretaria da escola.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($alunos as $item): ?>
                <?php $aluno = $item['model']; ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($aluno->nome) ?></h3>
                                <span class="text-xs text-indigo-600 font-semibold uppercase tracking-wider block mt-1"><?= htmlspecialchars($item['parentesco']) ?></span>
                            </div>
                            <div class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-right">
                                <span class="text-xs text-slate-500 block uppercase font-bold tracking-widest">Ocorrências</span>
                                <span class="text-2xl font-black text-slate-900"><?= count($item['ocorrencias']) ?></span>
                            </div>
                        </div>

                        <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-widest mb-3">Histórico de Ocorrências</h4>
                        <?php if (empty($item['ocorrencias'])): ?>
                            <p class="text-sm text-emerald-600 font-medium">Nenhuma ocorrência registrada! Excelente comportamento.</p>
                        <?php else: ?>
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                <?php foreach ($item['ocorrencias'] as $ocorr): ?>
                                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                        <div class="flex justify-between items-center mb-1 text-slate-500 font-semibold">
                                            <span>Turma: <?= htmlspecialchars($ocorr['turma_name'] ?? $ocorr['turma_nome']) ?></span>
                                            <span><?= date('d/m/Y H:i', strtotime($ocorr['created_at'])) ?></span>
                                        </div>
                                        <p class="text-slate-800 text-sm mt-1"><?= htmlspecialchars($ocorr['descricao']) ?></p>
                                        <span class="text-[10px] block mt-2 text-indigo-600">Por: Prof. <?= htmlspecialchars($ocorr['autor_nome'] ?? $ocorr['autor_name'] ?? '') ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
