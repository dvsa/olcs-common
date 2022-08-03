<?php

namespace Common\Service\Data;

/**
 * AbstractListDataServiceServices
 */
class AbstractListDataServiceServices
{
    /** @var AbstractDataServiceServices */
    protected $abstractDataServiceServices;

    /**
     * Create service instance
     *
     * @param AbstractDataServiceServices $abstractDataServiceServices
     *
     * @return AbstractListDataServiceServices
     */
    public function __construct(AbstractDataServiceServices $abstractDataServiceServices)
    {
        $this->abstractDataServiceServices = $abstractDataServiceServices;
    }

    /**
     * Return the AbstractDataServiceServices
     *
     * @return AbstractDataServiceServices
     */
    public function getAbstractDataServiceServices(): AbstractDataServiceServices
    {
        return $this->abstractDataServiceServices;
    }
}
