<?php
/**
 * Password hashing helpers.
 *
 * The legacy database stores MD5 password hashes. We transparently upgrade
 * each account to a modern algorithm (bcrypt via PASSWORD_DEFAULT) the first
 * time the user logs in successfully, without forcing a password reset.
 *
 * Flow in a login handler:
 *   1. Fetch the stored hash by email/username (prepared statement).
 *   2. frs_password_verify($plain, $stored) — works for both MD5 and bcrypt.
 *   3. If frs_password_needs_rehash($stored), UPDATE the column with
 *      frs_password_hash($plain).
 */

/** Hash a plaintext password for storage (bcrypt). */
function frs_password_hash(string $plain): string
{
    return password_hash($plain, PASSWORD_DEFAULT);
}

/** Verify a plaintext password against a stored hash (MD5 legacy or bcrypt). */
function frs_password_verify(string $plain, string $stored): bool
{
    // Legacy MD5 hashes are exactly 32 hexadecimal characters.
    if (preg_match('/^[a-f0-9]{32}$/i', $stored)) {
        return hash_equals(strtolower($stored), md5($plain));
    }
    return password_verify($plain, $stored);
}

/** Whether a stored hash should be upgraded to the current algorithm. */
function frs_password_needs_rehash(string $stored): bool
{
    if (preg_match('/^[a-f0-9]{32}$/i', $stored)) {
        return true; // any legacy MD5 hash
    }
    return password_needs_rehash($stored, PASSWORD_DEFAULT);
}
