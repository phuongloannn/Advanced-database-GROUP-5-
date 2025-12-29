<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "fashion_shop_group5";

    $conn = mysqli_connect($host, $username, $password, $database);
    mysqli_set_charset($conn, 'utf8');

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    /* ==========================================================
        AES-256-ECB ENCRYPTION (Safe Mode)
        - Dùng cho: phone, address, street_address, postal_code
        - Không ảnh hưởng email/login
    ========================================================== */

    // 32 ký tự — bạn nên thay bằng key riêng của bạn
    $SECRET_KEY = "YOUR32CHARSAESENCRYPTIONKEY1234";

    function encryptData($plaintext)
    {
        global $SECRET_KEY;
        if ($plaintext === null || $plaintext === "") return $plaintext;
        return openssl_encrypt($plaintext, "AES-256-ECB", $SECRET_KEY);
    }

    function decryptData($ciphertext)
    {
        global $SECRET_KEY;
        if ($ciphertext === null || $ciphertext === "") return $ciphertext;
        return openssl_decrypt($ciphertext, "AES-256-ECB", $SECRET_KEY);
    }

?>
