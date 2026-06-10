<?php
if (session_status() === PHP_SESSION_NONE) {
    $sessionLifetime = 604800; // 7 days

    // Only flag the cookie Secure when actually served over HTTPS, otherwise
    // the session cookie would be dropped on plain-HTTP local development.
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    ini_set("session.gc_maxlifetime", $sessionLifetime);
    ini_set("session.cookie_lifetime", $sessionLifetime);
    ini_set("session.use_strict_mode", "1");

    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,      // block JS access to the session cookie (XSS hardening)
        'samesite' => 'Lax',     // mitigate CSRF on cross-site requests
    ]);
    session_start();
}
