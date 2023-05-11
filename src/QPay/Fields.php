<?php
/*
 * This file is part of the Sinopac PHP SDK package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information,please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sinopac\QPay;

/**
 * Verify parameters in Assertion.
 */
class Fields
{
    /**
     * Change the field name from snake-case to API's camel-case naming.
     *
     * @param string $type   The API action name.
     * @param array  $fields
     *
     * @return array
     */
    public static function getApiFields(string $type, array $fields): array
    {
        $newData = [];
        $type = lcfirst($type);
        $data = self::{$type}($fields);

        foreach ($data as $key => $value) {
            if (isset($fields[$key])) {
                if (!empty($value['parent'])) {
                    $newData[$value['parent']][$value['name']] = $fields[$key];
                } else {
                    $newData[$value['name']] = $fields[$key];
                }
            } elseif (isset($value['default'])) {
                if (!empty($value['parent'])) {
                    $newData[$value['parent']][$value['name']] = $value['default'];
                } else {
                    $newData[$value['name']] = $value['default'];
                }
            }
        }

        return $newData;
    }

    /**
     * Fields for Order API - orderCreate
     *
     * @param array $fields The input data.
     *
     * @return array
     */
    public static function orderCreate(array $fields = []): array
    {
        $fields['pay_type'] = $fields['pay_type'] ?? null;
        $fields['cc_auto_billing'] = $fields['cc_auto_billing'] ?? null;

        return [
            'shop_no' => [
                'name'     => 'ShopNo',
                'type'     => 'string',
                'length'   => 20,
                'required' => true,
                'rules'     => '',
            ],
            'order_no' => [
                'name'     => 'OrderNo',
                'type'     => 'string',
                'length'   => 50,
                'required' => true,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'pay_type' => [
                'name'     => 'PayType',
                'type'     => 'string',
                'length'   => 1,
                'required' => true,
                'rules'    => 'string:A|C',
            ],
            'amount' => [
                'name'     => 'Amount',
                'type'     => 'integer',
                'length'   => 9,
                'required' => true,
                'rules'    => 'method:assertFieldAmount',
            ],
            'currency_id' => [
                'name'     => 'CurrencyID',
                'type'     => 'string',
                'length'   => 3,
                'required' => true,
                'default'  => 'TWD',
                'rules'    => '',
            ],
            'product_name' => [
                'name'     => 'PrdtName',
                'type'     => 'string',
                'length'   => 60,
                'required' => true,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'order_note' => [
                'name'     => 'Memo',
                'type'     => 'string',
                'length'   => 30,
                'required' => false,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'param_1' => [
                'name'     => 'Param1',
                'type'     => 'string',
                'length'   => 255,
                'required' => false,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'param_2' => [
                'name'     => 'Param2',
                'type'     => 'string',
                'length'   => 255,
                'required' => false,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'param_3' => [
                'name'     => 'Param3',
                'type'     => 'string',
                'length'   => 255,
                'required' => false,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'return_url' => [
                'name'     => 'ReturnURL',
                'type'     => 'string',
                'length'   => 255,
                'required' => true,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'backend_url' => [
                'name'     => 'BackendURL',
                'type'     => 'string',
                'length'   => 255,
                'required' => false,
                'rules'    => 'method:assertFieldUrlDecode',
            ],
            'atm_expired_date' => [
                'parent'   => 'ATMParam',
                'name'     => 'ExpireDate',
                'type'     => 'string',
                'length'   => 8,
                'required' => ($fields['pay_type'] === 'A'),
                'rules'    => 'method:assertFieldDateExpired',
            ],
            'cc_auto_billing' => [
                'parent'   => 'CardParam',
                'name'     => 'AutoBilling',
                'type'     => 'string',
                'length'   => 1,
                'required' => ($fields['pay_type'] === 'C'),
                'rules'    => 'string:Y|N',
            ],
            'cc_expired_billing_days' => [
                'parent'   => 'CardParam',
                'name'     => 'ExpBillingDays',
                'type'     => 'integer',
                'length'   => 2,
                'required' => ($fields['pay_type'] === 'C' && $fields['cc_auto_billing'] === 'N'),
                'rules'    => 'integer:1-7',
            ],
            'cc_expired_minutes' => [
                'parent'   => 'CardParam',
                'name'     => 'ExpMinutes',
                'type'     => 'integer',
                'length'   => 2,
                'required' => ($fields['pay_type'] === 'C'),
                'default'  => 10,
                'rules'    => 'integer:1-30',
            ],
        ];
    }

    /**
     * Fields for Order API - orderQuery
     *
     * @param array $fields The input data.
     *
     * @return array
     */
    public static function orderQuery(array $fields = []): array
    {
        return [
            'shop_no' => [
                'name'     => 'ShopNo',
                'type'     => 'string',
                'length'   => 20,
                'required' => true,
                'rules'    => '',
            ],
            'order_no' => [
                'name'     => 'OrderNo',
                'type'     => 'string',
                'length'   => 50,
                'required' => false,
                'rules'    => '',
            ],
            'pay_type' => [
                'name'     => 'PayType',
                'type'     => 'string',
                'length'   => 1,
                'required' => false,
                'rules'    => 'string:A|C',
            ],
            'order_datetime_begin' => [
                'name'     => 'OrderDateTimeS',
                'type'     => 'string',
                'length'   => 12,
                'required' => false,
                'rules'    => 'date:YmdHi',
            ],
            'order_datetime_end' => [
                'name'     => 'OrderDateTimeE',
                'type'     => 'string',
                'length'   => 12,
                'required' => !empty($fields['order_datetime_begin']),
                'rules'    => 'date:YmdHi',
            ],
            'pay_datetime_begin' => [
                'name'     => 'PayDateTimeS',
                'type'     => 'string',
                'length'   => 12,
                'required' => false,
                'rules'    => 'date:YmdHi',
            ],
            'pay_datetime_end' => [
                'name'     => 'PayDateTimeE',
                'type'     => 'string',
                'length'   => 12,
                'required' => !empty($fields['pay_datetime_begin']),
                'rules'    => 'date:YmdHi',
            ],
            'pay_flag' => [
                'name'     => 'PayFlag ',
                'type'     => 'string',
                'length'   => 1,
                'required' => false,
                'rules'    => 'string:Y|N|O',
            ],
        ];
    }

    /**
     * Fields for Order API - orderPayQuery
     *
     * @param array $fields The input data.
     *
     * @return array
     */
    public static function orderPayQuery(array $fields = []): array
    {
        unset($fields);

        return [
            'shop_no' => [
                'name'     => 'ShopNo',
                'type'     => 'string',
                'length'   => 20,
                'required' => true,
                'rules'    => '',
            ],
            'pay_token' => [
                'name'     => 'PayToken',
                'type'     => 'string',
                'length'   => 100,
                'required' => true,
                'rules'    => '',
            ],
        ];
    }
}
