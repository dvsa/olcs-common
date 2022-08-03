<?php

namespace Common\Service\Data;

/**
 * Abstract data service class
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
abstract class AbstractDataService
{
    /**
     * @var array
     */
    protected $data = [];

    /** @var TransferAnnotationBuilder */
    protected $transferAnnotationBuilder;

    /** @var QueryService */
    protected $queryService;

    /** @var CommandService */
    protected $commandService;

    /**
     * Create service instance
     *
     * @param AbstractDataServiceServices $abstractConsumerServices
     *
     * @return AbstractDataService
     */
    public function __construct(AbstractDataServiceServices $abstractDataServiceServices)
    {
        $this->transferAnnotationBuilder = $abstractDataServiceServices->getTransferAnnotationBuilder();
        $this->queryService = $abstractDataServiceServices->getQueryService();
        $this->commandService = $abstractDataServiceServices->getCommandService();
    }

    /**
     * Handle query
     *
     * @param string $dtoData Query dto
     *
     * @return \Common\Service\Cqrs\Response
     */
    protected function handleQuery($dtoData)
    {
        $query = $this->transferAnnotationBuilder->createQuery($dtoData);

        return $this->queryService->send($query);
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
        $command = $this->transferAnnotationBuilder->createCommand($dtoData);

        return $this->commandService->send($command);
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
