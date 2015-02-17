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
            [
                'sections' => $this->getAdapter()->getSectionData(
                    $this->params()->fromRoute('application'),
                    $this->getRelevantSections()
                )
            ]
        );
    }

    protected function getRelevantSections()
    {
        return $this->getAccessibleSections(true);
    }

    protected function handleCancelRedirect($lvaId)
    {
        // No-op
    }
}
