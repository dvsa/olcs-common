<?php

namespace Common\Controller\Lva;

use Common\Service\Helper\FormHelperService;
use Dvsa\Olcs\Transfer\Command\Application\CreateWorkshop as ApplicationCreateWorkshop;
use Dvsa\Olcs\Transfer\Command\Application\UpdateWorkshop as ApplicationUpdateWorkshop;
use Dvsa\Olcs\Transfer\Command\Workshop\CreateWorkshop as LicenceCreateWorkshop;
use Dvsa\Olcs\Transfer\Command\Workshop\UpdateWorkshop as LicenceUpdateWorkshop;
use Dvsa\Olcs\Transfer\Query\Workshop\Workshop;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * Safety Trait
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
abstract class AbstractEcmtController extends AbstractController
{
    use Traits\CrudTableTrait;

    const DEFAULT_TABLE_RECORDS_COUNT = 10;

    protected $section = 'ecmt';
    protected $baseRoute = 'lva-%s/ecmt';



}
