<?php

defined('PHPUNIT_TEST') || exit('Example code is not allowed to access.');

/**
 * Example 03. Query a deluge of orders.
 */
include __DIR__ . '/../../autoload.php';

$qpay = new \Sinopac\QPay();
$qpay->setShopNo('NA0249_001');
$qpay->setKeyId('b5e6986d-8636-4aa0-8c93-441ad14b2098');
$qpay->setFirstHashPair('86D50DEF3EB7400E', '01FD27C09E5549E5');
$qpay->setSecondHashPair('9E004965F4244953', '7FB3385F414E4F91');
$qpay->enableSandbox();

$data = [
    'pay_type'             => 'A',
    'order_datetime_begin' => '202109250110',
    'order_datetime_end'   => '202109262359',
];

$results = $qpay->queryOrders($data);

if (!empty($results['Message'])) {
    // @phpcs:disable
    print_r($results['Message']);
}
