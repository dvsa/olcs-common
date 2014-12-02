<?php

namespace Common\Service\Data\Interfaces;

use Common\Data\Object\Bundle;

/**
 * Interface BundleAware
 * @package Common\Service\Data\Interfaces
 */
interface BundleAware
{
    /**
     * A setter for the default bundle, used when one isn't explicitly passed
     *
     * @param Bundle $bundle
     * @return DataService
     */
    public function setBundle(Bundle $bundle);

    /**
     * Should return the name of the default bundle that this service uses, this can then be retrieved from the bundle
     * manager and injected into the class.
     *
     * @return string
     */
    public function getDefaultBundleName();
}