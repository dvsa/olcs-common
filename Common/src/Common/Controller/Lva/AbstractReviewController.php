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
        $response = $this->handleQuery(Review::create(['id' => $this->params('application')]));
        $reviewData = $response->getResult();

        return new ReviewViewModel(
            $this->buildReadonlyConfigForSections($reviewData['sections'], $reviewData)
        );
    }

    protected function buildReadonlyConfigForSections($sections, $reviewData)
    {
        $entity = ucfirst($this->lva);

        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        $sectionConfig = [];

        foreach ($sections as $section) {
            $serviceName = 'Review\\' . $entity . $stringHelper->underscoreToCamel($section);
            $config = null;

            // @NOTE this check is in place while we implement each section
            // eventually we should be able to remove the if
            if ($this->getServiceLocator()->has($serviceName)) {
                $service = $this->getServiceLocator()->get($serviceName);
                $config = $service->getConfigFromData($reviewData);
            }

            $sectionConfig[] = [
                'header' => 'review-' . $section,
                'config' => $config
            ];
        }

        return [
            'reviewTitle' => $this->getTitle($reviewData),
            'subTitle' => $this->getSubTitle($reviewData),
            'sections' => $sectionConfig
        ];
    }

    protected function getSubTitle($data)
    {
        return sprintf('%s %s/%s', $data['licence']['organisation']['name'], $data['licence']['licNo'], $data['id']);
    }

    protected function getTitle($data)
    {
        return sprintf(
            '%s-review-title-%s%s',
            $this->lva,
            $data['isGoods'] ? 'gv' : 'psv',
            $this->isNewPsvSpecialRestricted($data) ? '-sr' : ''
        );
    }

    protected function isNewPsvSpecialRestricted($data)
    {
        return $this->lva === 'application' && !$data['isGoods'] && $data['isSpecialRestricted'];
    }
}
