<?php
// security.php
// Lưu ý: KHÔNG commit file chứa key vào git. Tốt nhất load từ environment variables or config outside repo.

if (!defined('ENCRYPTION_KEY')) {
    // Nếu bạn dùng config/dbcon.php để define ENCRYPTION_KEY thì ok,
    // nếu không, bạn có thể define ở đây tạm: define('ENCRYPTION_KEY', '32_characters_min_required_key_here');
    // Better: set ENCRYPTION_KEY in config/dbcon.php from environment variable.
    // define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY'));
}

/**
 * Encrypt plaintext with AES-256-CBC. Returns base64(iv + ciphertext)
 * @param string|null $plaintext
 * @return string|null
 */
function encryptData($plaintext) {
    if ($plaintext === null || $plaintext === '') return $plaintext;

    $key = ENCRYPTION_KEY; // must be 32 bytes for AES-256
    if (empty($key) || strlen($key) < 32) {
        // throw or handle error in production; here we just return plaintext to avoid breaking site.
        error_log("ENCRYPTION_KEY not set or too short.");
        return $plaintext;
    }

    $cipher = "AES-256-CBC";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    // Store iv + ciphertext, base64 encode for safe storage in VARCHAR/TEXT
    $b64 = base64_encode($iv . $ciphertext_raw);
    return $b64;
}

/**
 * Decrypt base64(iv + ciphertext) -> plaintext
 * @param string|null $b64
 * @return string|null
 */
function decryptData($b64) {
    if ($b64 === null || $b64 === '') return $b64;

    $key = ENCRYPTION_KEY;
    if (empty($key) || strlen($key) < 32) {
        error_log("ENCRYPTION_KEY not set or too short.");
        return $b64;
    }

    $cipher = "AES-256-CBC";
    $ivlen = openssl_cipher_iv_length($cipher);
    $decoded = base64_decode($b64);
    if ($decoded === false || strlen($decoded) <= $ivlen) {
        // Not a ciphertext we produced; return as-is (backward compatibility)
        return $b64;
    }
    $iv = substr($decoded, 0, $ivlen);
    $ciphertext_raw = substr($decoded, $ivlen);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    if ($original_plaintext === false) {
        // if decrypt fails, return original base64 to avoid breaking UI
        return $b64;
    }
    return $original_plaintext;
}
