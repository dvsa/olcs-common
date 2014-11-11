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
}
