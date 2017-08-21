<?php

namespace Common\View\Helper;

use Common\Service\Cqrs\Query\CachingQueryService as QueryService;
use Common\View\Helper\Traits\Utils;
use Dvsa\Olcs\Transfer\Query\System\InfoMessage\GetListActive;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * View helper to print system info messages
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class SystemInfoMessages extends AbstractHelper
{
    use Utils;

    const HTML_BLOCK = '<div class="system-messages">%s</div>';
    const HTML_ITEM = '<div class="system-messages__wrapper"><p>%s</p></div>';

    /** @var AnnotationBuilder */
    protected $annotationBuilder;
    /** @var QueryService */
    protected $queryService;

    /** @var  array */
    protected $mssgs;

    public function __construct(
        AnnotationBuilder $annotationBuilder,
        QueryService $querySrv
    ) {
        $this->annotationBuilder = $annotationBuilder;
        $this->queryService = $querySrv;
    }

    /**
     * @param boolean $isInternal
     *
     * @return string
     */
    public function __invoke($isInternal)
    {
        $this->getData($isInternal);

        return $this->render();
    }

    private function render()
    {
        if ($this->mssgs === null || !isset($this->mssgs['results']) || $this->mssgs['count'] === 0) {
            return null;
        }

        $items = [];
        foreach ($this->mssgs['results'] as $msg) {
            $items[] = sprintf(self::HTML_ITEM, $this->escapeHtml($msg['description']));
        }

        return sprintf(self::HTML_BLOCK, implode('', $items));
    }

    private function getData($isInternal)
    {
        $qry = GetListActive::create(['isInternal' => $isInternal]);

        $qryContainer = $this->annotationBuilder->createQuery($qry);
        $response = $this->queryService->send($qryContainer);

        $this->mssgs = ($response->isOk() ? $response->getResult() : null);

        return $this;
    }
}
