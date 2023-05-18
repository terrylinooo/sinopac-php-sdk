<?php

declare(strict_types=1);

date_default_timezone_set('UTC');

define('BOOTSTRAP_DIR', __DIR__);

function get_qpay_instance()
{
    $instance = new \Sinopac\QPay([
        'shop_no' => CONFIG_STORE_ID,
        'hash' => [
            CONFIG_HASH_A1,
            CONFIG_HASH_A2,
            CONFIG_HASH_B1,
            CONFIG_HASH_B2,
        ],
        'key_id' => 'b5e6986d-8636-4aa0-8c93-441ad14b2098',
    ]);

    $instance->enableSandbox();

    return $instance;
}

function get_testing_shop_no()
{
    return CONFIG_STORE_ID;
}

function get_testing_order_no()
{
    $randNumber = str_pad((string) rand(1, 999999), 6, '0', STR_PAD_LEFT);
    return 'T' . date('ymd') . $randNumber;
}

function get_testing_expired_date()
{
    return date('Ymd', strtotime('+5 day'));
}
