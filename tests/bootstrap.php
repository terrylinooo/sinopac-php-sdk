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
    return 'TEST' . date('YmdHis');
}

function get_testing_expired_date()
{
    return date('Ymd', strtotime('+5 day'));
}