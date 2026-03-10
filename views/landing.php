<?php
// This is the landing page view, included by index.php
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('app_name', 'Cashflow Diary'); ?> - Smart Financial Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-text {
            background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.1), transparent),
                        radial-gradient(circle at bottom left, rgba(236, 72, 153, 0.1), transparent);
        }
    </style>
</head>
<body class="bg-slate-50 hero-gradient selection:bg-indigo-100 selection:text-indigo-700">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 p-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center glass p-4 px-8 rounded-3xl shadow-xl shadow-indigo-100/20">
            <div class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">C</div>
                <span class="font-bold text-slate-900 text-xl tracking-tight"><?php echo __('app_name', 'Cashflow Diary'); ?></span>
            </div>
            <div class="hidden md:flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-8">
                <!-- Language Switcher -->
                <div class="flex bg-slate-100/50 p-1 rounded-xl">
                    <a href="?lang=en" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $current_lang === 'en' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700'; ?>">EN</a>
                    <a href="?lang=ur" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $current_lang === 'ur' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700'; ?>">اردو</a>
                </div>
                <a href="#features" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors"><?php echo __('features', 'Features'); ?></a>
                <a href="login" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-600 transform hover:-translate-y-0.5 transition-all text-sm shadow-lg shadow-slate-200"><?php echo __('login', 'Login'); ?></a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right" data-aos-duration="1000">
                <span class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-widest mb-6"><?php echo __('welcome', 'Financial Freedom Starts Here'); ?></span>
                <h1 class="text-6xl md:text-7xl font-bold text-slate-900 leading-[1.1] tracking-tight mb-8">
                    <?php echo __('financial_freedom_precision', 'Master Your Money with Precision.'); ?>
                </h1>
                <p class="text-xl text-slate-500 leading-relaxed max-w-lg mb-10">
                    <?php echo __('hero_subtitle', 'The ultimate personal and business ledger. Track income, manage expenses, and keep your Udhaar records synchronized in one premium dashboard.'); ?>
                </p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 <?php echo get_lang_dir() === 'rtl' ? 'sm:space-x-reverse' : ''; ?> sm:space-x-6">
                    <a href="login" class="bg-indigo-600 text-white px-10 py-5 rounded-[2rem] font-bold text-lg hover:bg-indigo-700 transform hover:-translate-y-1 transition-all shadow-2xl shadow-indigo-200 text-center"><?php echo __('get_started_free', 'Get Started Free'); ?></a>
                    <a href="#features" class="flex items-center justify-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-3 text-slate-700 font-bold hover:text-indigo-600 transition-all text-center">
                        <span><?php echo __('explore_features', 'Explore Features'); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="relative" data-aos="zoom-in" data-aos-duration="1200">
                <div class="absolute -z-10 bg-indigo-200/30 blur-[100px] w-full h-full rounded-full"></div>
                <img src="assets/img/landing-mockup.png" alt="Dashboard Preview" class="rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(79,70,229,0.2)] border border-white/50 transform hover:scale-[1.02] transition-transform duration-700">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-slate-900 mb-4"><?php echo __('features_title', 'Powerful Features for Peace of Mind'); ?></h2>
                <p class="text-slate-500 max-w-2xl mx-auto text-lg"><?php echo __('features_subtitle', 'Everything you need to manage your personal finances or small business ledger with modern precision.'); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:border-indigo-100 transition-all transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4"><?php echo __('income_tracking', 'Income Tracking'); ?></h3>
                    <p class="text-slate-500 leading-relaxed mb-6"><?php echo __('income_desc', 'Easily record and categorize every dollar earned.'); ?></p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5<?php echo get_lang_dir() === 'rtl' ? ' ml-2' : ' mr-2'; ?> text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Instant Totals
                        </li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:border-rose-100 transition-all transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4"><?php echo __('expense_management', 'Expense Management'); ?></h3>
                    <p class="text-slate-500 leading-relaxed mb-6"><?php echo __('expense_desc', 'Never lose track of where your money goes.'); ?></p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5<?php echo get_lang_dir() === 'rtl' ? ' ml-2' : ' mr-2'; ?> text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Daily Records
                        </li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:border-amber-100 transition-all transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4"><?php echo __('udhaar_ledger', 'Udhaar Ledger'); ?></h3>
                    <p class="text-slate-500 leading-relaxed mb-6"><?php echo __('udhaar_desc', 'Manage Debt and Credit by contact.'); ?></p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5<?php echo get_lang_dir() === 'rtl' ? ' ml-2' : ' mr-2'; ?> text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Contact History
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Highlight -->
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto glass p-12 md:p-20 rounded-[4rem] flex flex-col lg:flex-row items-center gap-16" data-aos="zoom-in">
            <div class="lg:w-1/2">
                <h2 class="text-4xl font-bold text-slate-900 mb-6"><?php echo __('multi_business_title', 'Manage Multiple Businesses Seamlessly.'); ?></h2>
                <p class="text-lg text-slate-500 mb-8 leading-relaxed">
                    <?php echo __('multi_business_desc', 'Personal finance or multiple shops? Cashflow Diary supports multi-tenant business profiles.'); ?>
                </p>
            </div>
            <div class="lg:w-1/2 grid grid-cols-2 gap-4">
                <div class="space-y-4 pt-12">
                   <div class="h-40 bg-indigo-600 rounded-3xl shadow-2xl flex items-center justify-center text-white text-4xl font-bold">P</div>
                   <div class="h-40 bg-emerald-500 rounded-3xl shadow-xl flex items-center justify-center text-white text-4xl font-bold">S</div>
                </div>
                <div class="space-y-4">
                   <div class="h-40 bg-rose-500 rounded-3xl shadow-xl flex items-center justify-center text-white text-4xl font-bold">O</div>
                   <div class="h-40 bg-amber-500 rounded-3xl shadow-xl flex items-center justify-center text-white text-4xl font-bold">B</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-32 px-6 text-center">
        <div class="max-w-4xl mx-auto" data-aos="fade-up">
            <h2 class="text-5xl font-bold text-slate-900 mb-8 leading-tight"><?php echo __('ready_to_control', 'Ready to take control of your financial destiny?'); ?></h2>
            <p class="text-xl text-slate-500 mb-12"><?php echo __('join_thousands', 'Join thousands of users who trust Cashflow Diary.'); ?></p>
            <a href="login" class="inline-block bg-slate-900 text-white px-12 py-6 rounded-[2.5rem] font-bold text-xl hover:bg-slate-800 transform hover:scale-110 transition-all shadow-2xl"><?php echo __('start_free_today', 'Start for free today'); ?></a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 px-6 border-t border-slate-100 bg-white/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">C</div>
                <span class="font-bold text-slate-800 text-lg tracking-tight"><?php echo __('app_name', 'Cashflow Diary'); ?></span>
            </div>
            <div class="flex flex-wrap justify-center md:justify-end items-center gap-6">
                <a href="about" class="text-slate-500 hover:text-indigo-600 transition-colors text-sm font-bold"><?php echo __('about_us', 'About Us'); ?></a>
                <a href="privacy" class="text-slate-500 hover:text-indigo-600 transition-colors text-sm font-bold"><?php echo __('privacy_policy', 'Privacy Policy'); ?></a>
                <a href="terms" class="text-slate-500 hover:text-indigo-600 transition-colors text-sm font-bold"><?php echo __('terms_of_service', 'Terms of Service'); ?></a>
            </div>
            <p class="text-slate-400 text-sm font-medium">© 2026 <?php echo __('app_name', 'Cashflow Diary'); ?>. A Product by <a href="#" class="text-slate-600 font-bold hover:text-indigo-600">Ventory Stack</a></p>
        </div>
    </footer>

    <script>
        AOS.init({
            once: true,
            offset: 120,
        });
    </script>
</body>
</html>
