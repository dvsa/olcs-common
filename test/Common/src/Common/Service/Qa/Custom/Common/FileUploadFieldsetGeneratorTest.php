<?php

namespace CommonTest\Service\Qa\Custom\Common;

use Common\Form\Annotation\CustomAnnotationBuilder;
use Common\Form\Model\Fieldset\MultipleFileUpload;
use Common\Service\Qa\Custom\Common\FileUploadFieldsetGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Factory as FormFactory;
use Zend\Form\InputFilterProviderFieldset;

/**
 * FileUploadFieldsetGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FileUploadFieldsetGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $multipleFileUploadSpec = [
            'input_filter' => [
                'fileCount' => [
                    'fileCountAttribute1' => 'fileCountValue1',
                    'fileCountAttribute2' => 'fileCountValue2'
                ]
            ],
            'otherAttribute1' => 'otherValue1',
            'otherAttribute2' => 'otherValue2',
        ];

        $updatedInputFilter = [
            'fileCount' => [
                'fileCountAttribute1' => 'fileCountValue1',
                'fileCountAttribute2' => 'fileCountValue2',
                'continue_if_empty' => true,
            ]
        ];

        $updatedMultipleFileUploadSpec = [
            'input_filter' => $updatedInputFilter,
            'otherAttribute1' => 'otherValue1',
            'otherAttribute2' => 'otherValue2',
            'type' => InputFilterProviderFieldset::class
        ];

        $fieldset = m::mock(InputFilterProviderFieldset::class);
        $fieldset->shouldReceive('setInputFilterSpecification')
            ->with($updatedInputFilter)
            ->once();

        $formFactory = m::mock(FormFactory::class);
        $formFactory->shouldReceive('create')
            ->with($updatedMultipleFileUploadSpec)
            ->once()
            ->andReturn($fieldset);

        $customAnnotationBuilder = m::mock(CustomAnnotationBuilder::class);
        $customAnnotationBuilder->shouldReceive('getFormSpecification')
            ->with(MultipleFileUpload::class)
            ->once()
            ->andReturn($multipleFileUploadSpec);

        $fileUploadFieldsetGenerator = new FileUploadFieldsetGenerator($formFactory, $customAnnotationBuilder);

        $this->assertSame(
            $fieldset,
            $fileUploadFieldsetGenerator->generate()
        );
    }
}
