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

        $this->multiUpdate($postData);
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

        $postData = $this->getServiceLocator()
            ->get('Helper\Data')
            ->arrayRepeat(
                array_merge($defaults, $data),
                $count
            );

        $this->multiCreate($postData);
    }

    /**
     * Thin wrapper around requestDiscs with a few more fields blanked
     */
    public function requestBlankDiscs($licenceId, $count)
    {
        $data = array(
            'licence' => $licenceId,
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null
        );

        $this->requestDiscs($count, $data);
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
        $ids = array_map(
            function ($v) {
                return $v['id'];
            },
            $results['Results']
        );

        $this->ceaseDiscs($ids);

        return $this->requestBlankDiscs($licenceId, $results['Count']);
    }
}
