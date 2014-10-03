<?php

/**
 * Abstract Variation Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\VehicleSafety;

/**
 * Abstract Variation Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVariationDiscsPsvSectionServiceTestCase extends AbstractDiscsPsvSectionServiceTestCase
{
    /**
     * Check that the variation form still has form actions after altering
     *
     * @group section_service
     * @group disc_psv_section_service
     */
    public function testAlterForm()
    {
        $form = parent::testAlterForm();

        $this->assertTrue($form->has('form-actions'));
    }
}
