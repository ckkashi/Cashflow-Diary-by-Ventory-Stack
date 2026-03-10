<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

// Fetch Contacts for dropdown
$stmt = $pdo->prepare("SELECT id, name FROM contacts WHERE user_id = ? AND business_id = ? ORDER BY name ASC");
$stmt->execute([$user_id, $business_id]);
$contacts = $stmt->fetchAll();

$contact_id = $_GET['contact_id'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $contact_id = $_POST['contact_id'] ?? '';
    $type = $_POST['type'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $date = $_POST['date'] ?? date('Y-m-d');
    $note = $_POST['note'] ?? '';

    if ($contact_id && $type && $amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO udhaar_transactions (user_id, business_id, contact_id, type, amount, date, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $business_id, $contact_id, $type, $amount, $date, $note])) {
            redirect_with("view.php?id=$contact_id", 'Transaction recorded successfully!');
        }
        else {
            $error = 'Failed to record transaction.';
        }
    }
    else {
        $error = 'Please fill all required fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Udhaar - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-amber-500 p-8 text-white">
                <h1 class="text-2xl font-bold">New Udhaar Transaction</h1>
                <p class="text-amber-100 mt-1">Record money given, received, borrowed, or repaid.</p>
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
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Select Contact</label>
                        <select name="contact_id" required class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-all font-medium">
                            <option value="">-- Choose Person --</option>
                            <?php foreach ($contacts as $contact): ?>
                                <option value="<?php echo $contact['id']; ?>" <?php echo $contact_id == $contact['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($contact['name']); ?>
                                </option>
                            <?php
endforeach; ?>
                        </select>
                        <p class="mt-2 text-xs text-slate-400">Can't find someone? <a href="../contacts/add.php" class="text-amber-600 font-bold hover:underline">Add New Contact</a></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Transaction Type</label>
                            <select name="type" required class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-all font-medium">
                                <option value="">-- Select Type --</option>
                                <option value="given">Given (I gave money)</option>
                                <option value="received">Received (Someone returned)</option>
                                <option value="borrowed">Borrowed (I took money)</option>
                                <option value="repaid">Repaid (I returned money)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Amount</label>
                            <div class="relative">
                                <span class="absolute <?php echo get_lang_dir() === 'rtl' ? 'right-4' : 'left-4'; ?> top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                                <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full pl-10 pr-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-all text-xl font-bold">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Date</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-all font-medium">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Note (Optional)</label>
                        <textarea name="note" rows="3" placeholder="What was this for?" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:outline-none transition-all"></textarea>
                    </div>

                    <div class="flex space-x-4 pt-6">
                        <a href="list.php" class="flex-1 text-center py-4 rounded-2xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-amber-500 text-white py-4 rounded-2xl font-bold shadow-lg shadow-amber-100 hover:bg-amber-600 transform hover:-translate-y-0.5 transition-all">
                            Save Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
