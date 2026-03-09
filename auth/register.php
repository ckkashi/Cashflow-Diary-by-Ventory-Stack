<?php
require_once '../includes/db.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($name && $email && $password) {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        }
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $user_id = $pdo->lastInsertId();

                // Create default business
                $stmt = $pdo->prepare("INSERT INTO businesses (user_id, name, type) VALUES (?, 'Personal Finance', 'Personal')");
                $stmt->execute([$user_id]);

                $success = 'Successfully registered! You can now login.';
                header('refresh:2;url=login.php');
            }
            else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
    else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(236, 72, 153, 0.05), transparent);
        }
    </style>
</head>
<body class="bg-slate-50 hero-gradient min-h-screen flex items-center justify-center p-6 select-none">
    <div class="max-w-md w-full glass rounded-[3rem] shadow-2xl shadow-indigo-100/50 overflow-hidden p-10 md:p-12">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-indigo-200 mx-auto mb-8">C</div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Create Account</h1>
            <p class="text-slate-500 mt-2 font-medium">Join Cashflow Diary today</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-600 p-5 rounded-2xl mb-6 text-sm font-bold border border-rose-100 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-emerald-50 text-emerald-600 p-5 rounded-2xl mb-6 text-sm font-bold border border-emerald-100 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <?php echo e($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Full Name</label>
                <input type="text" name="name" required placeholder="John Doe" class="w-full px-6 py-4 rounded-2xl border-2 border-transparent bg-slate-100/50 focus:bg-white focus:border-indigo-600 focus:outline-none transition-all font-medium text-slate-800 placeholder:text-slate-300">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Email</label>
                <input type="email" name="email" required placeholder="name@example.com" class="w-full px-6 py-4 rounded-2xl border-2 border-transparent bg-slate-100/50 focus:bg-white focus:border-indigo-600 focus:outline-none transition-all font-medium text-slate-800 placeholder:text-slate-300">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-6 py-4 rounded-2xl border-2 border-transparent bg-slate-100/50 focus:bg-white focus:border-indigo-600 focus:outline-none transition-all font-medium text-slate-800 placeholder:text-slate-300">
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-bold shadow-2xl shadow-slate-200 hover:bg-indigo-600 transform hover:-translate-y-1 transition-all">
                Create Account
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-sm font-bold text-slate-400">
                Already have an account? 
                <a href="login.php" class="text-indigo-600 hover:text-indigo-700 underline underline-offset-4 decoration-2 decoration-indigo-100 hover:decoration-indigo-600 transition-all">Sign In</a>
            </p>
        </div>
    </div>
</body>
</html>
