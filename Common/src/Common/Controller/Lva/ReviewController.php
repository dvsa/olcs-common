<?php

namespace Common\Controller\Lva;

use Laminas\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Query\Application\Review;

/**
 * Review Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReviewController extends AbstractController implements Interfaces\AdapterAwareInterface
{
    use Traits\AdapterAwareTrait;

    /**
     * Review application action
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $response = $this->handleQuery(Review::create(['id' => $this->params('application')]));
        if ($response->isForbidden()) {
            return $this->notFoundAction();
        }
        $data = $response->getResult();

        $view = new ViewModel(['content' => $data['markup']]);

        $view->setTerminal(true);
        $view->setTemplate('layout/blank');

        return $view;
    }
}
