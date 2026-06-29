<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Painel Escolar - Guardian') ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc; /* slate-50 */
        }
        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0; /* slate-200 */
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            border-radius: 1rem;
        }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col pb-12">

    <!-- Header / Navbar -->
    <nav class="bg-white sticky top-0 z-50 border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md">
                        <span class="font-bold text-lg text-white">G</span>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900">Guardian</span>
                        <span class="text-xs block text-slate-500 font-medium tracking-widest uppercase">Sistema de Diários</span>
                    </div>
                </div>
                
                <?php if (session()->has('user')): ?>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/dashboard" class="text-slate-600 hover:text-indigo-600 font-medium transition">Início</a>
                    <?php if ((session('user')['perfil'] ?? '') === 'secretaria'): ?>
                        <a href="/alunos" class="text-slate-600 hover:text-indigo-600 font-medium transition">Alunos</a>
                        <a href="/turmas" class="text-slate-600 hover:text-indigo-600 font-medium transition">Turmas</a>
                    <?php endif; ?>
                    <a href="/ocorrencias" class="text-slate-600 hover:text-indigo-600 font-medium transition">Ocorrências</a>
                </div>
                <?php endif; ?>

                <div class="flex items-center space-x-6">
                    <div class="text-right hidden sm:block">
                        <span class="text-sm font-semibold text-slate-800 block"><?= htmlspecialchars(session('user')['nome'] ?? 'Usuário') ?></span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                            <?php $p = session('user')['perfil'] ?? ''; ?>
                            <?= $p === 'responsavel' ? 'Responsável' : ($p === 'professor' ? 'Professor' : 'Secretaria') ?>
                        </span>
                    </div>
                    <form action="/logout" method="POST" class="inline m-0">
                        <?= csrf_field() ?? '' ?>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 hover:text-slate-900 font-medium transition duration-300 text-sm focus:outline-none">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-10 flex-grow">
        
        <!-- Notificações Flash -->
        <?php if (session()->has('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                <span><?= session()->get('success') ?></span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('errors')): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    <?php foreach (session()->get('errors') as $field => $messages): ?>
                        <?php foreach ((array)$messages as $message): ?>
                            <li><?= htmlspecialchars($message) ?></li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Renderiza a view filha -->
        <?php $this->renderSection('content'); ?>

    </main>

</body>
</html>
