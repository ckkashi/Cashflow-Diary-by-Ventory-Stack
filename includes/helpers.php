<?php
/**
 * Global Helper Functions for Cashflow Diary
 */

/**
 * Format currency with symbol
 */
function format_currency($amount)
{
    return '$' . number_format($amount, 2);
}

/**
 * Format date to a readable format
 */
function format_date($date)
{
    return date('M d, Y', strtotime($date));
}

/**
 * Redirect to a URL with a session message
 */
function redirect_with($url, $message, $type = 'success')
{
    $_SESSION[$type] = $message;
    header("Location: $url");
    exit;
}

/**
 * Get navigation items for the sidebar
 */
function get_nav_items()
{
    return [
        ['id' => 'dashboard', 'name' => __('dashboard', 'Dashboard'), 'url' => '../dashboard/index.php', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['id' => 'businesses', 'name' => __('businesses', 'Businesses'), 'url' => '../businesses/list.php', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ['id' => 'expenses', 'name' => __('expenses', 'Expenses'), 'url' => '../expenses/list.php', 'icon' => 'M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['id' => 'income', 'name' => __('income', 'Income'), 'url' => '../income/list.php', 'icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['id' => 'udhaar', 'name' => __('udhaar', 'Udhaar'), 'url' => '../udhaar/list.php', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
        ['id' => 'contacts', 'name' => __('contacts', 'Contacts'), 'url' => '../contacts/list.php', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['id' => 'reports', 'name' => __('reports', 'Reports'), 'url' => '../reports/index.php', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z']
    ];
}
/**
 * Translate a key into the current language
 */
function __($key, $default = null)
{
    global $lang_data;
    if (isset($lang_data[$key])) {
        return (string)$lang_data[$key];
    }
    return (string)($default !== null ? $default : $key);
}

/**
 * Get the current language direction (rtl/ltr)
 */
function get_lang_dir()
{
    return ($_SESSION['lang'] ?? 'en') === 'ur' ? 'rtl' : 'ltr';
}
