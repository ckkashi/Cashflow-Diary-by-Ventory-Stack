<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

$search = $_GET['search'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Build Query
$query = "SELECT * FROM expenses WHERE user_id = ? AND business_id = ?";
$params = [$user_id, $business_id];

if ($search) {
    $query .= " AND (category LIKE ? OR note LIKE ? OR payment_method LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

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
$expenses = $stmt->fetchAll();

// Calculate total
$total_expenses = array_sum(array_column($expenses, 'amount'));
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('expenses', 'Expenses'); ?> - <?php echo __('app_name', 'Cashflow Diary'); ?></title>
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
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight"><?php echo __('expenses', 'Expenses'); ?></h1>
                <p class="text-slate-500 mt-1"><?php echo __('total', 'Total'); ?>: <span class="font-bold text-rose-600"><?php echo format_currency($total_expenses); ?></span></p>
            </div>
            
            <form class="flex flex-wrap gap-3 w-full md:w-auto<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="<?php echo __('search_category_note', 'Search category, note...'); ?>" class="px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <input type="date" name="from_date" value="<?php echo e($from_date); ?>" class="px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <input type="date" name="to_date" value="<?php echo e($to_date); ?>" class="px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-indigo-700 transition-all text-sm"><?php echo __('filter', 'Filter'); ?></button>
                <?php if ($search || $from_date || $to_date): ?>
                    <a href="list.php" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-xl font-semibold hover:bg-slate-300 transition-all text-sm"><?php echo __('clear', 'Clear'); ?></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Expenses List -->
        <div class="space-y-4">
            <?php if (empty($expenses)): ?>
                <div class="bg-white rounded-[2rem] p-20 text-center border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo __('no_expenses_found', 'No expenses found'); ?></h3>
                    <p class="text-slate-500"><?php echo __('add_your_first_expense', 'Add your first expense to see it here.'); ?></p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($expenses as $expense): ?>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center justify-between hover:border-rose-200 transition-all group<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
                            <div class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-6">
                                <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-xl font-bold shrink-0">
                                    <?php echo substr($expense['category'], 0, 1); ?>
                                </div>
                                <div class="<?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>">
                                    <h3 class="font-bold text-slate-900 group-hover:text-rose-600 transition-colors"><?php echo e($expense['category']); ?></h3>
                                    <div class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-3 text-sm text-slate-500 mt-1">
                                        <span><?php echo format_date($expense['date']); ?></span>
                                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                        <span><?php echo e($expense['payment_method']); ?></span>
                                    </div>
                                    <?php if ($expense['note']): ?>
                                        <p class="text-xs text-slate-400 mt-2 italic"><?php echo e($expense['note']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-6">
                                <span class="text-xl font-bold text-slate-900" dir="ltr">-<?php echo format_currency($expense['amount']); ?></span>
                                <div class="flex <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-2">
                                    <a href="edit.php?id=<?php echo $expense['id']; ?>" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Floating Action Button -->
    <a href="add.php" class="fixed <?php echo get_lang_dir() === 'rtl' ? 'left-10' : 'right-10'; ?> bottom-10 w-16 h-16 bg-rose-600 text-white rounded-full flex items-center justify-center shadow-2xl shadow-rose-200 hover:bg-rose-700 transform hover:scale-110 active:scale-95 transition-all z-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
    </a>
</body>
</html>
