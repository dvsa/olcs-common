<?php

namespace Common\Controller\Continuation;

use Laminas\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\Review as ReviewQuery;

/**
 * Review controller
 */
class ReviewController extends AbstractContinuationController
{
    /**
     * Index page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $reviewData = $this->getReviewData($this->getContinuationDetailId());
        $view = new ViewModel(['content' => $reviewData]);

        $view->setTerminal(true);
        $view->setTemplate('layout/blank');
        return $view;
    }

    /**
     * Get review data
     *
     * @param int $continuationDetailId continuation detail id
     *
     * @return array
     */
    protected function getReviewData($continuationDetailId)
    {
        $dto = ReviewQuery::create(['id' => $continuationDetailId]);
        $response = $this->handleQuery($dto);
        if (!$response->isOk()) {
            $this->addErrorMessage('unknown-error');
        }
        return $response->getResult()['markup'];
    }
}
