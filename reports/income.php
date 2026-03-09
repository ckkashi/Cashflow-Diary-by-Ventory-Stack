<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

$from_date = $_GET['from_date'] ?? date('Y-m-01');
$to_date = $_GET['to_date'] ?? date('Y-m-t');

// Build Query
$query = "SELECT * FROM income WHERE user_id = ? AND business_id = ? AND date BETWEEN ? AND ? ORDER BY date DESC, created_at DESC";
$params = [$user_id, $business_id, $from_date, $to_date];

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$income_records = $stmt->fetchAll();

// CSV Export Logic
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="income_report_' . $from_date . '_to_' . $to_date . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'Source', 'Amount', 'Note']);
    foreach ($income_records as $row) {
        fputcsv($output, [$row['date'], $row['source'], $row['amount'], $row['note']]);
    }
    fclose($output);
    exit;
}

$total_amount = array_sum(array_column($income_records, 'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Report - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .print-container { border: none; shadow: none; }
        }
    </style>
</head>
<body class="bg-slate-50">
    <main class="p-4 md:p-10">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4 no-print">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Income Report</h1>
                    <p class="text-slate-500 mt-1">Detailed breakdown of your earnings.</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="bg-white border border-slate-200 text-slate-700 px-6 py-2 rounded-xl font-bold hover:bg-slate-50 transition-all flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Print
                    </button>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-emerald-700 transition-all flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Export CSV
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm mb-8 no-print">
                <form class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">From Date</label>
                        <input type="date" name="from_date" value="<?php echo $from_date; ?>" class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">To Date</label>
                        <input type="date" name="to_date" value="<?php echo $to_date; ?>" class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-slate-900 text-white py-2 rounded-xl font-bold hover:bg-slate-800 transition-all">Generate Report</button>
                    </div>
                </form>
            </div>

            <!-- Report Content -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden print-container">
                <div class="p-10 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Income Statement</h2>
                        <p class="text-sm text-slate-500"><?php echo format_date($from_date); ?> - <?php echo format_date($to_date); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase">Total Earnings</p>
                        <p class="text-3xl font-bold text-emerald-600"><?php echo format_currency($total_amount); ?></p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="px-10 py-4">Date</th>
                                <th class="px-10 py-4">Source</th>
                                <th class="px-10 py-4">Note</th>
                                <th class="px-10 py-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($income_records)): ?>
                                <tr>
                                    <td colspan="4" class="px-10 py-20 text-center text-slate-400">No records found for this period.</td>
                                </tr>
                            <?php
else: ?>
                                <?php foreach ($income_records as $row): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-10 py-4 text-sm whitespace-nowrap"><?php echo format_date($row['date']); ?></td>
                                        <td class="px-10 py-4">
                                            <span class="px-3 py-1 bg-emerald-50 rounded-full text-xs font-bold text-emerald-600"><?php echo e($row['source']); ?></span>
                                        </td>
                                        <td class="px-10 py-4 text-sm text-slate-400 italic">
                                            <?php echo e(substr($row['note'], 0, 80)); ?>
                                            <?php echo strlen($row['note']) > 80 ? '...' : ''; ?>
                                        </td>
                                        <td class="px-10 py-4 text-right font-bold text-emerald-600">+<?php echo format_currency($row['amount']); ?></td>
                                    </tr>
                                <?php
    endforeach; ?>
                            <?php
endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-10 bg-slate-50/50 text-center hidden print:block">
                    <p class="text-xs text-slate-400">Computer generated report by Cashflow Diary by Ventory Stack</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
