<?php $this->layout('layouts/app', ['title' => 'Gestão de Alunos - Guardian']); ?>

<?php $this->section('content'); ?>
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-5 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Alunos</h2>
            <p class="text-slate-600 mt-1">Gerencie os alunos cadastrados e seus vínculos com responsáveis e turmas.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna da Esquerda: Ações -->
        <div class="space-y-6">
            <!-- Cadastrar Novo Aluno -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Cadastrar Novo Aluno</h3>
                <form action="/secretaria/alunos" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_aluno">Nome Completo</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="nome_aluno" type="text" name="nome" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="data_nasc_aluno">Data de Nascimento</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="data_nasc_aluno" type="date" name="data_nascimento">
                    </div>
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-200">
                        Salvar Aluno
                    </button>
                </form>
            </div>

            <!-- Vincular à Turma -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Matricular em Turma</h3>
                <form action="/secretaria/vincular-turma" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="aluno_v_turma">Aluno</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="aluno_v_turma" name="aluno_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($alunos_detalhes as $ad): ?>
                                <option value="<?= $ad['model']->id ?>"><?= htmlspecialchars($ad['model']->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="turma_v_turma">Turma</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="turma_v_turma" name="turma_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?= $turma->id ?>"><?= htmlspecialchars($turma->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-200">
                        Vincular
                    </button>
                </form>
            </div>

            <!-- Vincular ao Responsável -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Vincular Responsável</h3>
                <form action="/secretaria/vincular-responsavel" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="aluno_v_resp">Aluno</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="aluno_v_resp" name="aluno_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($alunos_detalhes as $ad): ?>
                                <option value="<?= $ad['model']->id ?>"><?= htmlspecialchars($ad['model']->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="resp_v_resp">Responsável Cadastrado</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="resp_v_resp" name="responsavel_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($responsaveis_disponiveis as $resp): ?>
                                <option value="<?= $resp->id ?>"><?= htmlspecialchars($resp->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="parentesco">Parentesco</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="parentesco" type="text" name="parentesco" placeholder="Ex: Pai, Mãe" required>
                    </div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-200">
                        Vincular
                    </button>
                </form>
            </div>
        </div>

        <!-- Coluna da Direita: Lista de Alunos -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-4">Lista de Alunos</h3>
                <?php if (empty($alunos_detalhes)): ?>
                    <p class="text-sm text-slate-500">Nenhum aluno cadastrado no sistema.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold text-slate-500 uppercase tracking-widest">
                                    <th class="py-3 px-4">Aluno</th>
                                    <th class="py-3 px-4">Turma</th>
                                    <th class="py-3 px-4">Contatos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php foreach ($alunos_detalhes as $ad): ?>
                                    <tr>
                                        <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($ad['model']->nome) ?></td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold">
                                                <?= htmlspecialchars($ad['turma']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <?php if (empty($ad['responsaveis'])): ?>
                                                <span class="text-xs text-amber-600 italic">Sem responsável</span>
                                            <?php else: ?>
                                                <div class="space-y-1 text-xs">
                                                    <?php foreach ($ad['responsaveis'] as $resp): ?>
                                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                                            <p class="font-semibold text-slate-800"><?= htmlspecialchars($resp->nome) ?> (<?= htmlspecialchars($resp->parentesco) ?>)</p>
                                                            <p class="text-slate-500"><?= htmlspecialchars($resp->email) ?></p>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
<?php $this->endSection(); ?>
