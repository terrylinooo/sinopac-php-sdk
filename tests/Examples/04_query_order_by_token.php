<?php

defined('PHPUNIT_TEST') || exit('Example code is not allowed to access.');

/**
 * Example 04. Query a single order's information by using its PayToken.
 */
include __DIR__ . '/../../autoload.php';

$qpay = new \Sinopac\QPay();
$qpay->setShopNo('NA0249_001');
$qpay->setKeyId('b5e6986d-8636-4aa0-8c93-441ad14b2098');
$qpay->setFirstHashPair('86D50DEF3EB7400E', '01FD27C09E5549E5');
$qpay->setSecondHashPair('9E004965F4244953', '7FB3385F414E4F91');
$qpay->enableSandbox();

$token = 'da1547c3d0d1649af5049125b0880c0e227f31e107cbf4f0995bed28d0f066c1';
$results = $qpay->queryOrderByToken($token);

if (!empty($results['Message'])) {
    // @phpcs:disable
    print_r($results['Message']);
}
