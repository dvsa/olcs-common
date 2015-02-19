<?php

/**
 * Review View Model
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Model;

use Zend\View\Model\ViewModel;

/**
 * Review View Model
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReviewViewModel extends ViewModel
{
    protected $terminate = true;
    protected $template = 'layout/review';
}
