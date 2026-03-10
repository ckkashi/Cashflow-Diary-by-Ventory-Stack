<?php
require_once __DIR__ . '/../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$business_id = $_SESSION['business_id'] ?? null;

if (!$business_id) {
    header('Location: ../businesses/list.php');
    exit;
}

$search = $_GET['search'] ?? '';

// Build Query
$query = "SELECT * FROM contacts WHERE user_id = ? AND business_id = ?";
$params = [$user_id, $business_id];

if ($search) {
    $query .= " AND (name LIKE ? OR phone LIKE ? OR address LIKE ? OR note LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$contacts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo get_lang_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('contacts', 'Contacts'); ?> - <?php echo __('app_name', 'Cashflow Diary'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <main class="p-4 md:p-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight"><?php echo __('contacts', 'Contacts'); ?></h1>
                <p class="text-slate-500 mt-1"><?php echo __('total', 'Total'); ?>: <span class="font-bold text-indigo-600"><?php echo count($contacts); ?></span></p>
            </div>
            
            <form class="flex gap-3 w-full md:w-auto<?php echo get_lang_dir() === 'rtl' ? ' flex-row-reverse' : ''; ?>">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="<?php echo __('search_name_phone', 'Search name, phone...'); ?>" class="w-full md:w-64 px-4 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-indigo-700 transition-all text-sm"><?php echo __('search', 'Search'); ?></button>
            </form>
        </div>

        <!-- Contacts Grid -->
        <div class="space-y-4">
            <?php if (empty($contacts)): ?>
                <div class="bg-white rounded-[2rem] p-20 text-center border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo __('no_contacts_found', 'No contacts found'); ?></h3>
                    <p class="text-slate-500"><?php echo __('add_people_desc', 'Add people involved in your business transactions.'); ?></p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($contacts as $contact): ?>
                        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 hover:border-indigo-200 transition-all group relative overflow-hidden">
                            <div class="flex items-center <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-4 mb-6">
                                <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl font-bold shrink-0">
                                    <?php echo strtoupper(substr($contact['name'], 0, 1)); ?>
                                </div>
                                <div class="truncate <?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>">
                                    <h3 class="font-bold text-slate-900 text-lg truncate"><?php echo htmlspecialchars($contact['name']); ?></h3>
                                    <?php if ($contact['phone']): ?>
                                        <p class="text-sm text-slate-500" dir="ltr"><?php echo htmlspecialchars($contact['phone']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($contact['address']): ?>
                                <div class="flex items-start <?php echo get_lang_dir() === 'rtl' ? 'space-x-reverse' : ''; ?> space-x-2 text-slate-500 text-sm mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span class="<?php echo get_lang_dir() === 'rtl' ? 'text-right' : ''; ?>"><?php echo htmlspecialchars($contact['address']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between items-center mt-6 pt-6 border-t border-slate-100 <?php echo get_lang_dir() === 'rtl' ? 'flex-row-reverse' : ''; ?>">
                                <a href="edit.php?id=<?php echo $contact['id']; ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors"><?php echo __('edit_contact', 'Edit Contact'); ?></a>
                                <a href="../udhaar/list.php?contact_id=<?php echo $contact['id']; ?>" class="bg-slate-50 text-slate-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-100"><?php echo __('view_udhaar', 'View Udhaar'); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Floating Action Button -->
    <a href="add.php" class="fixed <?php echo get_lang_dir() === 'rtl' ? 'left-10' : 'right-10'; ?> bottom-10 w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center shadow-2xl shadow-indigo-200 hover:bg-indigo-700 transform hover:scale-110 active:scale-95 transition-all z-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
    </a>
</body>
</html>
