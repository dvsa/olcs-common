<?php

/**
 * Inspect Request Email View Model
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\View\Model;

use Zend\View\Model\ViewModel;

/**
 * Inspect Request Email View Model
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InspectionRequestEmailViewModel extends ViewModel
{
    protected $terminate = true;
    protected $template = 'email/inspection-request';
}
