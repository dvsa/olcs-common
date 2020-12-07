<?php

namespace Common\Service\Data;

use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract data service class
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
abstract class AbstractDataService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Handle query
     *
     * @param string $dtoData Query dto
     *
     * @return \Common\Service\Cqrs\Response
     */
    protected function handleQuery($dtoData)
    {
        $serviceLocator = $this->getServiceLocator();

        $query = $serviceLocator->get('TransferAnnotationBuilder')->createQuery($dtoData);

        return $serviceLocator->get('QueryService')->send($query);
    }

    /**
     * Handle command
     *
     * @param string $dtoData Command dto
     *
     * @return \Common\Service\Cqrs\Response
     */
    protected function handleCommand($dtoData)
    {
        $serviceLocator = $this->getServiceLocator();

        $command = $serviceLocator->get('TransferAnnotationBuilder')->createCommand($dtoData);

        return $serviceLocator->get('CommandService')->send($command);
    }

    /**
     * Format result
     *
     * @param array $result Result
     *
     * @return array
     */
    protected function formatResult($result)
    {
        // For backwards compatibility we need to return result with keys starting with uppercase letters
        return [
            'Results' => $result['results'],
            'Count' => $result['count']
        ];
    }

    /**
     * Set data
     *
     * @param string $key  Key
     * @param mixed  $data Data
     *
     * @return $this
     */
    public function setData($key, $data)
    {
        $this->data[$key] = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @param string $key Key
     *
     * @return mixed|null
     */
    public function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }
}
