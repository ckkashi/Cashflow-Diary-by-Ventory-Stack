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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $note = $_POST['note'] ?? '';

    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO contacts (user_id, business_id, name, phone, address, note) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $business_id, $name, $phone, $address, $note])) {
            redirect_with('list.php', 'Contact added successfully!');
        }
        else {
            $error = 'Failed to add contact.';
        }
    }
    else {
        $error = 'Please provide at least a contact name.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contact - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-indigo-600 p-8 text-white">
                <h1 class="text-2xl font-bold">New Contact</h1>
                <p class="text-indigo-100 mt-1">Save information for people you deal with.</p>
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
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. John Doe" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-medium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Phone Number</label>
                            <input type="text" name="phone" placeholder="+1 234 567 890" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Address (Optional)</label>
                            <input type="text" name="address" placeholder="City, Country" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Note (Optional)</label>
                        <textarea name="note" rows="3" placeholder="Additional details..." class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"></textarea>
                    </div>

                    <div class="flex space-x-4 pt-6">
                        <a href="list.php" class="flex-1 text-center py-4 rounded-2xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all">
                            Save Contact
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
