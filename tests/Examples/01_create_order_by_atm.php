<?php

defined('PHPUNIT_TEST') || exit('Example code is not allowed to access.');

/**
 * Example 01. Create order by payment ATM (virtual account)
 */
include __DIR__ . '/../../autoload.php';

$qpay = new \Sinopac\QPay();
$qpay->setShopNo('NA0249_001');
$qpay->setKeyId('b5e6986d-8636-4aa0-8c93-441ad14b2098');
$qpay->setFirstHashPair('86D50DEF3EB7400E', '01FD27C09E5549E5');
$qpay->setSecondHashPair('9E004965F4244953', '7FB3385F414E4F91');
$qpay->enableSandbox();

$data = [
    'shop_no'          => 'NA0249_001',
    'order_no'         => 'TEST0000001',
    'amount'           => 50000,
    'atm_expired_date' => '20210930',
    'product_name'     => '虛擬帳號訂單',
    'return_url'       => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
    'backend_url'      => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
];

$results = $qpay->createOrderByATM($data);

if (!empty($results['Message'])) {
    // @phpcs:disable
    print_r($results['Message']);
}
