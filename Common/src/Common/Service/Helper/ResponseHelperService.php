<?php

/**
 * Miscellaneous response helper service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Helper;

use Zend\Http\Response;
use Common\Service\Table\TableBuilder;

/**
 * Miscellaneous response helper service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ResponseHelperService extends AbstractHelperService
{
    protected $ignoreTableColumns = ['action'];

    public function tableToCsv(Response $response, TableBuilder $table, $name)
    {
        $table->setContentType(TableBuilder::CONTENT_TYPE_CSV);
        foreach ($this->ignoreTableColumns as $column) {
            $table->removeColumn($column);
        }

        $body = $table->render();

        $response->getHeaders()
            ->addHeaderLine('Content-Type', 'text/csv')
            ->addHeaderLine('Content-Disposition', sprintf('attachment; filename="%s.csv"', $name))
            ->addHeaderLine('Content-Length', strlen($body));

        $response->setContent($body);

        return $response;
    }
}
