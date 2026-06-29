<?php $this->layout('layouts/app', ['title' => 'Gestão de Usuários - Guardian']); ?>

<?php $this->section('content'); ?>
<div class="space-y-8">
    <div class="border-b border-slate-200 pb-5">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Gestão de Usuários</h2>
        <p class="text-slate-600 mt-1">Gerencie os acessos de Secretários, Professores e Responsáveis no sistema Guardian.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna da Esquerda: Cadastro de Usuário -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center space-x-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">👤</span>
                    <span>Novo Usuário</span>
                </h3>
                <form action="/secretaria/usuarios" method="POST" class="space-y-4">
                    <?= csrf_field() ?? '' ?>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="nome_usuario">Nome Completo</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="nome_usuario" type="text" name="nome" placeholder="Ex: Ana Souza" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="email_usuario">E-mail</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="email_usuario" type="email" name="email" placeholder="nome@escola.com" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="senha_usuario">Senha Inicial</label>
                        <input class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="senha_usuario" type="password" name="senha" placeholder="••••••••" required>
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1" for="perfil_usuario">Perfil de Acesso</label>
                        <select class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" id="perfil_usuario" name="perfil" required>
                            <option value="">Selecione o perfil...</option>
                            <option value="professor">Professor / Coordenador</option>
                            <option value="responsavel">Responsável / Pai de Aluno</option>
                            <option value="secretaria">Secretária / Administrativo</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-250">
                        Criar Conta de Acesso
                    </button>
                </form>
            </div>
        </div>

        <!-- Coluna da Direita: Lista de Usuários -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-4">Usuários Cadastrados</h3>
                <?php if (empty($usuarios)): ?>
                    <p class="text-sm text-slate-500">Nenhum usuário cadastrado.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold text-slate-500 uppercase tracking-widest">
                                    <th class="py-3 px-4">Nome</th>
                                    <th class="py-3 px-4">E-mail</th>
                                    <th class="py-3 px-4">Perfil</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($u->nome) ?></td>
                                        <td class="py-3.5 px-4"><?= htmlspecialchars($u->email) ?></td>
                                        <td class="py-3.5 px-4">
                                            <?php 
                                            $perfilColor = 'bg-slate-100 text-slate-700 border-slate-200';
                                            $perfilNome = 'Desconhecido';
                                            if ($u->perfil === 'secretaria') {
                                                $perfilColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                $perfilNome = 'Secretaria';
                                            } elseif ($u->perfil === 'professor') {
                                                $perfilColor = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                                $perfilNome = 'Professor';
                                            } elseif ($u->perfil === 'responsavel') {
                                                $perfilColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                                $perfilNome = 'Responsável';
                                            }
                                            ?>
                                            <span class="px-2.5 py-1 rounded-lg border text-xs font-semibold <?= $perfilColor ?>">
                                                <?= $perfilNome ?>
                                            </span>
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
