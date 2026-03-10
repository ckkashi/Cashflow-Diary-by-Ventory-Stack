<?php
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /cashflow/login');
    exit;
}

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Fetch Businesses
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ?");
$stmt->execute([$user_id]);
$businesses = $stmt->fetchAll();

$current_business_id = $_SESSION['business_id'] ?? ($businesses[0]['id'] ?? 0);
$current_business_name = 'Select Business';
foreach ($businesses as $b) {
    if ($b['id'] == $current_business_id) {
        $current_business_name = $b['name'];
        break;
    }
}

$nav_items = get_nav_items();
?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: { outfit: ['Outfit', 'sans-serif'] },
                colors: {
                    dark: {
                        bg: '#0f172a',
                        card: '#1e293b',
                        border: '#334155',
                        text: '#f1f5f9'
                    }
                }
            }
        }
    }
</script>
<style>
    /* Dark Mode Global Overrides */
    .dark body { background-color: #0f172a; color: #f1f5f9; }
    .dark .bg-white { background-color: #1e293b !important; }
    .dark .text-slate-900 { color: #f1f5f9 !important; }
    .dark .text-slate-500 { color: #94a3b8 !important; }
    .dark .border-slate-200, .dark .border-slate-100 { border-color: #334155 !important; }
    .dark .bg-slate-50 { background-color: #0f172a !important; }
    .dark .shadow-sm { shadow: none !important; }
    
    .sidebar-active {
        background: <?php echo get_lang_dir() === 'rtl' ? 'linear-gradient(to left, rgba(79, 70, 229, 0.1), transparent)' : 'linear-gradient(to right, rgba(79, 70, 229, 0.1), transparent)'; ?>;
        border-<?php echo get_lang_dir() === 'rtl' ? 'right' : 'left'; ?>: 4px solid #4f46e5;
        color: #4f46e5 !important;
    }
</style>

<!-- Mobile Header -->
<div class="lg:hidden bg-white dark:bg-dark-card border-b dark:border-dark-border p-4 flex justify-between items-center sticky top-0 z-50 <?php echo get_lang_dir() === 'rtl' ? 'flex-row-reverse' : ''; ?>">
    <div class="flex items-center space-x-2 <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?>">
        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">C</div>
        <span class="font-bold text-slate-800 dark:text-white"><?php echo __('app_name', 'Cashflow Diary'); ?></span>
    </div>
    <button onclick="document.getElementById('sidebar').classList.toggle('<?php echo get_lang_dir() === 'rtl' ? 'translate-x-full' : '-translate-x-full'; ?>')" class="p-2 text-slate-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
    </button>
</div>

<!-- Sidebar -->
<aside id="sidebar" dir="<?php echo get_lang_dir(); ?>" class="fixed inset-y-0 <?php echo get_lang_dir() === 'rtl' ? 'right-0' : 'left-0'; ?> w-72 bg-white dark:bg-dark-card border-<?php echo get_lang_dir() === 'rtl' ? 'l' : 'r'; ?> dark:border-dark-border transform <?php echo get_lang_dir() === 'rtl' ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0'; ?> transition-transform duration-300 z-50 flex flex-col">
    <!-- Branding -->
    <div class="p-8 border-b dark:border-dark-border">
        <div class="flex items-center space-x-3 mb-2<?php echo get_lang_dir() === 'rtl' ? ' space-x-reverse' : ''; ?>">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">C</div>
            <div>
                <h1 class="font-bold text-slate-900 dark:text-white text-lg leading-tight"><?php echo __('app_name', 'Cashflow Diary'); ?></h1>
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">by Ventory Stack</p>
            </div>
        </div>
    </div>

    <!-- Business Switcher -->
    <div class="p-6">
        <div class="relative group">
            <button class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border dark:border-dark-border hover:border-indigo-300 transition-all<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
                <div class="flex items-center space-x-3 overflow-hidden<?php echo get_lang_dir() === 'rtl' ? ' space-x-reverse' : ''; ?>">
                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center flex-shrink-0 font-bold">
                        <?php echo strtoupper(substr($current_business_name, 0, 1)); ?>
                    </div>
                    <span class="font-bold text-slate-700 dark:text-slate-200 truncate"><?php echo e($current_business_name); ?></span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
            
            <div class="absolute <?php echo get_lang_dir() === 'rtl' ? 'right-0' : 'left-0'; ?> right-0 mt-2 bg-white dark:bg-dark-card border dark:border-dark-border rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                <?php foreach ($businesses as $biz): ?>
                    <a href="/cashflow/businesses/switch?id=<?php echo $biz['id']; ?>" class="flex items-center space-x-3 p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors<?php echo get_lang_dir() === 'rtl' ? ' space-x-reverse' : ''; ?>">
                        <div class="w-8 h-8 bg-slate-100 dark:bg-slate-700 text-slate-500 rounded-lg flex items-center justify-center font-bold"><?php echo strtoupper(substr($biz['name'], 0, 1)); ?></div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e($biz['name']); ?></span>
                    </a>
                <?php endforeach; ?>
                <div class="p-2 border-t dark:border-dark-border bg-slate-50 dark:bg-slate-800">
                    <a href="/cashflow/businesses/add" class="flex items-center justify-center p-3 text-indigo-600 font-bold text-xs uppercase tracking-widest hover:bg-white dark:hover:bg-dark-card rounded-xl transition-all">
                        + <?php echo __('create_business', 'Create Business'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
        <?php foreach ($nav_items as $item):
    $isActive = (strpos($_SERVER['REQUEST_URI'], $item['id']) !== false);
    if ($item['id'] == 'dashboard' && $current_page == 'index.php' && ($current_dir == 'dashboard' || $current_dir == 'cashflow'))
        $isActive = true;
?>
            <a href="<?php echo $item['url']; ?>" class="flex items-center space-x-4 px-4 py-4 rounded-2xl font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all <?php echo $isActive ? 'sidebar-active text-indigo-600 dark:text-indigo-400' : ''; ?><?php echo get_lang_dir() === 'rtl' ? ' space-x-reverse' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $item['icon']; ?>" /></svg>
                <span><?php echo $item['name']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Sidebar Bottom -->
    <div class="p-6 border-t dark:border-dark-border space-y-4">
        <!-- Language Switcher -->
        <div class="flex bg-slate-50 dark:bg-slate-800 p-2 rounded-2xl border dark:border-dark-border">
            <a href="?lang=en" class="flex-1 text-center py-2 rounded-xl text-xs font-bold transition-all <?php echo $current_lang === 'en' ? 'bg-white dark:bg-dark-card shadow-sm text-indigo-600' : 'text-slate-400 hover:text-slate-600'; ?>"><?php echo __('english', 'English'); ?></a>
            <a href="?lang=ur" class="flex-1 text-center py-2 rounded-xl text-xs font-bold transition-all <?php echo $current_lang === 'ur' ? 'bg-white dark:bg-dark-card shadow-sm text-indigo-600' : 'text-slate-400 hover:text-slate-600'; ?>"><?php echo __('urdu', 'Urdu'); ?></a>
        </div>

        <!-- Dark Mode Toggle -->
        <button onclick="toggleDarkMode()" class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border dark:border-dark-border text-slate-700 dark:text-slate-300 font-bold transition-all<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
            <span class="flex items-center<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
                <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?php echo get_lang_dir() === 'rtl' ? 'ml-3' : 'mr-3'; ?> hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.95 16.95l.707.707M7.05 7.05l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" /></svg>
                <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?php echo get_lang_dir() === 'rtl' ? 'ml-3' : 'mr-3'; ?> block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <?php echo __('dark_mode', 'Dark Mode'); ?>
            </span>
            <div class="w-10 h-6 bg-slate-200 dark:bg-indigo-600 rounded-full relative p-1 transition-colors">
                <div class="w-4 h-4 bg-white rounded-full shadow-sm transform transition-transform dark:translate-x-4"></div>
            </div>
        </button>

        <a href="/cashflow/logout" class="flex items-center space-x-4 px-4 py-4 rounded-2xl font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse flex-row-reverse' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 <?php echo get_lang_dir() === 'rtl' ? 'rotate-180' : ''; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            <span><?php echo __('logout', 'Logout'); ?></span>
        </a>
    </div>
</aside>
</aside>

<!-- Multi-Action Floating Button -->
<div class="fixed bottom-8 <?php echo get_lang_dir() === 'rtl' ? 'left-8' : 'right-8'; ?> z-[100] group">
    <!-- Buttons Menu -->
    <div id="fab-menu" class="flex flex-col-reverse items-center mb-4 space-y-reverse space-y-3 opacity-0 invisible translate-y-4 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300">
        <a href="/cashflow/expenses/add" title="Add Expense" class="w-12 h-12 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </a>
        <a href="/cashflow/income/add" title="Add Income" class="w-12 h-12 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-41 w-64" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </a>
        <a href="/cashflow/udhaar/add" title="Add Udhaar" class="w-12 h-12 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </a>
    </div>
    
    <!-- Main FAB Trigger -->
    <button class="w-16 h-16 bg-slate-900 text-white rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 group-hover:rotate-45 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
    </button>
</div>

<!-- Extra Layout Wrapper (Fix for Sidebar overlap) -->
<style>
    @media (min-width: 1024px) {
        body { padding-<?php echo get_lang_dir() === 'rtl' ? 'right' : 'left'; ?>: 18rem; }
    }
</style>

<!-- Notification Hub -->
<div id="notification-container" class="fixed top-6 <?php echo get_lang_dir() === 'rtl' ? 'left-6' : 'right-6'; ?> z-[200] space-y-3 px-4 w-full max-w-sm pointer-events-none <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>"></div>

<script>
    function showNotification(message, type = 'success') {
        const container = document.getElementById('notification-container');
        const alert = document.createElement('div');
        alert.className = `p-4 rounded-2xl shadow-2xl border flex items-center space-x-3 pointer-events-auto transform translate-x-12 opacity-0 transition-all duration-500 ${
            type === 'success' 
            ? 'bg-emerald-50 border-emerald-100 text-emerald-700' 
            : 'bg-rose-50 border-rose-100 text-rose-700'
        }`;
        
        alert.innerHTML = `
            <div class="w-8 h-8 rounded-lg flex items-center justify-center ${type === 'success' ? 'bg-emerald-100' : 'bg-rose-100'}">
                ${type === 'success' 
                    ? '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>' 
                    : '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>'}
            </div>
            <span class="font-bold text-sm">${message}</span>
        `;
        
        container.appendChild(alert);
        setTimeout(() => alert.classList.remove('translate-x-12', 'opacity-0'), 10);
        setTimeout(() => {
            alert.classList.add('translate-x-12', 'opacity-0');
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    }

    // Check for PHP session flashes
    <?php if (isset($_SESSION['success'])): ?>
        showNotification("<?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?>", 'success');
    <?php
endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        showNotification("<?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?>", 'error');
    <?php
endif; ?>

    // Dark Mode Core Logic
    function toggleDarkMode() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    // Persist Theme
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>
