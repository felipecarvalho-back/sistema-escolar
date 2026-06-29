<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Coluna Principal (Listagem Geral e Vinculos) -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Lista de Alunos e Histórico de Contatos/Ocorrências -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-4">Ficha de Alunos Cadastrados</h3>
            <?php if (empty($alunos_detalhes)): ?>
                <p class="text-sm text-slate-500">Nenhum aluno cadastrado no sistema.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead>
                            <tr class="text-left text-xs font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-4">Aluno</th>
                                <th class="py-3 px-4">Turma</th>
                                <th class="py-3 px-4">Ocorrências Aprovadas</th>
                                <th class="py-3 px-4">Responsáveis / Contato</th>
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
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="text-sm font-black px-2.5 py-1 rounded-xl 
                                            <?= $ad['ocorrencias_count'] >= 3 ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' ?>">
                                            <?= $ad['ocorrencias_count'] ?>
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

        <!-- Seção de Vínculos de Matrícula e Responsável -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            <!-- Vincular Aluno à Turma -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Vincular Aluno à Turma</h3>
                <form action="/secretaria/vincular-turma" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="aluno_v_turma">Aluno</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="aluno_v_turma" name="aluno_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($alunos_detalhes as $ad): ?>
                                <option value="<?= $ad['model']->id ?>"><?= htmlspecialchars($ad['model']->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="turma_v_turma">Turma</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="turma_v_turma" name="turma_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?= $turma->id ?>"><?= htmlspecialchars($turma->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition duration-200 shadow-sm">
                        Matricular Aluno na Sala
                    </button>
                </form>
            </div>

            <!-- Vincular Aluno ao Responsável -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Vincular Responsável / Pai</h3>
                <form action="/secretaria/vincular-responsavel" method="POST" class="space-y-3">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="aluno_v_resp">Aluno</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="aluno_v_resp" name="aluno_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($alunos_detalhes as $ad): ?>
                                <option value="<?= $ad['model']->id ?>"><?= htmlspecialchars($ad['model']->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="resp_v_resp">Responsável Cadastrado</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="resp_v_resp" name="responsavel_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($responsaveis_disponiveis as $resp): ?>
                                <option value="<?= $resp->id ?>"><?= htmlspecialchars($resp->nome) ?> (<?= htmlspecialchars($resp->email) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="parentesco">Parentesco</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="parentesco" type="text" name="parentesco" placeholder="Ex: Pai, Mãe, Tio" required>
                    </div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition duration-200 shadow-sm">
                        Vincular Dependência
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Coluna Lateral (Cadastros Administrativos da Secretaria) -->
    <div class="space-y-8">
        
        <!-- Cadastrar Usuário -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Cadastrar Usuário</h3>
            <form action="/secretaria/usuarios" method="POST" class="space-y-3">
                <?= csrf_field() ?? '' ?>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_usuario">Nome Completo</label>
                    <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="nome_usuario" type="text" name="nome" placeholder="Nome do usuário" required>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="email_usuario">E-mail</label>
                    <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="email_usuario" type="email" name="email" placeholder="email@exemplo.com" required>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="senha_usuario">Senha Inicial</label>
                    <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="senha_usuario" type="password" name="senha" placeholder="••••••••" required>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="perfil_usuario">Perfil</label>
                    <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="perfil_usuario" name="perfil" required>
                        <option value="">Selecione o perfil...</option>
                        <option value="professor">Professor / Coordenador</option>
                        <option value="responsavel">Responsável / Pai de Aluno</option>
                        <option value="secretaria">Secretária / Administrativo</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition duration-200 shadow-sm">
                    Criar Conta
                </button>
            </form>
        </div>

        <!-- Cadastrar Aluno -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Cadastrar Novo Aluno</h3>
            <form action="/secretaria/alunos" method="POST" class="space-y-3">
                <?= csrf_field() ?? '' ?>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_aluno">Nome Completo</label>
                    <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="nome_aluno" type="text" name="nome" placeholder="Nome do aluno" required>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="data_nasc_aluno">Data de Nascimento</label>
                    <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="data_nasc_aluno" type="date" name="data_nascimento">
                </div>
                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition duration-200 shadow-sm">
                    Adicionar Aluno
                </button>
            </form>
        </div>

        <!-- Cadastrar Turma -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Cadastrar Nova Turma</h3>
            <form action="/secretaria/turmas" method="POST" class="space-y-3">
                <?= csrf_field() ?? '' ?>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_turma">Nome da Sala</label>
                    <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="nome_turma" type="text" name="nome" placeholder="Ex: 1º Ano A, 2º Ano B" required>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-semibold mb-1" for="professor_coordenador">Professor Coordenador</label>
                    <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" id="professor_coordenador" name="professor_coordenador_id">
                        <option value="">Selecione o Professor...</option>
                        <?php foreach ($professores as $prof): ?>
                            <option value="<?= $prof->id ?>"><?= htmlspecialchars($prof->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition duration-200 shadow-sm">
                    Criar Sala de Aula
                </button>
            </form>
        </div>

        <!-- Ocorrências Pendentes Globais -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Aprovações Pendentes</h3>
            <?php if (empty($ocorrencias_pendentes)): ?>
                <p class="text-xs text-slate-500">Nenhuma ocorrência necessita de aprovação.</p>
            <?php else: ?>
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                    <?php foreach ($ocorrencias_pendentes as $ocorr): ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs shadow-sm">
                            <div class="space-y-0.5">
                                <p class="font-bold text-slate-900"><?= htmlspecialchars($ocorr['aluno_name'] ?? $ocorr['aluno_nome']) ?></p>
                                <p class="text-slate-600">Turma: <?= htmlspecialchars($ocorr['turma_name'] ?? $ocorr['turma_nome']) ?></p>
                                <p class="text-slate-500">Cadastrado por: Prof. <?= htmlspecialchars($ocorr['autor_nome'] ?? $ocorr['autor_name'] ?? '') ?></p>
                            </div>
                            <p class="bg-white p-2.5 rounded-lg border border-slate-200 text-slate-700"><?= htmlspecialchars($ocorr['descricao']) ?></p>
                            <div class="flex gap-2 pt-1">
                                <form action="/ocorrencias/<?= $ocorr['id'] ?>/aprovar" method="POST" class="flex-grow">
                                    <?= csrf_field() ?? '' ?>
                                    <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg transition duration-200">
                                        Aprovar
                                    </button>
                                </form>
                                <form action="/ocorrencias/<?= $ocorr['id'] ?>/rejeitar" method="POST" class="flex-grow">
                                    <?= csrf_field() ?? '' ?>
                                    <button type="submit" class="w-full py-1.5 bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-600 hover:text-white font-bold text-[10px] rounded-lg transition duration-200">
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
