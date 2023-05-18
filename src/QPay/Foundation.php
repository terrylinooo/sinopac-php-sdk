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

namespace Sinopac\QPay;

use Sinopac\QPay\Algorithm;
use Sinopac\QPay\Assertion;
use Sinopac\QPay\Logger;
use Sinopac\QPay\Http;
use Sinopac\QPay\ErrorEnum;
use Sinopac\Exception\QPayException;

/**
 * The Foundation trait.
 */
trait Foundation
{
    use Algorithm;

    use Assertion;

    use Logger;

    /**
     * API version No.
     *
     * @var string
     */
    private $version = '1.0.0';

    /**
     * The API URL used in production environment.
     *
     * @var string
     */
    private $apiUrl = [
        'prod' => 'https://api.sinopac.com/funBIZ/QPay.WebAPI/api',
        'test' => 'https://apisbx.sinopac.com/funBIZ-Sbx/QPay.WebAPI/api',
        'legacy_test' => 'https://apisbx.sinopac.com/funBIZ/QPay.WebAPI/api',
        
    ];

    /**
     * Is in sandbox mode?
     *
     * @var bool
     */
    private $sandbox = false;

    /**
     * The Shop No.
     *
     * @var string
     */
    private $shopNo = '';

    /**
     * The hash group is used on generating HashId.
     *
     * @var array
     */
    private $firstHashPair = [
        0 => '',
        1 => '',
    ];

    /**
     * The hash group is used on generating HashId.
     *
     * @var array
     */
    private $secondHashPair = [
        0 => '',
        1 => '',
    ];

    /**
     * This is used on sending HTTP request.
     * For the header field X-KeyID.
     * It takes effect since the end of April 2023.
     *
     * @var string
     */
    private $keyId = '';

    /**
     * Set up Shop No.
     *
     * @param string $no The Shop No will be set.
     * @return void
     */
    public function setShopNo(string $no): void
    {
        $this->shopNo = $no;
    }

    /**
     * Set the A-pair hash key.
     *
     * @param string $a1 The A1 hash key.
     * @param string $a2 The B1 hash key.
     * @return void
     */
    public function setFirstHashPair(string $a1, string $a2): void
    {
        $this->firstHashPair = [
            $a1,
            $a2,
        ];
    }

    /**
     * Set the B-pair hash key.
     *
     * @param string $b1 The B1 hash key.
     * @param string $b2 The B2 hash key.
     * @return void
     */
    public function setSecondHashPair(string $b1, string $b2): void
    {
        $this->secondHashPair = [
            $b1,
            $b2,
        ];
    }

    /**
     * Set up Key ID.
     *
     * @param string $keyId The Key ID.
     * @return void
     */
    public function setKeyId(string $keyId): void
    {
        $this->keyId = $keyId;
    }

    /**
     * Set up Nonce
     *
     * @param string $nonce The Nonce.
     * @return void
     */
    public function setNonce(string $nonce): void
    {
        $this->nonce = $nonce;
    }

    /**
     * Enable the sandbox mode.
     *
     * @return void
     */
    public function enableSandbox(): void
    {
        $this->sandbox = true;
    }

    /**
     * Start sending a request to API endpoint.
     *
     * @param string $type The service type of the Order API.
     * @param array  $body The data fields will be sent.
     * @return array
     */
    public function getRequestBody(string $type, array $body): array
    {
        $this->assertServiceType($type);

        $nonce = $this->getNonce();

        if (empty($nonce)) {
            return [];
        }

        $iv = $this->getIV($nonce);

        $hashId = $this->getHashId(
            $this->firstHashPair[0],
            $this->firstHashPair[1],
            $this->secondHashPair[0],
            $this->secondHashPair[1]
        );
        
        $sign = $this->getSign($body, $nonce, $hashId);
        $message = $this->aesEncrypt($body, $hashId, $iv);

        $body = [
            'APIService' => $type,
            'Version'    => $this->version,
            'ShopNo'     => $this->shopNo,
            'Nonce'      => $nonce,
            'Sign'       => $sign,
            'Message'    => $message,
        ];

        return $body;
    }

    /**
     * Get the Shop No.
     *
     * @return string
     */
    public function getShopNo(): string
    {
        return $this->shopNo;
    }

    /**
     * Get the Key ID.
     *
     * @return string
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * Get the API URL.
     *
     * @return string
     */
    public function getApiUrl(string $name): string
    {
        if ($this->sandbox) {
            return $this->apiUrl['test'] . '/' . $name;
        }

        return $this->apiUrl['prod'] . '/' . $name;
    }

    /**
     * Fetch the fresh Nonce.
     *
     * @return string
     */
    public function getNonce(): string
    {
        $nonce = '';
        $apiUrl = $this->getApiUrl('Nonce');
        $keyId = $this->getKeyId();

        $parameters = [
            'ShopNo' => $this->getShopNo(),
        ];

        $response = Http::request($apiUrl, $parameters, $keyId);
        $bodyContent = $response->getBody()->getContents();
        $httpStatusCode = $response->getStatusCode();

        if ($httpStatusCode !== 200) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::API_UNEXPECTED_HTTP_STATUS_ERROR,
                    $httpStatusCode,
                    $bodyContent
                ),
            );
        }

        $data = json_decode($bodyContent, true);

        if (empty($data['Nonce'])) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::API_UNEXPECTED_RESULTS_ERROR,
                    $bodyContent
                )
            );
        } else {
            $nonce = $data['Nonce'];
        }

        return $nonce;
    }

    /**
     * Send a request to Order API.
     *
     * @return array
     * @throws QPayException
     */
    public function sendRequest(array $parameters): array
    {
        $apiUrl = $this->getApiUrl('Order');
        $keyId = $this->getKeyId();

        $response = Http::request($apiUrl, $parameters, $keyId);
        $bodyContent = $response->getBody()->getContents();
        $httpStatusCode = $response->getStatusCode();

        if ($httpStatusCode !== 200) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::API_UNEXPECTED_HTTP_STATUS_ERROR,
                    $httpStatusCode,
                    $bodyContent
                ),
            );
        }

        $data = json_decode($bodyContent, true);

        if (JSON_ERROR_NONE !== json_last_error() || empty($data['Nonce'])) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::API_UNEXPECTED_RESULTS_ERROR,
                    $bodyContent
                )
            );
        }

        $hashId = $this->getHashId(
            $this->firstHashPair[0],
            $this->firstHashPair[1],
            $this->secondHashPair[0],
            $this->secondHashPair[1]
        );

        $nonce = $this->getIV($data['Nonce']);

        $decryptedData = $this->aesDecrypt(
            $data['Message'],
            $hashId,
            $nonce
        );

        $data['Message'] = json_decode($decryptedData, true);

        return $data;
    }
}
