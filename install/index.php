<?php
session_start();

$config_path = dirname(__DIR__) . '/config/config.php';

// If already installed, don't allow access to install page
if (file_exists($config_path)) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';

    $admin_name = $_POST['admin_name'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';
    $admin_pass = $_POST['admin_pass'] ?? '';

    try {
        // 1. Connect to MySQL (without DB first to create it)
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Create Database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`;"); // Using 'USE' followed by 'text' as a placeholder or just USE. Wait, 'USE $db_name' is enough.

        // 3. Create Users Table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;";
        $pdo->exec($sql);

        // 4. Create Businesses Table
        $sql = "CREATE TABLE IF NOT EXISTS businesses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            type ENUM('Personal', 'Shop', 'Freelance', 'Business', 'Other') DEFAULT 'Personal',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        $pdo->exec($sql);

        // 5. Create Expenses Table
        $sql = "CREATE TABLE IF NOT EXISTS expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            business_id INT NOT NULL,
            amount DECIMAL(15,2) NOT NULL,
            category VARCHAR(50) NOT NULL,
            payment_method VARCHAR(50) NOT NULL,
            note TEXT,
            date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        $pdo->exec($sql);

        // 6. Create Income Table
        $sql = "CREATE TABLE IF NOT EXISTS income (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            business_id INT NOT NULL,
            amount DECIMAL(15,2) NOT NULL,
            source ENUM('Salary', 'Business', 'Freelance', 'Other') DEFAULT 'Business',
            note TEXT,
            date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        $pdo->exec($sql);

        // 7. Create Contacts Table
        $sql = "CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            business_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            note TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        $pdo->exec($sql);

        // 8. Create Udhaar Transactions Table
        $sql = "CREATE TABLE IF NOT EXISTS udhaar_transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            business_id INT NOT NULL,
            contact_id INT NOT NULL,
            type ENUM('given', 'received', 'borrowed', 'repaid') NOT NULL,
            amount DECIMAL(15,2) NOT NULL,
            note TEXT,
            date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
            FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        $pdo->exec($sql);

        // 9. Create Admin User
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$admin_name, $admin_email, $hashed_password]);
        $admin_user_id = $pdo->lastInsertId();

        // 6. Create Default Business for Admin
        $stmt = $pdo->prepare("INSERT INTO businesses (user_id, name, type) VALUES (?, 'Personal Finance', 'Personal')");
        $stmt->execute([$admin_user_id]);

        // 7. Generate config/config.php
        $config_content = "<?php
define('DB_HOST', '$db_host');
define('DB_NAME', '$db_name');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');

try {
    \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME, DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \$e) {
    die(\"Database connection failed: \" . \$e->getMessage());
}

define('SITE_NAME', 'Cashflow Diary');
define('APP_URL', 'http://' . \$_SERVER['HTTP_HOST'] . '/cashflow'); // Adjust if needed
?>";

        if (file_put_contents($config_path, $config_content)) {
            $success = "Installation successful! Redirecting to login...";
            header("refresh:2;url=../login.php");
        }
        else {
            $error = "Failed to write config file. Please check permissions.";
        }

    }
    catch (PDOException $e) {
        $error = "Connection failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - Cashflow Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full glass rounded-3xl shadow-2xl overflow-hidden">
        <div class="bg-indigo-600 p-8 text-white text-center">
            <h1 class="text-3xl font-bold">Cashflow Diary</h1>
            <p class="text-indigo-100 mt-2">Installation Wizard</p>
        </div>
        
        <div class="p-8">
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php
endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm border border-green-100">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php
endif; ?>

            <form method="POST" class="space-y-6">
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-slate-800 border-b pb-2">Database Settings</h2>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Database Host</label>
                        <input type="text" name="db_host" value="localhost" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Database Name</label>
                        <input type="text" name="db_name" placeholder="cashflow_db" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Database Username</label>
                        <input type="text" name="db_user" value="root" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Database Password</label>
                        <input type="password" name="db_pass" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-4 pt-4">
                    <h2 class="text-lg font-semibold text-slate-800 border-b pb-2">Admin Account</h2>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Full Name</label>
                        <input type="text" name="admin_name" placeholder="Admin" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Email Address</label>
                        <input type="email" name="admin_email" placeholder="admin@example.com" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Password</label>
                        <input type="password" name="admin_pass" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-semibold shadow-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all">
                    Install Now
                </button>
            </form>
        </div>
        
        <div class="bg-slate-50 p-4 text-center border-t border-slate-100">
            <p class="text-xs text-slate-400">Developed by Ventory Stack</p>
        </div>
    </div>
</body>
</html>
