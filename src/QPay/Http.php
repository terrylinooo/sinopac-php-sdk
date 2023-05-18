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
use Sinopac\Exception\QPayException;
use Shieldon\Psr7\Response;

use function curl_close;
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
    private static $headers = [];

    /**
     * Send a HTTP request.
     *
     * @param string $url The request target URL.
     * @param array $fields The data fields to send.
     * @param string $keyId The X-KeyID to send.
     * @return Response
     */
    public static function request(string $url, array $fields, string $keyId): Response
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new QPayException(
                sprintf('Invalid URL %s', $url)
            );
        }

        $headers = [
            'Content-Type: application/json',
        ];

        if (!empty($keyId)) {
            $headers[] = 'X-KeyID: ' . $keyId;
        }

        return self::cURL($url, $fields, $headers);
    }

    /**
     * Create a HTTP request through cURL.
     *
     * @param string $url The request target URL.
     * @param array $fields The data fields to send.
     * @param array $headers The HTTP headers to send.
     * @return Response
     */
    protected static function cURL(string $url, array $fields, array $headers): Response
    {
        $ch = curl_init($url);
        self::$headers = [];

        $fields = json_encode($fields, JSON_FORCE_OBJECT);
        $agent = 'sinopac-php-sdk/' . QPay::SDK_VERSION;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [new self(), 'readHeaders']);

        $results = curl_exec($ch);
        curl_close($ch);

        $parsedHeaders = self::getParsedHeaders();

        return new Response(
            $parsedHeaders['statusCode'],
            $parsedHeaders['headers'],
            $results,
            $parsedHeaders['version'],
            $parsedHeaders['reason']
        );
    }

    /**
     * Parse the HTTP header.
     *
     * @param mixed $curl
     * @param string $headerLine
     * @return int
     */
    protected static function readHeaders($curl, string $headerLine): int
    {
        array_push(self::$headers, trim($headerLine));
        return strlen($headerLine);
    }

    /**
     * Get parsed headers.
     *
     * @return array
     */
    protected static function getParsedHeaders(): array
    {
        $prepareHeaders = [];
        $version = '';
        $statusCode = 0;
        $reason = '';
        foreach (self::$headers as $header) {
            $colonPosition = strpos($header, ':');
            if ($colonPosition === false) {
                if (preg_match('/HTTP\/(\d+\.\d+)\s+(\d+)\s+(.*)/', $header, $matches)) {
                    $version = $matches[1];
                    $statusCode = (int) $matches[2];
                    $reason = $matches[3];
                }
                continue;
            }
            $name = substr($header, 0, $colonPosition);
            $value = substr($header, $colonPosition + 1);
            $prepareHeaders[$name] = $value;
        }
        return [
            'version' => $version,
            'statusCode' => $statusCode,
            'reason' => $reason,
            'headers' => $prepareHeaders,
        ];
    }
}
