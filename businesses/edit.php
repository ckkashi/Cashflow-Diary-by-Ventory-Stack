<?php
require_once '../includes/navbar.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$business_id = $_GET['id'] ?? null;

if (!$business_id) {
    header('Location: list.php');
    exit;
}

// Fetch business details
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = ? AND user_id = ?");
$stmt->execute([$business_id, $user_id]);
$business = $stmt->fetch();

if (!$business) {
    header('Location: list.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    if (isset($_POST['update'])) {
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? 'Personal';
        $description = $_POST['description'] ?? '';

        if ($name) {
            $stmt = $pdo->prepare("UPDATE businesses SET name = ?, type = ?, description = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$name, $type, $description, $business_id, $user_id])) {
                redirect_with('list.php', 'Business updated successfully!');
            }
            else {
                $error = 'Failed to update business.';
            }
        }
        else {
            $error = 'Please provide a business name.';
        }
    }
    elseif (isset($_POST['delete'])) {
        // Prevent deleting the only business
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM businesses WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() <= 1) {
            $error = 'You must have at least one business.';
        }
        else {
            $stmt = $pdo->prepare("DELETE FROM businesses WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$business_id, $user_id])) {
                // If current business was deleted, switch to another one
                if ($_SESSION['business_id'] == $business_id) {
                    $stmt = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ? LIMIT 1");
                    $stmt->execute([$user_id]);
                    $_SESSION['business_id'] = $stmt->fetchColumn();
                }
                header('Location: list.php');
                exit;
            }
            else {
                $error = 'Failed to delete business.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Business - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <main class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-800 p-8 text-white">
                <h1 class="text-2xl font-bold">Edit Business</h1>
                <p class="text-slate-400 mt-1">Update details for <?php echo e($business['name']); ?>.</p>
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
                        <input type="text" name="name" value="<?php echo e($business['name']); ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                        <select name="type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                            <option value="Personal" <?php echo $business['type'] == 'Personal' ? 'selected' : ''; ?>>Personal</option>
                            <option value="Shop" <?php echo $business['type'] == 'Shop' ? 'selected' : ''; ?>>Shop</option>
                            <option value="Freelance" <?php echo $business['type'] == 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
                            <option value="Business" <?php echo $business['type'] == 'Business' ? 'selected' : ''; ?>>Business</option>
                            <option value="Other" <?php echo $business['type'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optional)</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"><?php echo e($business['description']); ?></textarea>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="submit" name="update" class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-semibold shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-100">
                    <h3 class="text-rose-600 font-bold mb-2">Danger Zone</h3>
                    <p class="text-slate-500 text-sm mb-6">Deleting a business will remove all its related income, expenses, and data. This action cannot be undone.</p>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this business? All related data will be lost.');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" name="delete" class="w-full bg-rose-50 text-rose-600 border border-rose-100 py-3 rounded-xl font-semibold hover:bg-rose-100 transition-all">
                            Delete Business
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
