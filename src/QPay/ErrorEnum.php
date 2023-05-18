<?php
/*
 * This file is part of the Sinopac PHP SDK package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// phpcs:ignoreFile

declare(strict_types=1);

namespace Sinopac\QPay;

/**
 * Error message list.
 */
class ErrorEnum
{
    const FIELD_SERVICE_TYPE_ERROR = 'The service type %s is not currently supported. (1001)';
    const FIELD_SHOP_NO_ERROR = 'The shop_no field is missing. (1002)';
    const FIELD_HASH_KEY_PAIR_ERROR = 'The hash keys corresponding to the pair A and B are required. (1003)';
    const FIELD_HASH_KEY_ERROR = 'The hash key % is missing. (1004)';
    const FIELD_MISSING_FIELD_ERROR = 'The QPay API requires %s in order to process your request. (1005)';
    const FIELD_VARIABLE_TYPE_ERROR = 'The field %s is expected to be of type %s, not %s. (1006)';
    const FIELD_SIZE_ERROR = 'The field %s has a size limitation of %d, but the size of your input is %d. (1007)';
    const FIELD_RULE_STRING_ERROR = 'Field %s contains a value that does not meet the requirement. Expected values are %s, but %s was found. (1008)';
    const FIELD_RULE_INTEGER_ERROR = 'Field %s contains a value that does not meet the requirement. It should be between %d and %d, but %d was found. (1009)';
    const FIELD_RULE_DATE_ERROR = 'Field %s should adhere to the date format %s. (1010)';
    const FIELD_AMOUNT_EXCEED_ERROR = 'ATM payments cannot exceed $30,000 NTD. (1011)';
    const FIELD_INVALID_CHARACTERS_ERROR = 'The field %s contains invalid characters. (1012)';

    const HTTP_CURL_CONNECTION_ERROR = 'The field %s contains invalid characters. (2001)';
    const API_UNEXPECTED_HTTP_STATUS_ERROR = 'The Sinopac API returned an unexpected HTTP status code. Expected 200, but received %s. Please check the response body below: %s (3001)';
    const API_UNEXPECTED_RESULTS_ERROR = 'Unexpected results were received from the Sinopac API. Please check the response body below: %s (3002)';
}
