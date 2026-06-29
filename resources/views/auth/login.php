<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Guardian</title>
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
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="text-slate-800 flex items-center justify-center min-h-screen p-4">

    <div class="glass-card p-8 rounded-2xl w-full max-w-md relative overflow-hidden">
        
        <div class="flex flex-col items-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-md mb-3">
                <span class="font-bold text-xl text-white">G</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Acesse sua conta</h2>
            <p class="text-slate-500 text-xs mt-1 uppercase tracking-widest font-semibold">Guardian · Sistema Escolar</p>
        </div>
        
        <?php if ($error = errors('email')): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl mb-6 text-sm flex items-center justify-between shadow-sm">
                <span><?= htmlspecialchars($error) ?></span>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-5">
            <?= csrf_field() ?? '' ?>
            
            <div>
                <label class="block text-slate-700 text-sm font-semibold mb-2" for="email">E-mail</label>
                <input class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-200 shadow-sm" id="email" type="email" name="email" placeholder="nome@exemplo.com" value="<?= htmlspecialchars(old('email')) ?>" required>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-slate-700 text-sm font-semibold" for="senha">Senha</label>
                </div>
                <input class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition duration-200 shadow-sm" id="senha" type="password" name="senha" placeholder="••••••••" required>
            </div>
            
            <button class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition duration-300 focus:outline-none" type="submit">
                Entrar
            </button>
        </form>
    </div>

</body>
</html>
