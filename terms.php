<?php
require_once 'includes/config_check.php';
session_start();

$current_lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $current_lang;
$lang_data = require "languages/{$current_lang}.php";
require_once 'includes/helpers.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('terms_of_service', 'Terms of Service'); ?> - <?php echo __('app_name', 'Cashflow Diary'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .hero-gradient { background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.1), transparent), radial-gradient(circle at bottom left, rgba(236, 72, 153, 0.1), transparent); }
    </style>
</head>
<body class="bg-slate-50 hero-gradient min-h-screen">
    <nav class="p-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center glass p-4 px-8 rounded-3xl shadow-xl shadow-indigo-100/20">
            <a href="index.php" class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">C</div>
                <span class="font-bold text-slate-900 text-xl tracking-tight"><?php echo __('app_name', 'Cashflow Diary'); ?></span>
            </a>
            <div class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-6">
                 <div class="flex bg-slate-100/50 p-1 rounded-xl">
                    <a href="?lang=en" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $current_lang === 'en' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700'; ?>">EN</a>
                    <a href="?lang=ur" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $current_lang === 'ur' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700'; ?>">اردو</a>
                </div>
                <a href="index.php" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors"><?php echo __('back_to_home', 'Back to Home'); ?></a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto py-20 px-6">
        <div class="glass p-12 md:p-16 rounded-[3rem] shadow-2xl shadow-indigo-100/20">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-8"><?php echo __('terms_of_service', 'Terms of Service'); ?></h1>
            <div class="prose prose-slate prose-lg max-w-none text-slate-600 leading-relaxed space-y-6">
                <p><?php echo __('terms_desc', 'By using Cashflow Diary...'); ?></p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                <h2 class="text-2xl font-bold text-slate-800 mt-10 mb-4">Acceptable Use</h2>
                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center text-slate-400 text-sm font-medium">
        © 2026 <?php echo __('app_name', 'Cashflow Diary'); ?>. All rights reserved.
    </footer>
</body>
</html>
