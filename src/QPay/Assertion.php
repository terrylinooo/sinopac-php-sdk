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

use Sinopac\Exception\QPayException;
use Sinopac\QPay\Fields;
use Sinopac\QPay\ErrorEnum;
use DateTime;

use function explode;
use function gettype;
use function implode;
use function in_array;
use function sprintf;
use function str_replace;

/**
 * Verify parameters in Assertion.
 */
trait Assertion
{
    /**
     * Verify the service type is correct or not.
     *
     * @param string $type The service type that API provides with.
     *
     * @return void
     * @throws QPayException
     */
    protected function assertServiceType(string $type): void
    {
        $supportedTypes = [
            'OrderCreate',
            'OrderQuery',
            'OrderPayQuery',
        ];

        if (!in_array($type, $supportedTypes)) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::FIELD_SERVICE_TYPE_ERROR,
                    $type
                )
            );
        }
    }

    /**
     * Verify the configuration values.
     *
     * @param array $data A collect of configuration values.
     *
     * @return void
     * @throws QPayException
     */
    protected function assertConfig(array $data): void
    {
        if (empty($data['shop_no'])) {
            throw new QPayException(ErrorEnum::FIELD_SHOP_NO_ERROR);
        }

        if (empty($data['hash'])) {
            throw new QPayException(ErrorEnum::FIELD_HASH_KEY_PAIR_ERROR);
        }

        for ($i = 0; $i <= 3; $i++) {
            if (empty($data['hash'][$i])) {
                throw new QPayException(
                    sprintf(
                        ErrorEnum::FIELD_HASH_KEY_ERROR,
                        $i
                    )
                );
            }
        }
    }

    /**
     * Check if every field is fit with the limitation.
     *
     * @param array $fields The input data.
     *
     * @return void
     * @throws QPayException
     */
    protected function assertOrderCreate(array $fields): void
    {
        $this->assertApiServiceFields('orderCreate', $fields);
    }

    /**
     * Check if every field is fit with the limitation.
     *
     * @param array $fields The input data.
     *
     * @return void
     * @throws QPayException
     */
    protected function assertOrderQuery(array $fields): void
    {
        $this->assertApiServiceFields('orderQuery', $fields);
    }

    /**
     * Check if every field is fit with the limitation.
     *
     * @param array $fields The input data.
     *
     * @return void
     * @throws QPayException
     */
    protected function assertOrderPayQuery(array $fields): void
    {
        $this->assertApiServiceFields('orderPayQuery', $fields);
    }

    /**
     * Check if every field is fit with the limitation.
     *
     * @param array $fields The input data.
     *
     * @return void
     * @throws QPayException
     */
    private function assertApiServiceFields(string $type, array $fields): void
    {
        $apiFieldsLimitation = Fields::{$type}($fields);
        
        foreach ($apiFieldsLimitation as $name => $limitation) {
            $limitation['required'] = isset($limitation['default'])
                ? false
                : $limitation['required'];

            $this->assertFieldRequired($limitation['required'], $fields, $name);
            $this->assertFieldType($limitation['type'], $fields, $name);
            $this->assertFieldLength($limitation['length'], $fields, $name);
            $this->assertFieldRules($limitation['rules'], $fields, $name);
        }
    }

    /**
     * Check if the required field exists or not.
     *
     * @param bool   $required Is this field required.
     * @param array  $fields   Input data.
     * @param string $name     The field name.
     *
     * @return void
     * @throws QPayException
     */
    private function assertFieldRequired(bool $required, array $fields, string $name): void
    {
        if ($required && !isset($fields[$name])) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::FIELD_MISSING_FIELD_ERROR,
                    $name
                )
            );
        }
    }

    /**
     * Check the type of the variable.
     *
     * @param string $type   The type of the variable.
     * @param array  $fields Input data.
     * @param string $name   The field name.
     *
     * @return void
     * @throws QPayException
     */
    private function assertFieldType(string $type, array $fields, string $name): void
    {
        if (!isset($fields[$name])) {
            return;
        }

        $fieldType = gettype($fields[$name]);

        if ($fieldType !== $type) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::FIELD_VARIABLE_TYPE_ERROR,
                    $name,
                    $type,
                    $fieldType
                )
            );
        }
    }

    /**
     * Check the size of the variable.
     *
     * @param int $length The size limitation of the field
     * @param array  $fields Input data.
     * @param string $name   The field name.
     *
     * @return void
     * @throws QPayException
     */
    private function assertFieldLength(int $length, array $fields, string $name): void
    {
        if (!isset($fields[$name])) {
            return;
        }

        $fieldLength = strlen((string) $fields[$name]);

        if ($fieldLength > $length) {
            throw new QPayException(
                sprintf(
                    ErrorEnum::FIELD_SIZE_ERROR,
                    $name,
                    $length,
                    $fieldLength
                )
            );
        }
    }
    

    /**
     * Check if the field is fit with the rules or not.
     *
     * @param string $rules  The specific rule for this field.
     * @param array  $fields Input data.
     * @param string $name   The field name.
     *
     * @return void
     * @throws QPayException
     */
    private function assertFieldRules(string $rules, array $fields, string $name): void
    {
        if (!isset($fields[$name]) || $rules === '') {
            return;
        }

        $ruleData = explode(':', $rules);
        $ruleType = $ruleData[0];
        $ruleContent = $ruleData[1];

        switch ($ruleType) {
            case 'string':
                $ruleOptions = explode('|', $ruleContent);

                if (!in_array($fields[$name], $ruleOptions)) {
                    throw new QPayException(
                        sprintf(
                            ErrorEnum::FIELD_RULE_STRING_ERROR,
                            $name,
                            implode(', ', $ruleOptions),
                            $fields[$name]
                        )
                    );
                }

                break;

            case 'integer':
                $ruleOptions = explode('-', $ruleContent);
                $minimum = $ruleOptions[0];
                $maximum = $ruleOptions[1];

                if ($fields[$name] < $minimum || $fields[$name] > $maximum) {
                    throw new QPayException(
                        sprintf(
                            ErrorEnum::FIELD_RULE_INTEGER_ERROR,
                            $name,
                            $minimum,
                            $maximum,
                            $fields[$name]
                        )
                    );
                }

                break;

            case 'method':
                // Complex rules are processed in another assertion method.
                $this->{$ruleContent}($fields, $name);

                break;

            case 'date':
                $date = DateTime::createFromFormat($ruleContent, $fields[$name]);
                $check = ($date && $date->format($ruleContent) == $fields[$name]);

                if (!$check) {
                    throw new QPayException(
                        sprintf(
                            ErrorEnum::FIELD_RULE_DATE_ERROR,
                            $name,
                            $ruleContent
                        )
                    );
                }

                break;
        }
    }

    /**
     * Check if the field is fit with the rules or not.
     *
     * @param array  $fields Input data.
     * @param string $name   The field name.
     *
     * @return void
     * @throws QPayException
     */
    private function assertFieldAmount(array $fields, string $name): void
    {
        if ($fields['pay_type'] === 'A' && $fields['amount'] > 3000000) {
            throw new QPayException(ErrorEnum::FIELD_AMOUNT_EXCEED_ERROR);
        }
    }

    /**
     * Characters can not have ', " and %
     *
     * @param array  $fields Input data.
     * @param string $name   The field name.
     *
     * @return void
     * @throws QPayException
     */
    private function assertFieldUrlDecode(array $fields, string $name): void
    {
        $string = str_replace(['"', "'", '%'], '', $fields[$name]);

        if ($fields[$name] !== $string) {
            sprintf(
                ErrorEnum::FIELD_INVALID_CHARACTERS_ERROR,
                $name,
            );
        }
    }

    private function assertFieldDateExpired(array $fields, string $name): void
    {
        // Todo
    }
}
