<?php

/**
 * Psv Disc Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Psv Disc Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvDiscEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'PsvDisc';

    protected $bundle = [
        'children' => [
            'licence'
        ]
    ];

    public function ceaseDiscs(array $ids = array())
    {
        $ceasedDate = $this->getServiceLocator()->get('Helper\Date')->getDate();
        $postData = array();

        foreach ($ids as $id) {

            $postData[] = array(
                'id' => $id,
                'ceasedDate' => $ceasedDate,
                '_OPTIONS_' => array('force' => true)
            );
        }

        $postData['_OPTIONS_']['multiple'] = true;

        $this->put($postData);
    }

    /**
     * Request multiple discs
     *
     * @param int $count
     * @param array $data
     */
    public function requestDiscs($count, $data = array())
    {
        $defaults = array(
            'isCopy' => 'N'
        );

        $postData = $this->getServiceLocator()->get('Helper\Data')->arrayRepeat(array_merge($defaults, $data), $count);

        $postData['_OPTIONS_'] = array('multiple' => true);

        $this->save($postData);
    }

    /**
     * Get active and pending discs
     *
     * @param int $licenceId
     * @return array
     */
    public function getNotCeasedDiscs($licenceId)
    {
        $query = [
            'ceasedDate' => 'NULL',
            'licence' => $licenceId
        ];

        return $this->getAll($query, $this->bundle);
    }

    public function updateExistingForLicence($licenceId)
    {
        $results = $this->getNotCeasedDiscs($licenceId);

        // @TODO mark ceased date as today

        // we just create (X) new discs

        return $this->requestBlankDiscs($licenceId, $results['Count']);
    }

    public function requestBlankDiscs($licenceId, $count)
    {
        $data = array(
            'licence' => $licenceId,
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
        );

        $this->getServiceLocator()->get('Entity\PsvDisc')->requestDiscs($count, $data);
    }
}
