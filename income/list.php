<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Build Query
$query = "SELECT * FROM income WHERE user_id = ? AND business_id = ?";
$params = [$user_id, $business_id];

if ($from_date) {
    $query .= " AND date >= ?";
    $params[] = $from_date;
}

if ($to_date) {
    $query .= " AND date <= ?";
    $params[] = $to_date;
}

$query .= " ORDER BY date DESC, created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$income_records = $stmt->fetchAll();

// Calculate total
$total_income = array_sum(array_column($income_records, 'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="p-4 md:p-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Income</h1>
                <p class="text-slate-500 mt-1">Total: <span class="font-bold text-emerald-600">$<?php echo number_format($total_income, 2); ?></span></p>
            </div>
            
            <form class="flex flex-wrap gap-3 w-full md:w-auto">
                <input type="date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <input type="date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-indigo-700 transition-all text-sm">Filter</button>
                <?php if ($from_date || $to_date): ?>
                    <a href="list.php" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-xl font-semibold hover:bg-slate-300 transition-all text-sm">Clear</a>
                <?php
endif; ?>
            </form>
        </div>

        <!-- Income List -->
        <div class="space-y-4">
            <?php if (empty($income_records)): ?>
                <div class="bg-white rounded-[2rem] p-20 text-center border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No income recorded</h3>
                    <p class="text-slate-500">Add your first income transaction to see it here.</p>
                </div>
            <?php
else: ?>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($income_records as $record): ?>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center justify-between hover:border-emerald-200 transition-all group">
                            <div class="flex items-center space-x-6">
                                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl font-bold shrink-0">
                                    <?php echo substr($record['source'], 0, 1); ?>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($record['source']); ?></h3>
                                    <div class="flex items-center space-x-3 text-sm text-slate-500 mt-1">
                                        <span><?php echo date('M d, Y', strtotime($record['date'])); ?></span>
                                    </div>
                                    <?php if ($record['note']): ?>
                                        <p class="text-xs text-slate-400 mt-2 italic"><?php echo htmlspecialchars($record['note']); ?></p>
                                    <?php
        endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6">
                                <span class="text-xl font-bold text-emerald-600">+$<?php echo number_format($record['amount'], 2); ?></span>
                                <div class="flex space-x-2">
                                    <a href="edit.php?id=<?php echo $record['id']; ?>" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                </div>
            <?php
endif; ?>
        </div>
    </main>

    <!-- Floating Action Button -->
    <a href="add.php" class="fixed bottom-10 right-10 w-16 h-16 bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-2xl shadow-emerald-200 hover:bg-emerald-700 transform hover:scale-110 active:scale-95 transition-all z-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
    </a>
</body>
</html>
