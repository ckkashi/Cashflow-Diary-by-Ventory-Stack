<?php
require_once 'includes/config_check.php';
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashflow Diary - Smart Financial Management</title>
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
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">C</div>
                <span class="font-bold text-slate-900 text-xl tracking-tight">Cashflow Diary</span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Features</a>
                <a href="#about" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">About</a>
                <a href="login.php" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-600 transform hover:-translate-y-0.5 transition-all text-sm shadow-lg shadow-slate-200">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right" data-aos-duration="1000">
                <span class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-widest mb-6">Financial Freedom Starts Here</span>
                <h1 class="text-6xl md:text-7xl font-bold text-slate-900 leading-[1.1] tracking-tight mb-8">
                    Master Your Money with <span class="gradient-text">Precision.</span>
                </h1>
                <p class="text-xl text-slate-500 leading-relaxed max-w-lg mb-10">
                    The ultimate personal and business ledger. Track income, manage expenses, and keep your Udhaar (Debt) records synchronized in one premium dashboard.
                </p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="login.php" class="bg-indigo-600 text-white px-10 py-5 rounded-[2rem] font-bold text-lg hover:bg-indigo-700 transform hover:-translate-y-1 transition-all shadow-2xl shadow-indigo-200 text-center">Get Started Free</a>
                    <a href="#features" class="flex items-center justify-center space-x-3 text-slate-700 font-bold hover:text-indigo-600 transition-all text-center">
                        <span>Explore Features</span>
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
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Powerful Features for Peace of Mind</h2>
                <p class="text-slate-500 max-w-2xl mx-auto text-lg">Everything you need to manage your personal finances or small business ledger with modern precision.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:border-indigo-100 transition-all transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Income Tracking</h3>
                    <p class="text-slate-500 leading-relaxed mb-6">Easily record and categorize every dollar earned. Track salaries, freelance gigs, and business revenue in real-time.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5 text-emerald-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Instant Totals
                        </li>
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5 text-emerald-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Source Tagging
                        </li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:border-rose-100 transition-all transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Expense Management</h3>
                    <p class="text-slate-500 leading-relaxed mb-6">Never lose track of where your money goes. Record daily spendings, attach notes, and analyze your burn rate.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5 text-rose-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Daily Categorization
                        </li>
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5 text-rose-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Budget Insights
                        </li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:border-amber-100 transition-all transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Udhaar Ledger</h3>
                    <p class="text-slate-500 leading-relaxed mb-6">Manage Debt and Credit by contact. Know exactly who owes you and who you owe, with automated balance tracking.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Contact History
                        </li>
                        <li class="flex items-center text-sm font-bold text-slate-700">
                            <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Net Balance View
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
                <h2 class="text-4xl font-bold text-slate-900 mb-6">Manage Multiple Businesses Seamlessly.</h2>
                <p class="text-lg text-slate-500 mb-8 leading-relaxed">
                    Personal finance or multiple shops? Cashflow Diary supports multi-tenant business profiles. Switch contexts in a single click and keep your accounts separate but accessible.
                </p>
                <div class="grid grid-cols-2 gap-6 mb-10">
                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-2xl font-bold text-indigo-600 mb-1">∞</p>
                        <p class="text-sm font-bold text-slate-700 uppercase tracking-tighter">Unlimited Biz</p>
                    </div>
                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-2xl font-bold text-indigo-600 mb-1">100%</p>
                        <p class="text-sm font-bold text-slate-700 uppercase tracking-tighter">Privacy Secluded</p>
                    </div>
                </div>
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
            <h2 class="text-5xl font-bold text-slate-900 mb-8 leading-tight">Ready to take control of your financial destiny?</h2>
            <p class="text-xl text-slate-500 mb-12">Join thousands of users who trust Cashflow Diary for their daily ledgering needs.</p>
            <a href="login.php" class="inline-block bg-slate-900 text-white px-12 py-6 rounded-[2.5rem] font-bold text-xl hover:bg-slate-800 transform hover:scale-110 transition-all shadow-2xl">Start for free today</a>
            <p class="mt-8 text-sm text-slate-400 font-medium italic">"The most beautiful ledger app I've ever used." - Satisfied User</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 px-6 border-t border-slate-100 bg-white/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">C</div>
                <span class="font-bold text-slate-800 text-lg tracking-tight">Cashflow Diary</span>
            </div>
            <p class="text-slate-400 text-sm font-medium">© 2026 Cashflow Diary. A Product by <a href="#" class="text-slate-600 font-bold hover:text-indigo-600">Ventory Stack</a></p>
            <div class="flex space-x-6">
                <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors"><svg class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                <a href="#" class="text-slate-400 hover:text-emerald-600 transition-colors"><svg class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
            </div>
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
