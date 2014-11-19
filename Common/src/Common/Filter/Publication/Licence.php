<?php

namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Class DateTimeSelectNullifier
 * @package Common\Filter
 */
class Licence extends AbstractPublicationFilter
{
    const GV_LIC_TYPE = 'lcat_gv';

    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        //var_dump($this->getServiceLocator()); die();
//var_dump($this->getPluginManager()->getServiceLocator()); die();
        $licence = $this->getServiceLocator()->get('\Common\Service\Data\Licence')->fetchLicenceData();

        if (!isset($licence['id'])) {
            throw new ResourceNotFoundException('No licence found');
        }

        $newData = [
            'pubType' => $licence['goodsOrPsv']['id'] == self::GV_LIC_TYPE ? 'A&D' : 'N&P',
            'licence' => $licence['id'],
            'trafficArea' => $licence['trafficArea']['id']
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
