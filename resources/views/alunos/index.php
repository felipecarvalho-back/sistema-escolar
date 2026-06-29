<?php $this->layout('layouts/app', ['title' => 'Gestão de Alunos - Guardian']); ?>

<?php $this->section('content'); ?>
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-5">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Alunos</h2>
        <p class="text-slate-600 mt-1">Gerencie os alunos cadastrados e seus vínculos com responsáveis e turmas.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna da Esquerda: Ações -->
        <div class="space-y-6">
            <!-- Cadastrar Novo Aluno -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center space-x-2">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">👶</span>
                    <span>Novo Aluno</span>
                </h3>
                <form action="/secretaria/alunos" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_aluno">Nome Completo</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="nome_aluno" type="text" name="nome" placeholder="Ex: Lucas Silva" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="data_nasc_aluno">Data de Nascimento</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="data_nasc_aluno" type="date" name="data_nascimento">
                    </div>
                    <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition duration-200">
                        Salvar Aluno
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
                                    <th class="py-3 px-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php foreach ($alunos_detalhes as $ad): ?>
                                    <tr>
                                        <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($ad['model']->nome) ?></td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold">
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
                                        <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                            <?php $hasTurma = ($ad['turma'] !== 'Sem turma'); ?>
                                            <button onclick="openMatricularModal(<?= $ad['model']->id ?>, '<?= htmlspecialchars($ad['model']->nome, ENT_QUOTES) ?>')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer">
                                                <?= $hasTurma ? 'Alterar Turma' : 'Matricular' ?>
                                            </button>
                                            <button onclick="openVincularModal(<?= $ad['model']->id ?>, '<?= htmlspecialchars($ad['model']->nome, ENT_QUOTES) ?>')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100 transition cursor-pointer">
                                                Responsável
                                            </button>
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

<!-- Modal 1: Matricular em Turma -->
<div id="modalMatricular" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-lg border border-slate-200 transform scale-95 transition-all duration-300">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Matricular Aluno</h3>
        <p class="text-xs text-slate-500 mb-4">Selecione a turma para o aluno: <span id="matricular_aluno_nome" class="font-semibold text-slate-800"></span></p>
        
        <form action="/secretaria/vincular-turma" method="POST" class="space-y-4">
            <?= csrf_field() ?? '' ?>
            <input type="hidden" name="aluno_id" id="matricular_aluno_id">
            <div>
                <label class="block text-slate-700 text-xs font-semibold mb-1" for="turma_id_modal">Selecione a Turma</label>
                <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" name="turma_id" id="turma_id_modal" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?= $turma->id ?>"><?= htmlspecialchars($turma->nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="closeMatricularModal()" class="px-4 py-2 rounded-xl text-xs font-semibold border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-sm cursor-pointer">
                    Confirmar Matrícula
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Vincular Responsável -->
<div id="modalVincular" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-lg border border-slate-200 transform scale-95 transition-all duration-300">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Vincular Responsável</h3>
        <p class="text-xs text-slate-500 mb-4">Defina o responsável pelo aluno: <span id="vincular_aluno_nome" class="font-semibold text-slate-800"></span></p>
        
        <form action="/secretaria/vincular-responsavel" method="POST" class="space-y-4">
            <?= csrf_field() ?? '' ?>
            <input type="hidden" name="aluno_id" id="vincular_aluno_id">
            <div>
                <label class="block text-slate-700 text-xs font-semibold mb-1" for="responsavel_id_modal">Responsável Cadastrado</label>
                <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" name="responsavel_id" id="responsavel_id_modal" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($responsaveis_disponiveis as $resp): ?>
                        <option value="<?= $resp->id ?>"><?= htmlspecialchars($resp->nome) ?> (<?= htmlspecialchars($resp->email) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-slate-700 text-xs font-semibold mb-1" for="parentesco_modal">Parentesco</label>
                <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" type="text" name="parentesco" id="parentesco_modal" placeholder="Ex: Pai, Mãe, Tio" required>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="closeVincularModal()" class="px-4 py-2 rounded-xl text-xs font-semibold border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-sm cursor-pointer">
                    Salvar Vínculo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openMatricularModal(alunoId, alunoNome) {
    document.getElementById('matricular_aluno_id').value = alunoId;
    document.getElementById('matricular_aluno_nome').innerText = alunoNome;
    const modal = document.getElementById('modalMatricular');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.firstElementChild.classList.remove('scale-95');
    }, 10);
}

function closeMatricularModal() {
    const modal = document.getElementById('modalMatricular');
    modal.firstElementChild.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 150);
}

function openVincularModal(alunoId, alunoNome) {
    document.getElementById('vincular_aluno_id').value = alunoId;
    document.getElementById('vincular_aluno_nome').innerText = alunoNome;
    const modal = document.getElementById('modalVincular');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.firstElementChild.classList.remove('scale-95');
    }, 10);
}

function closeVincularModal() {
    const modal = document.getElementById('modalVincular');
    modal.firstElementChild.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 150);
}
</script>
<?php $this->endSection(); ?>
