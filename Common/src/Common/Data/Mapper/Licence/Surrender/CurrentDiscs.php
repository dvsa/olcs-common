<?php

namespace Common\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\MapperInterface;

class CurrentDiscs implements MapperInterface
{

    public static function mapFromResult(array $data): array
    {

        $inPossession = isset($data['discDestroyed']) ? 'Y' : 'N';
        $lost = isset($data['discLost']) ? 'Y' : isset($data['discLostInfo']) ? 'Y' : 'N';
        $stolen = isset($data['discStolen']) ? 'Y' : isset($data['discStolenInfo']) ? 'Y' : 'N';

        return [
            'version' => $data['version'],
            'possessionSection' => [
                'inPossession' => $inPossession,
                'possessionInfo' => [
                    'discDestroyed' => $data['discDestroyed'] ?? null
                ]
            ],
            'lostSection' => [
                'lost' => $lost,
                'lostInfo' => [
                    'discLost' => $data['discLost'] ?? null,
                    'lostInfo' => $data['discLostInfo'] ?? null
                ]
            ],
            'stolenSection' => [
                'stolen' => $stolen,
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
