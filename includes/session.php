<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set("session.gc_maxlifetime", 604800);
    ini_set("session.cookie_lifetime", 604800);
    session_set_cookie_params(604800);
    session_start();
}
