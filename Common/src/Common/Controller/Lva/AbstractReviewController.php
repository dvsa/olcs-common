<?php

/**
 * Abstract Review Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\View\Model\ReviewViewModel;
use Dvsa\Olcs\Transfer\Query\Application\Review;

/**
 * Abstract Review Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReviewController extends AbstractController implements Interfaces\AdapterAwareInterface
{
    use Traits\AdapterAwareTrait;

    public function indexAction()
    {
        // @todo grab markup from backend
//        $response = $this->handleQuery(Review::create(['id' => $this->params('application')]));
//        $reviewData = $response->getResult();
//
//        return new ReviewViewModel();
    }
}
