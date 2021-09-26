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

use Sinopac\QPay;
use InvalidArgumentException;

use function curl_close;
use function curl_errno;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function filter_var;
use function json_encode;
use function sprintf;

/**
 * A simple cURL wrapper for sending HTTP requests.
 */
class Http
{
    public static function request(string $url, array $fields): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL %s', $url)
            );
        }

        return self::cURL($url, $fields);
    }

    /**
     * Create a HTTP request throgh cURL.
     *
     * @param string $url    The request target URL.
     * @param array  $fields The data fields to send.
     * @return array
     */
    protected static function cURL(string $url, array $fields): array
    {
        $ch = curl_init($url);

        $fields = json_encode($fields, JSON_FORCE_OBJECT);
        $agent = 'sinopac-php-sdk/' . QPay::SDK_VERSION;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $results = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorCode = curl_errno($ch);

            $data = [
                'success'    => false,
                'error_code' => $errorCode,
                'message'    => sprintf( 'cURL returns an error code #%s', $errorCode),
                'data'       => [],
            ];
        } else {
            $data = [
                'success'    => true,
                'error_code' => 0,
                'message'    => 'API is successfully called.',
                'data'       => $results,
            ];
        }

        curl_close($ch);

        return $data;
    }
}

