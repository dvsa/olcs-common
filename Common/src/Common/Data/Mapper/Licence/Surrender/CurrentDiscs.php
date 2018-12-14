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
                'info' => [
                    'number' => $data['discDestroyed'] ?? null
                ]
            ],
            'lostSection' => [
                'lost' => $lost,
                'info' => [
                    'number' => $data['discLost'] ?? null,
                    'details' => $data['discLostInfo'] ?? null
                ]
            ],
            'stolenSection' => [
                'stolen' => $stolen,
                'info' => [
                    'number' => $data['discStolen'] ?? null,
                    'details' => $data['discStolenInfo'] ?? null
                ]
            ]
        ];
    }

    public static function mapFromForm(array $data): array
    {

        $possessionData = $data['possessionSection']['info'];
        $lostData = $data['lostSection']['info'];
        $stolenData = $data['stolenSection']['info'];

        $return = [];
        if (!empty($possessionData['number'])) {
            $return['discDestroyed'] = $possessionData['number'];
        }
        if (!empty($lostData['number'])) {
            $return ['discLost'] = $lostData['number'];
        }
        if (!empty($lostData['details'])) {
            $return ['discLostInfo'] = $lostData['details'];
        }
        if (!empty($stolenData['number'])) {
            $return['discStolen'] = $stolenData['number'];
        }
        if (!empty($stolenData['details'])) {
            $return['discStolenInfo'] = $stolenData['details'];
        }

        return $return;
    }
}
