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

        $possessionData = $data['possessionSection']['possessionInfo'];
        $lostData = $data['lostSection']['lostInfo'];
        $stolenData = $data['stolenSection']['stolenInfo'];

        $return = [];
        if (!empty($possessionData['discDestroyed'])) {
            $return['discDestroyed'] = $possessionData['discDestroyed'];
        }
        if (!empty($lostData['discLost'])) {
            $return ['discLost'] = $lostData['discLost'];
        }
        if (!empty($lostData['lostInfo'])) {
            $return ['discLostInfo'] = $lostData['lostInfo'];
        }
        if (!empty($stolenData['discStolen'])) {
            $return['discStolen'] = $stolenData['discStolen'];
        }
        if (!empty($stolenData['discStolen'])) {
            $return['discStolenInfo'] = $stolenData['discStolen'];
        }

        return $return;
    }
}
