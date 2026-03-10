<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

// Fetch Business Name
$stmt = $pdo->prepare("SELECT name FROM businesses WHERE id = ? AND user_id = ?");
$stmt->execute([$business_id, $user_id]);
$business = $stmt->fetch();
$business_name = $business['name'] ?? 'Dashboard';

// Current Date Figures
$today = date('Y-m-d');

// Today Income
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM income WHERE user_id = ? AND business_id = ? AND date = ?");
$stmt->execute([$user_id, $business_id, $today]);
$today_income = $stmt->fetch()['total'] ?? 0;

// Today Expense
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND business_id = ? AND date = ?");
$stmt->execute([$user_id, $business_id, $today]);
$today_expense = $stmt->fetch()['total'] ?? 0;

// Total Balance (All time)
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM income WHERE user_id = ? AND business_id = ?");
$stmt->execute([$user_id, $business_id]);
$total_income = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND business_id = ?");
$stmt->execute([$user_id, $business_id]);
$total_expense = $stmt->fetch()['total'] ?? 0;

$total_balance = $total_income - $total_expense;

// Udhaar counts
// I Gave - They Returned = Receivable
$stmt = $pdo->prepare("SELECT 
    SUM(CASE WHEN type='given' THEN amount ELSE 0 END) - 
    SUM(CASE WHEN type='received' THEN amount ELSE 0 END) as receivable
    FROM udhaar_transactions WHERE user_id = ? AND business_id = ?");
$stmt->execute([$user_id, $business_id]);
$udhaar_given = $stmt->fetch()['receivable'] ?? 0;

// I Borrowed - I Repaid = Payable
$stmt = $pdo->prepare("SELECT 
    SUM(CASE WHEN type='borrowed' THEN amount ELSE 0 END) - 
    SUM(CASE WHEN type='repaid' THEN amount ELSE 0 END) as payable
    FROM udhaar_transactions WHERE user_id = ? AND business_id = ?");
$stmt->execute([$user_id, $business_id]);
$udhaar_taken = $stmt->fetch()['payable'] ?? 0;

// Recent Activity (Union of income, expenses, and udhaar)
$stmt = $pdo->prepare("(SELECT 'income' as type, source as title, amount, date, created_at FROM income WHERE user_id = ? AND business_id = ?)
                       UNION ALL
                       (SELECT 'expense' as type, category as title, amount, date, created_at FROM expenses WHERE user_id = ? AND business_id = ?)
                       UNION ALL
                       (SELECT CONCAT('udhaar_', type) as type, (SELECT name FROM contacts WHERE id = contact_id) as title, amount, date, created_at FROM udhaar_transactions WHERE user_id = ? AND business_id = ?)
                       ORDER BY date DESC, created_at DESC LIMIT 5");
$stmt->execute([$user_id, $business_id, $user_id, $business_id, $user_id, $business_id]);
$recent_activities = $stmt->fetchAll();

// Chart Data (Last 7 Days Expense)
$days = [];
$expense_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $days[] = date('D', strtotime($date));

    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND business_id = ? AND date = ?");
    $stmt->execute([$user_id, $business_id, $date]);
    $expense_data[] = $stmt->fetch()['total'] ?? 0;
}

// Chart Data (Last 6 Months Income)
$months = [];
$income_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $months[] = date('M', strtotime($month_start));

    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM income WHERE user_id = ? AND business_id = ? AND date BETWEEN ? AND ?");
    $stmt->execute([$user_id, $business_id, $month_start, date('Y-m-t', strtotime($month_start))]);
    $income_data[] = $stmt->fetch()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('dashboard', 'Dashboard'); ?> - <?php echo __('app_name', 'Cashflow Diary'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="p-4 md:p-10">
        <!-- Dashboard Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight"><?php echo __('welcome_back_dash', 'Welcome back!'); ?></h1>
            <p class="text-slate-500 mt-1"><?php echo __('heres_happening_with', "Here's what's happening with"); ?> <span class="text-indigo-600 font-bold"><?php echo e($business_name); ?></span> <?php echo __('today', 'today'); ?>.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">
            <!-- Card 1 -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" /></svg>
                </div>
                <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider"><?php echo __('today_income', 'Today Income'); ?></p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?php echo format_currency($today_income); ?></p>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
                </div>
                <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider"><?php echo __('today_expense', 'Today Expense'); ?></p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?php echo format_currency($today_expense); ?></p>
            </div>

            <!-- Card 3 -->
            <div class="bg-indigo-600 p-6 rounded-[2rem] shadow-lg shadow-indigo-200 text-white transform hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-indigo-500 text-white rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-indigo-200 text-sm font-semibold uppercase tracking-wider"><?php echo __('net_balance', 'Net Balance'); ?></p>
                <p class="text-2xl font-bold text-white mt-1"><?php echo format_currency($total_balance); ?></p>
            </div>

            <!-- Card 4 -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3" /></svg>
                </div>
                <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider"><?php echo __('udhaar_taken', 'Udhaar Taken'); ?></p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?php echo format_currency($udhaar_taken); ?></p>
            </div>

            <!-- Card 5 -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18" /></svg>
                </div>
                <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider"><?php echo __('udhaar_given', 'Udhaar Given'); ?></p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?php echo format_currency($udhaar_given); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Charts Section -->
            <div class="lg:col-span-2 space-y-10">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-bold text-slate-800 mb-6"><?php echo __('weekly_expense_trend', 'Weekly Expense Trend'); ?></h3>
                    <canvas id="weeklyChart" height="150"></canvas>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-bold text-slate-800 mb-6"><?php echo __('monthly_income_analysis', 'Monthly Income Analysis'); ?></h3>
                    <canvas id="monthlyChart" height="150"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 h-fit">
                <h3 class="text-xl font-bold text-slate-800 mb-6"><?php echo __('recent_activity', 'Recent Activity'); ?></h3>
                <div class="space-y-6">
                    <?php if (empty($recent_activities)): ?>
                        <p class="text-slate-400 text-center py-10"><?php echo __('no_recent_activity', 'No recent activity found.'); ?></p>
                    <?php else: ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="flex items-center justify-between group<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
                                <div class="flex items-center space-x-4<?php echo get_lang_dir() === 'rtl' ? ' space-x-reverse' : ''; ?>">
                                    <div class="w-12 h-12 <?php 
                                        if ($activity['type'] == 'income') echo 'bg-emerald-50 text-emerald-600';
                                        elseif ($activity['type'] == 'expense') echo 'bg-rose-50 text-rose-600';
                                        elseif (strpos($activity['type'], 'udhaar') !== false) echo 'bg-amber-50 text-amber-600';
                                        else echo 'bg-slate-50 text-slate-600';
                                    ?> rounded-2xl flex items-center justify-center font-bold">
                                        <?php 
                                            if ($activity['type'] == 'income') echo '+';
                                            elseif ($activity['type'] == 'expense') echo '-';
                                            else echo 'U';
                                        ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors"><?php echo e($activity['title']); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo format_date($activity['date']); ?></p>
                                    </div>
                                </div>
                                <span class="font-bold <?php echo $activity['type'] == 'income' ? 'text-emerald-600' : 'text-slate-900'; ?>">
                                    <?php echo format_currency($activity['amount']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="../income/list.php" class="block text-center mt-10 text-indigo-600 font-bold hover:underline"><?php echo __('view_all_transactions', 'View All Transactions'); ?></a>
            </div>
        </div>
    </main>

    <script>
        // Dark Mode Chart Configuration
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        // Weekly Expense Chart
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($days); ?>,
                datasets: [{
                    label: 'Expenses',
                    data: <?php echo json_encode($expense_data); ?>,
                    borderColor: '#e11d48',
                    backgroundColor: 'rgba(225, 29, 72, 0.05)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#e11d48',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#fff',
                        titleColor: isDark ? '#f1f5f9' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { display: true, color: gridColor }, 
                        ticks: { display: false } 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                }
            }
        });

        // Monthly Income Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Income',
                    data: <?php echo json_encode($income_data); ?>,
                    backgroundColor: '#10b981',
                    borderRadius: 12,
                    barThickness: 30
                }]
            },
            options: {
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#fff',
                        titleColor: isDark ? '#f1f5f9' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { display: true, color: gridColor }, 
                        ticks: { display: false } 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                }
            }
        });
    </script>
</body>
</html>
