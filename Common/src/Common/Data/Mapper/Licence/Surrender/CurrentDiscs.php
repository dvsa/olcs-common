<?php

namespace Common\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\MapperInterface;

class CurrentDiscs implements MapperInterface
{

    public static function mapFromResult(array $data): array
    {
        return [
            'version' => $data['version'],
            'possessionSection' => [
                'possessionInfo' => [
                    'discDestroyed' => $data['discDestroyed'] ?? null
                ]
            ],
            'lostSection' => [
                'lostInfo' => [
                    'discLost' => $data['discLost'] ?? null,
                    'lostInfo' => $data['discLostInfo'] ?? null
                ]
            ],
            'stolenSection' => [
                'stolenInfo' => [
                    'discStolen' => $data['discStolen'] ?? null,
                    'stolenInfo' => $data['discStolenInfo'] ?? null
                ]
            ]
        ];
    }

    public static function mapFromForm(array $data): array
    {
        return [
            'discDestroyed' => $data['possessionSection']['possessionInfo']['discDestroyed'],
            'discLost' => $data['lostSection']['lostInfo']['discLost'],
            'discLostInfo' => $data['lostSection']['lostInfo']['lostInfo'],
            'discStolen' => $data['stolenSection']['stolenInfo']['discStolen'],
            'discStolenInfo' => $data['stolenSection']['stolenInfo']['stolenInfo']
        ];
    }
}
