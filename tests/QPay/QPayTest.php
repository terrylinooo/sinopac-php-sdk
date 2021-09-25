<?php

namespace Sinopac\Test\QPay;

use PHPUnit\Framework\TestCase;
use function get_qpay_instance;

class QPayTest extends TestCase
{
    public function test_method_createOrderByCreditCard()
    {
        $qpay = get_qpay_instance();

        //$qpay->createOrderByCreditCard($data);
    }

    public function test_method_createOrderByATM()
    {
        $data = [
            'shop_no'          => get_testing_shop_no(),
            'order_no'         => get_testing_order_no(),
            'amount'           => 50000,
            'atm_expired_date' => get_testing_expired_date(),
            'product_name'     => '虛擬帳號訂單',
            'return_url'       => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
            'backend_url'      => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
        ];

        $qpay = get_qpay_instance();
        $results = $qpay->createOrderByATM($data);

        if (empty($results['APIService'])) {
            $this->assertTrue(false);
        } else {
            $this->assertSame('OrderCreate', $results['APIService']);
        }
    }

    public function test_method_queryOrders()
    {

    }

    public function test_method_queryOrderByToken()
    {

    }
}

