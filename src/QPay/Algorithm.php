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

use function array_filter;
use function base_convert;
use function bin2hex;
use function count;
use function hash;
use function hex2bin;
use function http_build_query;
use function implode;
use function is_array;
use function json_encode;
use function ksort;
use function openssl_decrypt;
use function openssl_encrypt;
use function str_pad;
use function strlen;
use function strtoupper;
use function substr;
use function urldecode;

/**
 * The algorithmic logic we will use.
 */
trait Algorithm
{
    /**
     * Turn message string into hashed string.
     * Check out the rules on P.19
     *
     * Detailed explanation:
     * https://ithelp.ithome.com.tw/articles/10266896
     *
     * @param array $body The message body.
     *
     * @return string
     */
    public function getHashedMessageBody(array $body): string
    {
        ksort($body);

        $body = array_filter($body, function ($value) {
            return !empty($value) && !is_array($value);
        });

        $hashedBody = urldecode(http_build_query($body));

        return $hashedBody;
    }
    /**
     * According to the API document, the IV value is extracted from
     * the Nonce string. Check out the rules on P.22
     *
     * Detailed explanation:
     * https://ithelp.ithome.com.tw/articles/10268021
     *
     * @param string $nonce The Nonce string.
     * @return string
     */
    public function getIV(string $nonce): string
    {
        $string = strtoupper(hash('sha256', $nonce));
        $string = substr($string, -16);

        return $string;
    }

    /**
     * Return the uppercase SHA-256 string.
     *
     * @param string $string Any string you want to encrypt.
     * @return string
     */
    public function getSha256(string $string): string
    {
        return strtoupper(hash('sha256', $string));
    }

    /**
     * Get the signature sign from the message body, Nonce and HashId.
     * Check out the rules on P.13
     *
     * Detailed explanation:
     * https://ithelp.ithome.com.tw/articles/10267405
     *
     * @param array  $data
     * @param string $nonce
     * @param string $hashId
     *
     * @return string
     */
    public function getSign(array $data, string $nonce, string $hashId): string
    {
        $string = $this->getHashedMessageBody($data);
        $string .= $nonce;
        $string .= $hashId;

        return strtoupper(hash('sha256', $string));
    }

    /**
     * Encrypt data by AES-256-CBC
     *
     * @param array  $data The data fields of requesting API.
     * @param string $key  The key used to encrypt.
     * @param string $iv   The IV used to encrypt.
     * @return string
     */
    public function aesEncrypt(array $data, string $key, string $iv): string
    {
        $json = json_encode($data);
        $encrypt = openssl_encrypt($json, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $result = strtoupper(bin2hex($encrypt));

        return $result;
    }

    /**
     * Decrypt data by AES-256-CBC
     *
     * @param string  $hex The Message field of the API returned string.
     * @param string $key The key used to encrypt.
     * @param string $iv  The IV used to encrypt.
     * @return string
     */
    public function aesDecrypt(string $hex, string $key, string $iv): string
    {
        $data = hex2bin($hex);
        $result = openssl_decrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $result;
    }

    /**
     * Get the HashId.
     *
     * Detailed explanation:
     * https://ithelp.ithome.com.tw/articles/10266442
     *
     * @param string $hashA1
     * @param string $hashA2
     * @param string $hashB1
     * @param string $hashB2
     * @return string
     */
    public function getHashId(
        string $hashA1,
        string $hashA2,
        string $hashB1,
        string $hashB2
    ): string
    {
        $hashId = '';

        $decimalGroupA1 = $this->hexToDec($hashA1);
        $decimalGroupA2 = $this->hexToDec($hashA2);
        $decimalGroupB1 = $this->hexToDec($hashB1);
        $decimalGroupB2 = $this->hexToDec($hashB2);

        $xorResultA = $this->getXOR($decimalGroupA1, $decimalGroupA2);
        $xorResultB = $this->getXOR($decimalGroupB1, $decimalGroupB2);

        $hexA = $this->restoreDecToHex($xorResultA);
        $hexB = $this->restoreDecToHex($xorResultB);

        $hashId = $hexA . $hexB;

        return $hashId;
    }

    /**
     * Get the Exclusive-OR results.
     *
     * @param array $decimalGroup1 The first place.
     * @param array $decimalGroup2 The second place compares with the first.
     * @return array
     */
    private function getXOR(array $decimalGroup1, array $decimalGroup2): array
    {
        $results = [];
        $groupCount = count($decimalGroup1);

        for ($i = 0; $i < $groupCount; $i++) {
            $results[$i] = ($decimalGroup1[$i] ^ $decimalGroup2[$i]);
        }

        return $results;
    }

    /**
     * Transform a hexadecimal string into a decimal number group.
     *
     * @param string $string HEX string.
     *
     * @return array A binary group.
     */
    private function hexToDec(string $string): array
    {
        $decimalGroup = [];
        $len = strlen($string);
        $j = 0;
        
        for ($i = 0; $i < $len; $i += 2) {
            $decimalGroup[$j] = (int) base_convert(substr($string, $i, 2), 16, 10);
            $j++;
        }

        return $decimalGroup;
    }

    /**
     * Restore a decimal number group to a hexadecimal string.
     *
     * @param array $decimalGroup
     *
     * @return string
     */
    private function restoreDecToHex(array $decimalGroup): string
    {
        $hexGroup = [];
        $groupCount = count($decimalGroup);

        for ($i = 0; $i < $groupCount; $i++) {
            $hexGroup[$i] = base_convert((string) $decimalGroup[$i], 10, 16);
            $hexGroup[$i] = str_pad($hexGroup[$i], 2, '0', STR_PAD_LEFT);
        }

        $hexString = strtoupper(implode('', $hexGroup));

        return $hexString;
    }
}
