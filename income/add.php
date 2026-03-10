<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

$error = '';
$success = '';

$sources = ['Salary', 'Business', 'Freelance', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $amount = $_POST['amount'] ?? 0;
    $source = $_POST['source'] ?? 'Business';
    $date = $_POST['date'] ?? date('Y-m-d');
    $note = $_POST['note'] ?? '';

    if ($amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO income (user_id, business_id, amount, source, date, note) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $business_id, $amount, $source, $date, $note])) {
            redirect_with('list.php', 'Income added successfully!');
        }
        else {
            $error = 'Failed to add income.';
        }
    }
    else {
        $error = 'Please enter a valid amount.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Income - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-emerald-600 p-8 text-white">
                <h1 class="text-2xl font-bold">New Income</h1>
                <p class="text-emerald-100 mt-1">Record a new earning for your ledger.</p>
            </div>
            
            <div class="p-8">
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100">
                        <?php echo e($error); ?>
                    </div>
                <?php
endif; ?>

                <?php if ($success): ?>
                    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 text-sm border border-emerald-100">
                        <?php echo e($success); ?>
                    </div>
                <?php
endif; ?>

                <form method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Amount</label>
                            <div class="relative">
                                <span class="absolute <?php echo get_lang_dir() === 'rtl' ? 'right-4' : 'left-4'; ?> top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                                <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full pl-10 pr-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all text-xl font-bold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Date</label>
                            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Source</label>
                        <select name="source" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all font-medium">
                            <?php foreach ($sources as $src): ?>
                                <option value="<?php echo $src; ?>"><?php echo $src; ?></option>
                            <?php
endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Note (Optional)</label>
                        <textarea name="note" rows="3" placeholder="Additional details..." class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"></textarea>
                    </div>

                    <div class="flex space-x-4 pt-6">
                        <a href="list.php" class="flex-1 text-center py-4 rounded-2xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-emerald-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 transform hover:-translate-y-0.5 transition-all">
                            Save Income
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
