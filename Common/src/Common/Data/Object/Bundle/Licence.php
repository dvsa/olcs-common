<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;

class Licence extends Bundle
{
    protected $bundle = array(
        'children' => array(
            'cases' => array(
                'children' => array(
                    'appeals' => array(
                        'children' => array(
                            'outcome' => array(
                                'children' => array(
                                    'reason'
                                )
                            )
                        )
                    ),
                    'stays' => array(
                        'children' => array(
                            'stayType' => array(
                                'children' => array(
                                    'outcome'
                                )
                            ),
                            'foo'
                        )
                    )
                )
            ),
            'status',
            'goodsOrPsv',
            'licenceType',
            'trafficArea',
            'organisation' => array(
                'children' => array(
                    'organisationPersons' => array(
                        'children' => array(
                            'tradingNames'
                        )
                    )
                )
            )
        )
    );

    public function __construct()
    {
        $appeals = new Bundle();
        $appeals->addChild('outcome')->addChild('reason');

        $outcome = new Bundle();
        $outcome->addChild('foo');

        $stays = new Bundle();
        $stays->addChild('stayType')->addChild('outcome', $outcome);
        $stays->addChild('foo');

        $cases = new Bundle();
        $cases->addChild('appeals', $appeals);
        $cases->addChild('stays', $stays);

        $organisation = new Bundle();
        $organisation->addChild('organisationPersons')->addChild('tradingNames');

        $this->addChild('cases', $cases)
             ->addChild('status')
             ->addChild('goodsOrPSv')
             ->addChild('licenceType')
             ->addChild('trafficeArea')
             ->addChild('organisation', $organisation);
    }
}