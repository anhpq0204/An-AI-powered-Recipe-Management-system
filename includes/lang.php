<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function _lang_set(string $lang): void {
    $lang = in_array($lang, ['vi', 'en']) ? $lang : 'vi';
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + 60 * 60 * 24 * 365, '/');
}

function lang_current(): string {
    if (isset($_GET['lang'])) {
        _lang_set($_GET['lang']);
    }
    if (isset($_SESSION['lang'])) return $_SESSION['lang'];
    if (isset($_COOKIE['lang'])) return $_COOKIE['lang'];
    return 'vi';
}

function lang_switcher_url(string $lang): string {
    $params = $_GET;
    $params['lang'] = $lang;
    unset($params['lang']); // remove first to re-add at end
    $base = strtok($_SERVER['REQUEST_URI'], '?');
    return $base . '?lang=' . $lang . (count($params) ? '&' . http_build_query($params) : '');
}

// Returns translation or falls back to the source string (English)
function __(string $text): string {
    static $translations = null;
    if ($translations === null) {
        $lang = lang_current();
        if ($lang === 'en') {
            $translations = [];
        } else {
            $file = __DIR__ . "/../lang/{$lang}.php";
            $translations = file_exists($file) ? require $file : [];
        }
    }
    return $translations[$text] ?? $text;
}

// Echo escaped translation
function _e(string $text): void {
    echo htmlspecialchars(__($text), ENT_QUOTES, 'UTF-8');
}
