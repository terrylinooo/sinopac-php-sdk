<?php

namespace Sinopac\Test\QPay;

use PHPUnit\Framework\TestCase;
use Sinopac\QPay\Fields;
use function get_qpay_instance;

class FieldsTest extends TestCase
{
    public function test_method_getApiFields_OrderCreate()
    {
        $data = [
            'shop_no'          => get_testing_shop_no(),
            'order_no'         => get_testing_order_no(),
            'amount'           => 50000,
            'pay_type'         => 'A',
            'currency_id'       => 'TWD',
            'atm_expired_date' => get_testing_expired_date(),
            'product_name'     => '虛擬帳號訂單',
            'return_url'       => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
            'backend_url'      => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
        ];

        $results = Fields::getApiFields('OrderCreate', $data);
      
        $this->assertSame($data['shop_no'], $results['ShopNo']);
        $this->assertSame($data['order_no'], $results['OrderNo']);
        $this->assertSame($data['amount'], $results['Amount']);
        $this->assertSame($data['currency_id'], $results['CurrencyID']);
        $this->assertSame($data['product_name'], $results['PrdtName']);
        $this->assertSame($data['return_url'], $results['ReturnURL']);
        $this->assertSame($data['backend_url'], $results['BackendURL']);
        $this->assertSame($data['atm_expired_date'], $results['ATMParam']['ExpireDate']);

        $data = [
            'shop_no'                 => get_testing_shop_no(),
            'order_no'                => get_testing_order_no(),
            'amount'                  => 70000,
            'pay_type'                => 'C',
            'currency_id'             => 'TWD',
            'cc_auto_billing'         => 'N',
            'cc_expired_billing_days' => 10,
            'product_name'            => '信用卡訂單',
            'return_url'              => 'http://10.11.22.113:8803/QPay.ApiClient/Store/Return',
            'backend_url'             => 'http://10.11.22.113:8803/QPay.ApiClient/AutoPush/PushSuccess',
        ];

        $results = Fields::getApiFields('OrderCreate', $data);

      
        $this->assertSame($data['shop_no'], $results['ShopNo']);
        $this->assertSame($data['order_no'], $results['OrderNo']);
        $this->assertSame($data['amount'], $results['Amount']);
        $this->assertSame($data['currency_id'], $results['CurrencyID']);
        $this->assertSame($data['product_name'], $results['PrdtName']);
        $this->assertSame($data['return_url'], $results['ReturnURL']);
        $this->assertSame($data['backend_url'], $results['BackendURL']);
        $this->assertSame($data['cc_auto_billing'], $results['CardParam']['AutoBilling']);
        $this->assertSame($data['cc_expired_billing_days'], $results['CardParam']['ExpBillingDays']);
        $this->assertSame(10, $results['CardParam']['ExpMinutes']);
    }
}
