<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

$search = $_GET['search'] ?? '';

// Fetch all contacts for this business with optional search
$query = "SELECT id, name, phone FROM contacts WHERE user_id = ? AND business_id = ?";
$params = [$user_id, $business_id];

if ($search) {
    $query .= " AND (name LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$contacts = $stmt->fetchAll();

$contact_balances = [];
$total_receivable = 0;
$total_payable = 0;

foreach ($contacts as $contact) {
    $stmt = $pdo->prepare("SELECT 
        SUM(CASE WHEN type='given' THEN amount ELSE 0 END) as given,
        SUM(CASE WHEN type='received' THEN amount ELSE 0 END) as received,
        SUM(CASE WHEN type='borrowed' THEN amount ELSE 0 END) as borrowed,
        SUM(CASE WHEN type='repaid' THEN amount ELSE 0 END) as repaid
        FROM udhaar_transactions WHERE contact_id = ?");
    $stmt->execute([$contact['id']]);
    $stats = $stmt->fetch();

    $net_given = ($stats['given'] ?? 0) - ($stats['received'] ?? 0);
    $net_borrowed = ($stats['borrowed'] ?? 0) - ($stats['repaid'] ?? 0);
    $balance = $net_given - $net_borrowed;

    if ($balance > 0)
        $total_receivable += $balance;
    if ($balance < 0)
        $total_payable += abs($balance);

    $contact_balances[] = [
        'id' => $contact['id'],
        'name' => $contact['name'],
        'phone' => $contact['phone'],
        'balance' => $balance
    ];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('udhaar_ledger', 'Udhaar Ledger'); ?> - <?php echo __('app_name', 'Cashflow Diary'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="p-4 md:p-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight"><?php echo __('udhaar_ledger', 'Udhaar Ledger'); ?></h1>
                <p class="text-slate-500 mt-1"><?php echo __('manage_credit_debt', 'Manage your credit and debt transactions.'); ?></p>
            </div>
            
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto items-end">
                <form class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="<?php echo __('search_contact', 'Search contact...'); ?>" class="px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm w-full">
                    <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-xl font-semibold hover:bg-amber-600 transition-all text-sm"><?php echo __('search', 'Search'); ?></button>
                </form>

                <div class="flex <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-4 w-full md:w-auto">
                    <div class="flex-1 md:flex-none bg-emerald-50 p-4 px-6 rounded-2xl border border-emerald-100">
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest leading-none mb-1"><?php echo __('receivable', 'Receivable'); ?></p>
                        <p class="text-xl font-bold text-emerald-700"><?php echo format_currency($total_receivable); ?></p>
                    </div>
                    <div class="flex-1 md:flex-none bg-rose-50 p-4 px-6 rounded-2xl border border-rose-100">
                        <p class="text-[10px] font-bold text-rose-600 uppercase tracking-widest leading-none mb-1"><?php echo __('payable', 'Payable'); ?></p>
                        <p class="text-xl font-bold text-rose-700"><?php echo format_currency($total_payable); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacts Ledger List -->
        <div class="space-y-4">
            <?php if (empty($contact_balances)): ?>
                <div class="bg-white rounded-[2rem] p-20 text-center border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo __('no_udhaar_records', 'No Udhaar records yet'); ?></h3>
                    <p class="text-slate-500 mb-8"><?php echo __('start_by_adding_contact', 'Start by adding a contact or a transaction.'); ?></p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="../contacts/add.php" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"><?php echo __('add_contact', 'Add Contact'); ?></a>
                        <a href="add.php" class="bg-amber-500 text-white px-8 py-4 rounded-2xl font-bold hover:bg-amber-600 transition-all shadow-lg shadow-amber-100"><?php echo __('add_transaction', 'Add Transaction'); ?></a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($contact_balances as $contact): ?>
                        <a href="view.php?id=<?php echo $contact['id']; ?>" class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 hover:border-amber-200 hover:shadow-2xl hover:-translate-y-1 transition-all group flex flex-col justify-between h-56 relative overflow-hidden">
                            <!-- Background Accent -->
                            <div class="absolute <?php echo get_lang_dir() === 'rtl' ? '-left-10' : '-right-10'; ?> -top-10 w-40 h-40 <?php echo $contact['balance'] >= 0 ? 'bg-emerald-50' : 'bg-rose-50'; ?> rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
                            
                            <div class="relative">
                                <div class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 text-slate-600 rounded-2xl flex items-center justify-center font-bold text-xl group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                        <?php echo strtoupper(substr($contact['name'], 0, 1)); ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-amber-600 transition-colors truncate"><?php echo e($contact['name']); ?></h3>
                                        <p class="text-xs text-slate-500 truncate"><?php echo e($contact['phone'] ?: __('no_phone_provided', 'No phone provided')); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative pt-6 border-t border-slate-50 flex justify-between items-end<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
                                <div class="<?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1"><?php echo $contact['balance'] >= 0 ? __('receivable', 'Receivable') : __('payable', 'Payable'); ?></p>
                                    <p class="text-2xl font-bold <?php echo $contact['balance'] >= 0 ? 'text-emerald-600' : 'text-rose-600'; ?>">
                                        <?php echo format_currency(abs($contact['balance'])); ?>
                                    </p>
                                </div>
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-amber-50 group-hover:text-amber-600 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform <?php echo get_lang_dir() === 'rtl' ? 'group-hover:-translate-x-1 rotate-180' : 'group-hover:translate-x-1'; ?> transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Floating Action Button -->
    <a href="add.php" class="fixed bottom-10 <?php echo get_lang_dir() === 'rtl' ? 'left-10' : 'right-10'; ?> w-16 h-16 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-2xl shadow-amber-200 hover:bg-amber-600 transform hover:scale-110 active:scale-95 transition-all z-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
    </a>
</body>
</html>
