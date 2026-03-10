<?php
require_once __DIR__ . '/../includes/db.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /cashflow/dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Set default business_id
            $stmt = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ? ORDER BY id ASC LIMIT 1");
            $stmt->execute([$user['id']]);
            $business = $stmt->fetch();
            if ($business) {
                $_SESSION['business_id'] = $business['id'];
            }

            header('Location: /cashflow/dashboard');
            exit;
        }
        else {
            $error = 'Invalid email or password.';
        }
    }
    else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('login', 'Login'); ?> - <?php echo __('app_name', 'Cashflow Diary'); ?></title>
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
        <div class="text-center mb-12">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl shadow-indigo-200 mx-auto mb-8">C</div>
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight"><?php echo __('welcome', 'Welcome'); ?></h1>
            <p class="text-slate-500 mt-3 font-medium"><?php echo __('sign_in_to_account', 'Log in to your account'); ?></p>
        </div>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-600 p-5 rounded-2xl mb-8 text-sm font-bold border border-rose-100 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?php echo get_lang_dir() === 'rtl' ? 'ml-3' : 'mr-3'; ?>" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-8">
            <?php echo csrf_field(); ?>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"><?php echo __('email', 'Email'); ?></label>
                <input type="email" name="email" required placeholder="name@example.com" class="w-full px-6 py-4 rounded-2xl border-2 border-transparent bg-slate-100/50 focus:bg-white focus:border-indigo-600 focus:outline-none transition-all font-medium text-slate-800 placeholder:text-slate-300">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"><?php echo __('password', 'Password'); ?></label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-6 py-4 rounded-2xl border-2 border-transparent bg-slate-100/50 focus:bg-white focus:border-indigo-600 focus:outline-none transition-all font-medium text-slate-800 placeholder:text-slate-300">
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-bold shadow-2xl shadow-slate-200 hover:bg-indigo-600 transform hover:-translate-y-1 transition-all">
                <?php echo __('sign_in', 'Sign In'); ?>
            </button>
        </form>

        <div class="mt-12 text-center">
            <p class="text-sm font-bold text-slate-400">
                <?php echo __('new_here', 'New here?'); ?> 
                <a href="/cashflow/register" class="text-indigo-600 hover:text-indigo-700 underline underline-offset-4 decoration-2 decoration-indigo-100 hover:decoration-indigo-600 transition-all"><?php echo __('create_account', 'Create Account'); ?></a>
            </p>
        </div>
    </div>
</body>
</html>
