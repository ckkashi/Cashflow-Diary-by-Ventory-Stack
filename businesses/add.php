<?php
require_once __DIR__ . '/../includes/navbar.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? 'Personal';
    $description = $_POST['description'] ?? '';
    $user_id = $_SESSION['user_id'];

    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO businesses (user_id, name, type, description) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $name, $type, $description])) {
            $business_id = $pdo->lastInsertId();
            $_SESSION['business_id'] = $business_id; // Automatically switch to new business
            redirect_with('list.php', 'Business created successfully!');
        }
        else {
            $error = 'Failed to create business.';
        }
    }
    else {
        $error = 'Please provide a business name.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Business - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <main class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-indigo-600 p-8 text-white">
                <h1 class="text-2xl font-bold">Add New Business</h1>
                <p class="text-indigo-100 mt-1">Create a separate ledger for your shop or project.</p>
            </div>
            
            <div class="p-8">
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100">
                        <?php echo e($error); ?>
                    </div>
                <?php
endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm border border-green-100">
                        <?php echo e($success); ?>
                    </div>
                <?php
endif; ?>

                <form method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Business Name</label>
                        <input type="text" name="name" placeholder="e.g. My Awesome Shop" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                        <select name="type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                            <option value="Personal">Personal</option>
                            <option value="Shop">Shop</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Business">Business</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optional)</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"></textarea>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <a href="list.php" class="flex-1 text-center py-4 rounded-xl font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-semibold shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all">
                            Create Business
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
