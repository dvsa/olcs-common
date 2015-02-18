<?php

/**
 * Abstract Review Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\View\Model\ReviewViewModel;

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
        return new ReviewViewModel(
            $this->getAdapter()->getSectionData(
                $this->params()->fromRoute('application'),
                $this->getAccessibleSections(true)
            )
        );
    }

    /**
     * @NOTE Need to implement this as it's declared abstract in AbstractController
     *
     * @param int $lvaId
     */
    protected function handleCancelRedirect($lvaId)
    {
        // No-op
    }
}
