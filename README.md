# 永豐金 Sinopac PHP SDK

[![Build Status](https://app.travis-ci.com/terrylinooo/sinopac-php-sdk.svg?branch=master)](https://app.travis-ci.com/terrylinooo/sinopac-php-sdk)

Notice:

Starting from the end of April 2023, the Sinopac API requires the `X-KeyId` to be set in the header for authorization.

## Examples

### Initialize QPay instance
```php
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
```

### Create order by virtual account (ATM)

```php
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
    print_r($results['Message']);
}
```

### Create order by credit card

```php
$data = [
    'shop_no'                 => 'NA0249_001',
    'order_no'                => 'TEST0000002',
    'amount'                  => 50000,
    'cc_auto_billing'         => 'N',
    'cc_expired_billing_days' => 7,
    'cc_expired_minutes'      => 10,
    'product_name'            => '信用卡訂單',
    'return_url'              => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
    'backend_url'             => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
];

$results = $qpay->createOrderByCreditCard($data);

if (!empty($results['Message'])) {
    print_r($results['Message']);
}
```

### Query orders

```php
$data = [
    'pay_type'             => 'A',
    'order_datetime_begin' => '202109250110',
    'order_datetime_end'   => '202109262359'
];

$results = $qpay->queryOrders($data);

if (!empty($results['Message'])) {
    print_r($results['Message']);
}
```

### Query order status

```php
$token = 'da1547c3d0d1649af5049125b0880c0e227f31e107cbf4f0995bed28d0f066c1';
$results = $qpay->queryOrderByToken($token);

if (!empty($results['Message'])) {
    print_r($results['Message']);
}
```

This PHP library was contributed by [Terry Lin](https://terryl.in) and [Colocal](https://colocal.com). 
It is licensed under the MIT License.
Should you have any inquiries regarding this library, we kindly request you to open an issue and provide a detailed description of your question.