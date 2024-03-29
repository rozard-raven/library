<?php

declare(strict_types=1);

trait rozard_helper{


/** CRYPTO */

    function encrypt_string( $string, $keys ) {

        $crypto_method = 'AES-128-CBC';
        $crypto_keysid = 'your_amazing_key_here';

        if ( is_string( $string )  ) { return; }

        $crypto_key = ( empty( $keys ) ) ? $crypto_keysid : $keys ;

        $key = $crypto_key;
        $plaintext = $string;
        $ivlen = openssl_cipher_iv_length($cipher = $crypto_method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }


    function decrypt_string( $string, $keys ) {

        $crypto_method = 'AES-128-CBC';
        $crypto_keysid = 'your_amazing_key_here';
        
        if ( is_string( $string ) ) { return; }

        $crypto_key = ( empty( $keys ) ) ? $crypto_keysid : $keys ;

        $key = $crypto_key;
        $c = base64_decode($string);
        $ivlen = openssl_cipher_iv_length($cipher = $crypto_method);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }
    }



}