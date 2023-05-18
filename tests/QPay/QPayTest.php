<?php

namespace Sinopac\Test\QPay;

use PHPUnit\Framework\TestCase;
use function get_qpay_instance;

class QPayTest extends TestCase
{
    public function test_method_createOrderByCreditCard()
    {
        $data = [
            'shop_no'                 => get_testing_shop_no(),
            'order_no'                => get_testing_order_no(),
            'amount'                  => 50000,
            'cc_auto_billing'         => 'N',
            'cc_expired_billing_days' => 7,
            'cc_expired_minutes'      => 10,
            'product_name'            => '信用卡訂單',
            'return_url'              => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
            'backend_url'             => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
        ];

        $qpay = get_qpay_instance();
        $results = $qpay->createOrderByCreditCard($data);

        echo "\n";
        echo "測試建立信用卡訂單結果如下：\n";
        echo json_encode($results, JSON_PRETTY_PRINT);
        echo "\n";

        if (empty($results['APIService'])) {
            $this->assertTrue(false);
        } else {
            $this->assertSame('1.0.0', $results['Version']);
            $this->assertSame($data['shop_no'], $results['ShopNo']);
            $this->assertSame('OrderCreate', $results['APIService']);
            $this->assertNotEmpty($results['Sign']);
            $this->assertNotEmpty($results['Nonce']);
            $this->assertSame($data['amount'], $results['Message']['Amount']);
            $this->assertSame($data['order_no'], $results['Message']['OrderNo']);
            $this->assertSame($data['shop_no'], $results['Message']['ShopNo']);
            $this->assertSame('S', $results['Message']['Status']);
            $this->assertSame('C', $results['Message']['PayType']);
            $this->assertNotEmpty($results['Message']['TSNo']);
            $this->assertNotEmpty($results['Message']['Description']);
            $this->assertNotEmpty($results['Message']['CardParam']['CardPayURL']);
        }
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

        echo "\n";
        echo "測試建立虛擬帳號訂單結果如下：\n";
        echo json_encode($results, JSON_PRETTY_PRINT);
        echo "\n";

        if (empty($results['APIService'])) {
            $this->assertTrue(false);
        } else {
            $this->assertSame('1.0.0', $results['Version']);
            $this->assertSame($data['shop_no'], $results['ShopNo']);
            $this->assertSame('OrderCreate', $results['APIService']);
            $this->assertNotEmpty($results['Sign']);
            $this->assertNotEmpty($results['Nonce']);
            $this->assertSame($data['amount'], $results['Message']['Amount']);
            $this->assertSame($data['order_no'], $results['Message']['OrderNo']);
            $this->assertSame($data['shop_no'], $results['Message']['ShopNo']);
            $this->assertSame('S', $results['Message']['Status']);
            $this->assertSame('A', $results['Message']['PayType']);
            $this->assertNotEmpty($results['Message']['TSNo']);
            $this->assertNotEmpty($results['Message']['Description']);
            $this->assertNotEmpty($results['Message']['ATMParam']['AtmPayNo']);
            $this->assertNotEmpty($results['Message']['ATMParam']['WebAtmURL']);
            $this->assertNotEmpty($results['Message']['ATMParam']['OtpURL']);
        }
    }

    public function test_method_queryOrders()
    {
        $data = [
            'shop_no'              => get_testing_shop_no(),
            'pay_type'             => 'A',
            'order_datetime_begin' => date('Ymd') . '0000',
            'order_datetime_end'   => date('Ymd') . '2359',
        ];

        $qpay = get_qpay_instance();
        $results = $qpay->queryOrders($data);

        if (empty($results['APIService'])) {
            $this->assertTrue(false);
        } else {
            $this->assertSame('1.0.0', $results['Version']);
            $this->assertSame($data['shop_no'], $results['ShopNo']);
            $this->assertSame('OrderQuery', $results['APIService']);
            $this->assertNotEmpty($results['Sign']);
            $this->assertNotEmpty($results['Nonce']);
            $this->assertSame($data['shop_no'], $results['Message']['ShopNo']);
            $this->assertNotEmpty($results['Message']['Date']);
            $this->assertSame('S', $results['Message']['Status']);
            $this->assertNotEmpty($results['Message']['Description']);

            // Remved checking OrderList, becasue it just has values after 5 minutes.
            // $this->assertNotEmpty($results['Message']['OrderList']);
        }
    }

    public function test_method_queryOrderByToken()
    {
        $data = [
            'shop_no'   => get_testing_shop_no(),
            'pay_token' => 'da1547c3d0d1649af5049125b0880c0e227f31e107cbf4f0995bed28d0f066c1',
        ];

        $qpay = get_qpay_instance();
        $results = $qpay->queryOrderByToken($data['pay_token']);
        if (empty($results['APIService'])) {
            $this->assertTrue(false);
        } else {
            $this->assertSame('1.0.0', $results['Version']);
            $this->assertSame($data['shop_no'], $results['ShopNo']);
            $this->assertSame('OrderPayQuery', $results['APIService']);
            $this->assertNotEmpty($results['Sign']);
            $this->assertNotEmpty($results['Nonce']);
            $this->assertSame($data['shop_no'], $results['Message']['ShopNo']);
            $this->assertSame($data['pay_token'], $results['Message']['PayToken']);
            $this->assertNotEmpty($results['Message']['Date']);
            $this->assertSame('S', $results['Message']['Status']);
            $this->assertNotEmpty($results['Message']['Description']);
        }
    }
}
