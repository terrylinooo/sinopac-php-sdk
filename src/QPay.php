<?php
/*
 * This file is part of the Sinopac PHP SDK package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sinopac;

use Sinopac\QPay\Foundation;
use Sinopac\QPay\Assertion;
use Sinopac\QPay\Fields;
use Sinopac\QPay\Logger;

/**
 * The Nonce API.
 */
class QPay
{
    use Foundation;

    use Assertion;

    use Logger;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->assertConfig($config);

            $this->setShopNo($config['shop_no']);
    
            $this->setFirstHashPair(
                $config['hash'][0],
                $config['hash'][1]
            );
    
            $this->setSecondHashPair(
                $config['hash'][2],
                $config['hash'][3]
            );
        }

        if (!empty($config['logger'])) {
            $this->setLogger($config['logger']);
        }
    }

    /**
     * Create payment order by credit card (C) or virtual acount (A).
     *
     * @param string $type     The payment type, option: C | A
     * @param array  $formData The message body.
     *
     * @return array
     * @throws QPayException
     */
    public function createOrder(string $type, array $formData)
    {
        $apiService = 'OrderCreate';
        $formData['shop_no'] = $this->getShopNo();
        $formData['pay_type'] = $type;
        $formData['currency_id'] = 'TWD';

        $this->assertOrderCreate($formData);

        $fields = Fields::getApiFields($apiService, $formData);
        $requestBody = $this->getRequestBody($apiService, $fields);
        $results = $this->sendRequest($requestBody);

        return $results;
    }

    /**
     * Create an order by using Credit Card payment.
     *
     * @param array $formData The message body.
     *
     * @return array
     */
    public function createOrderByCreditCard(array $formData)
    {
        return $this->createOrder('C', $formData);
    }

    /**
     * Create an order by using ATM (Virtual Account).
     *
     * @param array $formData The message body.
     *
     * @return array
     */
    public function createOrderByATM(array $formData)
    {
        return $this->createOrder('A', $formData);
    }

    /**
     * Query a deluge of orders' information.
     *
     * @param array $formData The message body.
     *
     * @return array
     */
    public function queryOrders(array $formData)
    {
        $apiService = 'OrderQuery';
        $formData['shop_no'] = $this->getShopNo();

        $this->assertOrderQuery($formData);

        $fields = Fields::getApiFields($apiService, $formData);
        $requestBody = $this->getRequestBody($apiService, $fields);
        $results = $this->sendRequest($requestBody);

        return $results;
    }

    /**
     * Query a single order's information by its PayToken.
     *
     * @param string $token The PayToken.
     *
     * @return array
     */
    public function queryOrderByToken(string $token)
    {
        $apiService = 'OrderPayQuery';
        $formData['shop_no'] = $this->getShopNo();
        $formData['pay_token'] = $token;

        $this->assertOrderPayQuery($formData);

        $fields = Fields::getApiFields($apiService, $formData);
        $requestBody = $this->getRequestBody($apiService, $fields);
        $results = $this->sendRequest($requestBody);

        return $results;
    }
}
