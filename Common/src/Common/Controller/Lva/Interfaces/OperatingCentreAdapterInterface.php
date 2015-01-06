<?php

/**
 * Operating Centre Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Operating Centre Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface OperatingCentreAdapterInterface extends AdapterInterface
{
    /**
     * Get extra document properties to save
     *
     * @return array
     */
    public function getDocumentProperties();

    /**
     * Get operating centre form data
     *
     * @param int $id
     * @return array
     */
    public function getOperatingCentresFormData($id);
}
