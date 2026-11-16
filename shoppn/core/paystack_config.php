<?php

/*
 * core/paystack_config.php
 *
 * Configuration for Paystack Payment Integration .
 *

 */

// Paystack Public Key (safe for client-side JavaScript)
if (!defined('PAYSTACK_PUBLIC_KEY')) {
    define(
        'PAYSTACK_PUBLIC_KEY',
        getenv('PAYSTACK_PUBLIC_KEY') ?: 'your public key '
    );
}

// Paystack Secret Key (SERVER-SIDE ONLY - never output in HTML or JS)
if (!defined('PAYSTACK_SECRET_KEY')) {
    define(
        'PAYSTACK_SECRET_KEY',
        getenv('PAYSTACK_SECRET_KEY') ?: 'your private key'
    );
}

// Standard currency for ShopPN
if (!defined('PAYSTACK_CURRENCY')) {
    define('PAYSTACK_CURRENCY', 'GHS');
}
