<?php

/**
 * Abstract Licence Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\VehicleSafety;

/**
 * Abstract Licence Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLicenceDiscsPsvSectionServiceTestCase extends AbstractDiscsPsvSectionServiceTestCase
{
    /**
     * Check that the licence form doesn't have form actions after altering
     *
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testAlterForm()
    {
        $form = parent::testAlterForm();

        $this->assertFalse($form->has('form-actions'));
    }
}
