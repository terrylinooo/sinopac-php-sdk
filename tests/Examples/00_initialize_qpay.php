<?php

defined('PHPUNIT_TEST') || exit('Example code is not allowed to access.');

/**
 * Example 00. Initialize QPay instance.
 */
include __DIR__ . '/../../autoload.php';

// Method 1.

$qpay = new \Sinopac\QPay([
    'shop_no' => 'NA0249_001',
    'hash' => [
        '86D50DEF3EB7400E',
        '01FD27C09E5549E5',
        '9E004965F4244953',
        '7FB3385F414E4F91',
    ],
    'key_id' => 'b5e6986d-8636-4aa0-8c93-441ad14b2098',
]);

// Method 2.

$qpay = new \Sinopac\QPay();
$qpay->setShopNo('NA0249_001');
$qpay->setKeyId('b5e6986d-8636-4aa0-8c93-441ad14b2098');
$qpay->setFirstHashPair('86D50DEF3EB7400E', '01FD27C09E5549E5');
$qpay->setSecondHashPair('9E004965F4244953', '7FB3385F414E4F91');

// Enabling sandbox mode will send API request to Sinopac's testing server.
$qpay->enableSandbox();
