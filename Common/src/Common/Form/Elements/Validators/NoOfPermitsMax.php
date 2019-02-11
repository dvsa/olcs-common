<?php

/**
 * Number of permits maximum validator
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\LessThan;

/**
 * Number of permits maximum validator
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsMax extends LessThan
{
    protected $messageTemplates = [
        self::NOT_LESS_INCLUSIVE => 'permits.page.bilateral.no-of-permits.error.max-exceeded'
    ];

    public function __construct($max)
    {
        parent::__construct(
            [
                'max' => $max,
                'inclusive' => true
            ]
        );
    }
}
