<?php

/**
 * OtherLicence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Data\Mapper\Lva;

/**
 * OtherLicence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OtherLicence
{
    public static function mapFromResult(array $data)
    {
        return [
            'id' => $data['id'],
            'version' => $data['version'],
            'licNo' => $data['licNo'],
            'willSurrender' => $data['willSurrender'],
            'holderName' => $data['holderName'],
            'disqualificationDate' => $data['disqualificationDate'],
            'disqualificationLength' => $data['disqualificationLength'],
            'previousLicenceType' => [
                'id' => $data['previousLicenceType']['id']
            ]
        ];
    }
}
