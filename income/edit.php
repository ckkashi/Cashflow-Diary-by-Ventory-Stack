<?php
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;
$id = $_GET['id'] ?? null;

if (!$business_id || !$id) {
    header('Location: list.php');
    exit;
}

// Fetch Income
$stmt = $pdo->prepare("SELECT * FROM income WHERE id = ? AND user_id = ? AND business_id = ?");
$stmt->execute([$id, $user_id, $business_id]);
$income = $stmt->fetch();

if (!$income) {
    header('Location: list.php');
    exit;
}
authorize_user($income['user_id'], $income['business_id']);

$error = '';
$success = '';

$sources = ['Salary', 'Business', 'Freelance', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    if (isset($_POST['update'])) {
        $amount = $_POST['amount'] ?? 0;
        $source = $_POST['source'] ?? 'Business';
        $date = $_POST['date'] ?? date('Y-m-d');
        $note = $_POST['note'] ?? '';

        if ($amount > 0) {
            $stmt = $pdo->prepare("UPDATE income SET amount = ?, source = ?, date = ?, note = ? WHERE id = ? AND user_id = ? AND business_id = ?");
            if ($stmt->execute([$amount, $source, $date, $note, $id, $user_id, $business_id])) {
                redirect_with('list.php', 'Income updated successfully!');
            }
            else {
                $error = 'Failed to update income.';
            }
        }
        else {
            $error = 'Please enter a valid amount.';
        }
    }
    elseif (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM income WHERE id = ? AND user_id = ? AND business_id = ?");
        if ($stmt->execute([$id, $user_id, $business_id])) {
            redirect_with('list.php', 'Income deleted successfully!');
        }
        else {
            $error = 'Failed to delete income.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Income - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-800 p-8 text-white">
                <h1 class="text-2xl font-bold">Edit Income</h1>
                <p class="text-slate-400 mt-1">Modify record for <?php echo htmlspecialchars($income['source']); ?>.</p>
            </div>
            
            <div class="p-8">
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php
endif; ?>

                <?php if ($success): ?>
                    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 text-sm border border-emerald-100">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php
endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Amount</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                                <input type="number" step="0.01" name="amount" value="<?php echo $income['amount']; ?>" required class="w-full pl-10 pr-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all text-xl font-bold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Date</label>
                            <input type="date" name="date" value="<?php echo $income['date']; ?>" required class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Source</label>
                        <select name="source" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all font-medium">
                            <?php foreach ($sources as $src): ?>
                                <option value="<?php echo $src; ?>" <?php echo $income['source'] == $src ? 'selected' : ''; ?>><?php echo $src; ?></option>
                            <?php
endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Note (Optional)</label>
                        <textarea name="note" rows="3" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"><?php echo htmlspecialchars($income['note']); ?></textarea>
                    </div>

                    <div class="flex space-x-4 pt-6">
                        <button type="submit" name="update" class="flex-1 bg-emerald-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 transform hover:-translate-y-0.5 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-100">
                    <h3 class="text-rose-600 font-bold mb-2">Danger Zone</h3>
                    <p class="text-slate-500 text-sm mb-6">Deleting this record is permanent.</p>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this income record?');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" name="delete" class="w-full bg-rose-50 text-rose-600 border border-rose-100 py-3 rounded-2xl font-bold hover:bg-rose-100 transition-all">
                            Delete Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
