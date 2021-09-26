<?php

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
]);

// Method 2.

$qpay = new \Sinopac\QPay();
$qpay->setShopNo('NA0249_001');
$qpay->setFirstHashPair('86D50DEF3EB7400E', '01FD27C09E5549E5');
$qpay->setSecondHashPair('9E004965F4244953', '7FB3385F414E4F91');

