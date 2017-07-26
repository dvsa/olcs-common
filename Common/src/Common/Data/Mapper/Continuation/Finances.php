<?php

namespace Common\Data\Mapper\Continuation;

use Common\Data\Mapper\MapperInterface;

/**
 * Finances
 */
class Finances implements MapperInterface
{
    /**
     * Map data from API data into something for the form
     *
     * @param array $data Data from the API
     *
     * @return array data for the form
     */
    public static function mapFromResult(array $data)
    {
        return [
            'finances' => [
                'version' => $data['version'],
                'averageBalance' => isset($data['averageBalanceAmount']) ? $data['averageBalanceAmount'] : '',
                'overdraftFacility' => [
                    'yesNo' => isset($data['hasOverdraft']) ? $data['hasOverdraft'] : '',
                    'yesContent' => isset($data['overdraftAmount']) ? $data['overdraftAmount'] : '',
                ],
                'factoring' => [
                    'yesNo' => isset($data['hasFactoring']) ? $data['hasFactoring'] : '',
                    'yesContent' => [
                        'amount' => isset($data['factoringAmount']) ? $data['factoringAmount'] : '',
                    ]
                ]
            ]
        ];
    }

    /**
     * Map data from form to DTO
     *
     * @param array $formData Form data
     *
     * @return array
     */
    public static function mapFromForm(array $formData)
    {
        return [
            'version' => (int)$formData['finances']['version'],
            'averageBalanceAmount' => $formData['finances']['averageBalance'],
            'hasOverdraft' => $formData['finances']['overdraftFacility']['yesNo'],
            'overdraftAmount' => $formData['finances']['overdraftFacility']['yesNo'] === 'Y'
                ? $formData['finances']['overdraftFacility']['yesContent']
                : null,
            'hasFactoring' => $formData['finances']['factoring']['yesNo'],
            'factoringAmount' => $formData['finances']['factoring']['yesNo'] === 'Y'
                ? $formData['finances']['factoring']['yesContent']['amount']
                : null,
        ];
    }
}
