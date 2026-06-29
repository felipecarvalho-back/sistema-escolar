<?php $this->layout('layouts/app', ['title' => 'Visão Geral - Guardian']); ?>

<?php $this->section('content'); ?>
    <div class="space-y-8">
        <div class="border-b border-slate-200 pb-5">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Visão Geral</h2>
            <p class="text-slate-600 mt-1">Resumo das suas informações no sistema Guardian.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php if ($user['perfil'] === 'secretaria'): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center">
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-widest">Total de Alunos</span>
                    <p class="text-4xl font-black text-slate-900 mt-2"><?= $total_alunos ?? 0 ?></p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center">
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-widest">Turmas Ativas</span>
                    <p class="text-4xl font-black text-slate-900 mt-2"><?= $total_turmas ?? 0 ?></p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent"></div>
                    <div class="relative">
                        <span class="text-xs text-amber-600 font-bold uppercase tracking-widest">Ocorrências Pendentes</span>
                        <p class="text-4xl font-black text-amber-600 mt-2"><?= $ocorrencias_pendentes ?? 0 ?></p>
                    </div>
                </div>
            <?php elseif ($user['perfil'] === 'professor'): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center">
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-widest">Turmas Coordenadas</span>
                    <p class="text-4xl font-black text-slate-900 mt-2"><?= $total_turmas_coordenadas ?? 0 ?></p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent"></div>
                    <div class="relative">
                        <span class="text-xs text-amber-600 font-bold uppercase tracking-widest">Aprovações Pendentes</span>
                        <p class="text-4xl font-black text-amber-600 mt-2"><?= $ocorrencias_pendentes ?? 0 ?></p>
                    </div>
                </div>
            <?php elseif ($user['perfil'] === 'responsavel'): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center">
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-widest">Alunos Dependentes</span>
                    <p class="text-4xl font-black text-slate-900 mt-2"><?= $total_filhos ?? 0 ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($user['perfil'] === 'responsavel'): ?>
            <!-- Lista de Filhos e Ocorrências -->
            <div class="space-y-6 mt-8">
                <div class="border-b border-slate-200 pb-3">
                    <h3 class="text-xl font-bold text-slate-900">Seus Filhos / Dependentes</h3>
                    <p class="text-sm text-slate-500 mt-1">Abaixo estão listadas as ocorrências aprovadas para seus filhos.</p>
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
                                            <span class="text-xs text-indigo-600 font-semibold uppercase tracking-wider block mt-1"><?= htmlspecialchars($item['parentesco'] ?? '') ?></span>
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
                                                        <span>Turma: <?= htmlspecialchars($ocorr->turma?->nome ?? 'Desconhecida') ?></span>
                                                        <span><?= date('d/m/Y H:i', strtotime($ocorr->created_at)) ?></span>
                                                    </div>
                                                    <p class="text-slate-800 text-sm mt-1"><?= htmlspecialchars($ocorr->descricao) ?></p>
                                                    <span class="text-[10px] block mt-2 text-indigo-600">Por: Prof. <?= htmlspecialchars($ocorr->autor?->nome ?? 'Desconhecido') ?></span>
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
        <?php endif; ?>
        
        <?php if ($user['perfil'] === 'secretaria'): ?>
            <!-- Cadastrar Usuário para a Secretaria continua aqui como uma ação rápida -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 max-w-lg mt-8">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Ação Rápida: Cadastrar Novo Usuário</h3>
                <form action="/secretaria/usuarios" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_usuario">Nome Completo</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="nome_usuario" type="text" name="nome" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="email_usuario">E-mail</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="email_usuario" type="email" name="email" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="senha_usuario">Senha Inicial</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="senha_usuario" type="password" name="senha" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="perfil_usuario">Perfil</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="perfil_usuario" name="perfil" required>
                            <option value="">Selecione o perfil...</option>
                            <option value="professor">Professor / Coordenador</option>
                            <option value="responsavel">Responsável / Pai de Aluno</option>
                            <option value="secretaria">Secretária / Administrativo</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition duration-200">
                        Criar Conta de Acesso
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
<?php $this->endSection(); ?>
