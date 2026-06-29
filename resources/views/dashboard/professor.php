<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Coluna Principal (Lançar Ocorrência e Minhas Aberturas) -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Formulário de Nova Ocorrência -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative">
            <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center space-x-2">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">📝</span>
                <span>Lançar Nova Ocorrência</span>
            </h2>

            <form action="/ocorrencias" method="POST" class="space-y-4">
                <?= csrf_field() ?? '' ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 text-sm font-semibold mb-2" for="aluno_id">Aluno</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="aluno_id" name="aluno_id" required>
                            <option value="">Selecione o Aluno...</option>
                            <?php foreach ($alunos as $aluno): ?>
                                <option value="<?= $aluno->id ?>"><?= htmlspecialchars($aluno->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-sm font-semibold mb-2" for="turma_id">Turma / Sala</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="turma_id" name="turma_id" required>
                            <option value="">Selecione a Turma...</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?= $turma->id ?>"><?= htmlspecialchars($turma->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 text-sm font-semibold mb-2" for="descricao">Fato / Descrição da Ocorrência</label>
                    <textarea class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 h-32" id="descricao" name="descricao" placeholder="Descreva os fatos detalhadamente..." required></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition duration-300">
                    Gravar Ocorrência
                </button>
            </form>
        </div>

        <!-- Minhas Ocorrências Abertas -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Ocorrências que você abriu</h3>
            <?php if (empty($minhas_ocorrencias)): ?>
                <p class="text-sm text-slate-500">Você ainda não registrou nenhuma ocorrência no sistema.</p>
            <?php else: ?>
                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <?php foreach ($minhas_ocorrencias as $ocorr): ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex justify-between items-start gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-slate-900"><?= htmlspecialchars($ocorr['aluno_name'] ?? $ocorr['aluno_nome']) ?></span>
                                    <span class="text-xs text-slate-500">(Turma: <?= htmlspecialchars($ocorr['turma_name'] ?? $ocorr['turma_nome']) ?>)</span>
                                </div>
                                <p class="text-xs text-slate-700"><?= htmlspecialchars($ocorr['descricao']) ?></p>
                                <span class="text-[10px] text-slate-400 block"><?= date('d/m/Y H:i', strtotime($ocorr['created_at'])) ?></span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full uppercase border 
                                    <?= $ocorr['status'] === 'aprovada' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : ($ocorr['status'] === 'pendente' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-rose-50 text-rose-600 border-rose-200') ?>">
                                    <?= htmlspecialchars($ocorr['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna Lateral (Coordenação de Turma e Aprovações) -->
    <div class="space-y-8">
        <!-- Turmas Coordenadas -->
        <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-6 shadow-sm border border-indigo-100">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Coordenação de Turmas</h3>
            <p class="text-xs text-slate-600 mb-4">Você é o coordenador responsável pelas seguintes turmas:</p>
            <?php if (empty($turmas_coordenadas)): ?>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-500">Nenhuma sala vinculada</span>
            <?php else: ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($turmas_coordenadas as $tc): ?>
                        <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-indigo-100 text-indigo-700 border border-indigo-200"><?= htmlspecialchars($tc->nome) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ocorrências Pendentes de Aprovação -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Aprovações Pendentes (Coordenador)</h3>
            <?php if (empty($ocorrencias_pendentes)): ?>
                <p class="text-xs text-slate-500">Nenhuma ocorrência de suas turmas necessita de aprovação.</p>
            <?php else: ?>
                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                    <?php foreach ($ocorrencias_pendentes as $ocorr): ?>
                        <div class="p-4 rounded-xl bg-white border border-slate-200 space-y-3 shadow-sm">
                            <div class="text-xs space-y-1">
                                <p class="font-bold text-slate-900"><?= htmlspecialchars($ocorr['aluno_name'] ?? $ocorr['aluno_nome']) ?></p>
                                <p class="text-slate-600">Turma: <?= htmlspecialchars($ocorr['turma_name'] ?? $ocorr['turma_nome']) ?></p>
                                <p class="text-slate-500">Autor: Prof. <?= htmlspecialchars($ocorr['autor_nome'] ?? $ocorr['autor_name'] ?? '') ?></p>
                            </div>
                            <p class="text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-slate-700"><?= htmlspecialchars($ocorr['descricao']) ?></p>
                            <div class="flex gap-2">
                                <form action="/ocorrencias/<?= $ocorr['id'] ?>/aprovar" method="POST" class="flex-grow">
                                    <?= csrf_field() ?? '' ?>
                                    <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition duration-200">
                                        Aprovar
                                    </button>
                                </form>
                                <form action="/ocorrencias/<?= $ocorr['id'] ?>/rejeitar" method="POST" class="flex-grow">
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
