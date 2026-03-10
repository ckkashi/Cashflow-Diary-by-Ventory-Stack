<?php
require_once __DIR__ . '/../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

// Helper for SUM
function getSum($pdo, $table, $user_id, $business_id, $where = "1", $params = [])
{
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM $table WHERE user_id = ? AND business_id = ? AND $where");
    $stmt->execute(array_merge([$user_id, $business_id], $params));
    return $stmt->fetchColumn() ?: 0;
}

// 1. Daily Stats (Today)
$today = date('Y-m-d');
$daily_income = getSum($pdo, 'income', $user_id, $business_id, "date = ?", [$today]);
$daily_expense = getSum($pdo, 'expenses', $user_id, $business_id, "date = ?", [$today]);
$daily_balance = $daily_income - $daily_expense;

// 2. Weekly Stats (Last 7 Days)
$last_week = date('Y-m-d', strtotime('-7 days'));
$weekly_income = getSum($pdo, 'income', $user_id, $business_id, "date >= ?", [$last_week]);
$weekly_expense = getSum($pdo, 'expenses', $user_id, $business_id, "date >= ?", [$last_week]);
$weekly_balance = $weekly_income - $weekly_expense;

// 3. Monthly Stats (Current Month)
$this_month = date('Y-m-01');
$monthly_income = getSum($pdo, 'income', $user_id, $business_id, "date >= ?", [$this_month]);
$monthly_expense = getSum($pdo, 'expenses', $user_id, $business_id, "date >= ?", [$this_month]);
$monthly_balance = $monthly_income - $monthly_expense;

// 4. Chart Data (Last 6 Months)
$chart_months = [];
$chart_income = [];
$chart_expense = [];
for ($i = 5; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));
    $chart_months[] = date('M', strtotime($month_start));
    $chart_income[] = getSum($pdo, 'income', $user_id, $business_id, "date BETWEEN ? AND ?", [$month_start, $month_end]);
    $chart_expense[] = getSum($pdo, 'expenses', $user_id, $business_id, "date BETWEEN ? AND ?", [$month_start, $month_end]);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('reports', 'Reports'); ?> - <?php echo __('app_name', 'Cashflow Diary'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="p-4 md:p-10">
        <div class="mb-10 <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight"><?php echo __('reports_dashboard', 'Reports Dashboard'); ?></h1>
            <p class="text-slate-500 mt-1"><?php echo __('deep_insights_desc', 'Deep insights and data exports.'); ?></p>
        </div>

        <!-- Quick Export Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <a href="expenses.php" class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-100 transition-all group <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform <?php echo get_lang_dir() === 'rtl' ? 'mr-auto ml-0' : ''; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900"><?php echo __('expense_report', 'Expense Report'); ?></h3>
                <p class="text-sm text-slate-500 mt-1"><?php echo __('expense_report_desc', 'Full statement with category filters and CSV export.'); ?></p>
                <div class="mt-6 flex items-center text-indigo-600 font-bold text-sm <?php echo get_lang_dir() === 'rtl' ? 'flex-row-reverse' : ''; ?>">
                    <?php echo __('view_report', 'View Report'); ?> <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 <?php echo get_lang_dir() === 'rtl' ? 'mr-2 rotate-180' : 'ml-2'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            <a href="income.php" class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:border-emerald-100 transition-all group <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform <?php echo get_lang_dir() === 'rtl' ? 'mr-auto ml-0' : ''; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900"><?php echo __('income_report', 'Income Report'); ?></h3>
                <p class="text-sm text-slate-500 mt-1"><?php echo __('income_report_desc', 'Track every source of earning with CSV data export.'); ?></p>
                <div class="mt-6 flex items-center text-emerald-600 font-bold text-sm <?php echo get_lang_dir() === 'rtl' ? 'flex-row-reverse' : ''; ?>">
                    <?php echo __('view_report', 'View Report'); ?> <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 <?php echo get_lang_dir() === 'rtl' ? 'mr-2 rotate-180' : 'ml-2'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            <a href="udhaar.php" class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:border-amber-100 transition-all group <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform <?php echo get_lang_dir() === 'rtl' ? 'mr-auto ml-0' : ''; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900"><?php echo __('udhaar_report', 'Udhaar Report'); ?></h3>
                <p class="text-sm text-slate-500 mt-1"><?php echo __('udhaar_report_desc', 'Credit and debt history filtered by contact.'); ?></p>
                <div class="mt-6 flex items-center text-amber-600 font-bold text-sm <?php echo get_lang_dir() === 'rtl' ? 'flex-row-reverse' : ''; ?>">
                    <?php echo __('view_report', 'View Report'); ?> <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 <?php echo get_lang_dir() === 'rtl' ? 'mr-2 rotate-180' : 'ml-2'; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>
        </div>

        <!-- Summary Cards Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>"><?php echo __('comparison_today', 'Comparison (Today)'); ?></p>
                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-emerald-50 p-4 rounded-2xl <?php echo get_lang_dir() === 'rtl' ? 'flex-row-reverse' : ''; ?>">
                        <span class="text-sm font-bold text-emerald-600 uppercase"><?php echo __('income', 'Income'); ?></span>
                        <span class="text-xl font-bold text-emerald-700" dir="ltr">+<?php echo format_currency($daily_income); ?></span>
                    </div>
                    <div class="flex justify-between items-center bg-rose-50 p-4 rounded-2xl <?php echo get_lang_dir() === 'rtl' ? 'flex-row-reverse' : ''; ?>">
                        <span class="text-sm font-bold text-rose-600 uppercase"><?php echo __('expenses', 'Expense'); ?></span>
                        <span class="text-xl font-bold text-rose-700" dir="ltr">-<?php echo format_currency($daily_expense); ?></span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] border border-slate-100">
                 <h3 class="text-lg font-bold text-slate-900 mb-6 <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>"><?php echo __('income_vs_expense_trend', 'Income vs Expense Trend'); ?></h3>
                 <canvas id="trendChart" height="200" dir="ltr"></canvas>
            </div>
        </div>
    </main>

    <script>
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_months); ?>,
                datasets: [
                    {
                        label: '<?php echo __('income', 'Income'); ?>',
                        data: <?php echo json_encode($chart_income); ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        borderWidth: 4,
                        tension: 0.4
                    },
                    {
                        label: '<?php echo __('expenses', 'Expense'); ?>',
                        data: <?php echo json_encode($chart_expense); ?>,
                        borderColor: '#e11d48',
                        backgroundColor: 'transparent',
                        borderWidth: 4,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { display: false }, ticks: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>
