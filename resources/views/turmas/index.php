<?php $this->layout('layouts/app', ['title' => 'Gestão de Turmas - Guardian']); ?>

<?php $this->section('content'); ?>
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-5">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Turmas</h2>
        <p class="text-slate-600 mt-1">Gerencie as salas de aula e seus coordenadores.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna da Esquerda: Ações -->
        <div class="space-y-6">
            <!-- Cadastrar Nova Turma -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Cadastrar Nova Turma</h3>
                <form action="/secretaria/turmas" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_turma">Nome da Sala</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="nome_turma" type="text" name="nome" placeholder="Ex: 1º Ano A" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="professor_coordenador">Professor Coordenador</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="professor_coordenador" name="professor_coordenador_id">
                            <option value="">Selecione o Professor...</option>
                            <?php foreach ($professores as $prof): ?>
                                <option value="<?= $prof->id ?>"><?= htmlspecialchars($prof->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-200">
                        Criar Sala de Aula
                    </button>
                </form>
            </div>
        </div>

        <!-- Coluna da Direita: Lista de Turmas -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-4">Lista de Turmas</h3>
                <?php if (empty($turmas)): ?>
                    <p class="text-sm text-slate-500">Nenhuma turma cadastrada no sistema.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold text-slate-500 uppercase tracking-widest">
                                    <th class="py-3 px-4">Nome da Turma</th>
                                    <th class="py-3 px-4">Coordenador (ID)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php foreach ($turmas as $t): ?>
                                    <tr>
                                        <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($t->nome) ?></td>
                                        <td class="py-3.5 px-4">
                                            <?php if ($t->professor_coordenador_id): ?>
                                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-semibold">
                                                    Prof. ID <?= $t->professor_coordenador_id ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400 italic">Sem Coordenador</span>
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
