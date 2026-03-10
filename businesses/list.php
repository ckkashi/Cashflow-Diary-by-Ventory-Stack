<?php
require_once __DIR__ . '/../includes/navbar.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all businesses for this user
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$businesses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Businesses - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900">My Businesses</h1>
            <a href="add.php" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:bg-indigo-700 transition-all flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Business
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($businesses as $business): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-semibold rounded-full uppercase tracking-wider">
                            <?php echo e($business['type']); ?>
                        </span>
                        <div class="flex space-x-2">
                            <a href="edit.php?id=<?php echo $business['id']; ?>" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo e($business['name']); ?></h3>
                    <p class="text-slate-500 text-sm mb-6 line-clamp-2"><?php echo e($business['description'] ?: 'No description provided.'); ?></p>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <span class="text-xs text-slate-400">Created <?php echo format_date($business['created_at']); ?></span>
                        <a href="switch.php?id=<?php echo $business['id']; ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                            Switch to this →
                        </a>
                    </div>
                </div>
            <?php
endforeach; ?>
        </div>
    </main>
</body>
</html>
