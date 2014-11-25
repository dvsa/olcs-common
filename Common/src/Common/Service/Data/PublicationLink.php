<?php

/**
 * Publication Link service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Util\RestClient;
use Common\Data\Object\Publication;

/**
 * Publication Link service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationLink extends AbstractData implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $serviceName = 'PublicationLink';

    /**
     * @return ArrayObject
     */
    public function createEmpty()
    {
        return new Publication();
    }

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return mixed
     */
    public function save(Publication $publication)
    {
        return $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
            'PublicationLink',
            'POST',
            $publication->getArrayCopy()
        );
    }

    /**
     * @param \Common\Data\Object\Publication $publication
     * @param string $service
     * @return mixed
     */
    public function createPublicationLink(Publication $publication, $service)
    {
        $publication = $this->getServiceLocator()->get($service)->filter($publication);
        return $this->save($publication);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function fetchPublicationLinkData($params)
    {
        $params['bundle'] = json_encode($this->getBundle());
        return $this->getRestClient()->get($params);
    }

    /**
     * @return array
     */
    private function getBundle()
    {
        return [
            'properties' => 'ALL',
            'children' => [
                'publication' => [
                    'properties' => 'ALL'
                ]
            ]
        ];
    }
}
