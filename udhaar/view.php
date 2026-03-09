<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;
$contact_id = $_GET['id'] ?? null;

if (!$business_id || !$contact_id) {
    header('Location: list.php');
    exit;
}

// Fetch Contact
$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ? AND business_id = ?");
$stmt->execute([$contact_id, $user_id, $business_id]);
$contact = $stmt->fetch();

if (!$contact) {
    header('Location: list.php');
    exit;
}
authorize_user($contact['user_id'], $contact['business_id']);

// Fetch Balance Stats
$stmt = $pdo->prepare("SELECT 
    SUM(CASE WHEN type='given' THEN amount ELSE 0 END) as given,
    SUM(CASE WHEN type='received' THEN amount ELSE 0 END) as received,
    SUM(CASE WHEN type='borrowed' THEN amount ELSE 0 END) as borrowed,
    SUM(CASE WHEN type='repaid' THEN amount ELSE 0 END) as repaid
    FROM udhaar_transactions WHERE contact_id = ?");
$stmt->execute([$contact_id]);
$stats = $stmt->fetch();

$net_given = ($stats['given'] ?? 0) - ($stats['received'] ?? 0);
$net_borrowed = ($stats['borrowed'] ?? 0) - ($stats['repaid'] ?? 0);
$current_balance = $net_given - $net_borrowed;

// Fetch Transactions
$stmt = $pdo->prepare("SELECT * FROM udhaar_transactions WHERE contact_id = ? ORDER BY date DESC, created_at DESC");
$stmt->execute([$contact_id]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($contact['name']); ?> - Ledger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <!-- Contact Header -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden mb-10">
            <div class="bg-slate-900 p-8 text-white flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center space-x-6 text-center md:text-left">
                    <div class="w-20 h-20 bg-slate-800 text-white border-2 border-slate-700/50 rounded-3xl flex items-center justify-center text-3xl font-bold">
                        <?php echo strtoupper(substr($contact['name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($contact['name']); ?></h1>
                        <p class="text-slate-400"><?php echo htmlspecialchars($contact['phone'] ?: 'No phone number'); ?></p>
                        <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-bold">Ledger Overview</p>
                    </div>
                </div>
                
                <div class="text-center md:text-right">
                    <p class="text-sm font-bold opacity-50 uppercase tracking-widest">Net Balance</p>
                    <p class="text-4xl font-bold <?php echo $current_balance >= 0 ? 'text-emerald-400' : 'text-rose-400'; ?>">
                        <?php echo format_currency(abs($current_balance)); ?>
                    </p>
                    <p class="text-xs <?php echo $current_balance >= 0 ? 'text-emerald-400/60' : 'text-rose-400/60'; ?> font-bold">
                        <?php echo $current_balance >= 0 ? 'YOU WILL RECEIVE' : 'YOU NEED TO PAY'; ?>
                    </p>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 border-t border-slate-100 bg-slate-50/50">
                <div class="p-6 border-r border-slate-100 text-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">I Gave</p>
                    <p class="text-lg font-bold text-slate-700"><?php echo format_currency($stats['given'] ?? 0); ?></p>
                </div>
                <div class="p-6 border-r border-slate-100 text-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Borrowed</p>
                    <p class="text-lg font-bold text-slate-700"><?php echo format_currency($stats['borrowed'] ?? 0); ?></p>
                </div>
                <div class="p-6 border-r border-slate-100 text-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Received</p>
                    <p class="text-lg font-bold text-slate-700"><?php echo format_currency($stats['received'] ?? 0); ?></p>
                </div>
                <div class="p-6 text-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Repaid</p>
                    <p class="text-lg font-bold text-slate-700"><?php echo format_currency($stats['repaid'] ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800">History</h3>
                <a href="add.php?contact_id=<?php echo $contact_id; ?>" class="bg-amber-500 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-amber-600 shadow-md">Add Activity</a>
            </div>

            <div class="space-y-4">
                <?php if (empty($transactions)): ?>
                    <div class="bg-white p-20 text-center rounded-[2rem] border border-slate-200">
                        <p class="text-slate-400">No transactions recorded yet.</p>
                    </div>
                <?php
else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center justify-between hover:border-indigo-100 transition-all">
                            <div class="flex items-center space-x-6">
                                <div class="w-12 h-12 flex items-center justify-center rounded-2xl text-xl font-bold
                                    <?php
        if ($tx['type'] == 'given')
            echo 'bg-blue-50 text-blue-600';
        elseif ($tx['type'] == 'received')
            echo 'bg-emerald-50 text-emerald-600';
        elseif ($tx['type'] == 'borrowed')
            echo 'bg-amber-50 text-amber-600';
        else
            echo 'bg-rose-50 text-rose-600';
?>">
                                    <?php
        if ($tx['type'] == 'given')
            echo 'G';
        elseif ($tx['type'] == 'received')
            echo 'RC';
        elseif ($tx['type'] == 'borrowed')
            echo 'B';
        else
            echo 'RP';
?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 capitalize"><?php echo e($tx['type']); ?></h4>
                                    <p class="text-sm text-slate-500"><?php echo format_date($tx['date']); ?></p>
                                    <?php if ($tx['note']): ?>
                                        <p class="text-xs text-slate-400 mt-1 italic"><?php echo e($tx['note']); ?></p>
                                    <?php
        endif; ?>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold <?php echo($tx['type'] == 'given' || $tx['type'] == 'repaid') ? 'text-slate-900' : 'text-indigo-600'; ?>">
                                    <?php echo format_currency($tx['amount']); ?>
                                </p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Transaction</p>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                <?php
endif; ?>
            </div>
        </div>

        <div class="mt-10 pt-10 border-t border-slate-200 text-center">
            <a href="list.php" class="text-slate-500 font-bold hover:text-indigo-600">← Back to Ledger Overview</a>
        </div>
    </main>
</body>
</html>
