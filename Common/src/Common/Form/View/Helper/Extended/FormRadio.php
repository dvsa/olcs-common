<?php

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\View\Helper\Extended;

use Zend\Form\ElementInterface;

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormRadio extends \Zend\Form\View\Helper\FormRadio
{
    use PrepareAttributesTrait;

    public function __invoke(ElementInterface $element = null, $labelPosition = null)
    {
        $rendered = parent::__invoke($element, $labelPosition);

        // Zend only renders radios within label elements, this is unsupported by new GDS styles.
        if (preg_match('/govuk-radios__label/', $rendered)) {
            // Change the rendered HTML, by moving the input outside of the label
            preg_match_all('/<input.*?>/', $rendered, $matchedInputs);
            $renderedWithoutInputs = preg_replace('/<input.*?>/', '', $rendered);
            preg_match_all('/<label.*?<\/label>/', $renderedWithoutInputs, $matchedLabels);

            $rendered = '';

            foreach ($matchedInputs[0] as $key => $match) {
                $label = preg_split('/@/', $matchedLabels[0][$key]);

                if (count($label) === 1) {
                    $rendered .= '<div class="govuk-radios__item">'
                        . $matchedInputs[0][$key] . $label[0]
                        . '</div>';
                } else {
                    $rendered .= '<div class="govuk-radios__item">'
                        . $matchedInputs[0][$key] . $label[0]
                        . '</label>'
                        . '<span class="govuk-hint govuk-radios__hint">'
                        . $label[1]
                        . '</span>'
                        . '</div>';
                }
            }
        }

        return $rendered;
    }
}
