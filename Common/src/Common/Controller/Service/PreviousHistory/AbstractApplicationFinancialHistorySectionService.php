<?php

/**
 * Abstract Application Financial History Section Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Service\PreviousHistory;

use Zend\Form\Form;

/**
 * Abstract Application Financial History Section Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractApplicationFinancialHistorySectionService extends AbstractFinancialHistorySectionService
{
    /**
     * Holds the section service
     *
     * @var string
     */
    protected $service = 'Application';

    /**
     * Data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'bankrupt',
            'liquidation',
            'receivership',
            'administration',
            'disqualified',
            'insolvencyDetails',
            'insolvencyConfirmation'
        ),
        'children' => array(
            'documents' => array(
                'properties' => array(
                    'id',
                    'version',
                    'filename',
                    'identifier',
                    'size'
                )
            )
        )
    );

    /**
     * Get licence section service
     *
     * @NOTE Needs DRYing up with AbstractApplicationAuthorisationSectionService
     * as they both implement this method (declared abstract in our parent)
     * in exactly the same way. Could do it as a trait, but the name would
     * need thinking about a bit
     *
     * Essentially just need all this stuff to bed in a bit when these
     * dependencies should become a bit clearer
     *
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    protected function getLicenceSectionService()
    {
        return $this->getSectionService('Application')->getLicenceSectionService();
    }
}
