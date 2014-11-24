<?php

/**
 * Html Translated Element
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Types;

use Common\Form\Elements\Types\Html;

/**
 * Html Translated Element
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class HtmlTranslated extends Html
{
    /**
     * @var array
     */
    protected $tokens = array();

    /**
     * Set the tokens to be translated
     *
     * @param array $tokens
     * @return Element|ElementInterface
     */
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
        return $this;
    }

    /**
     * Get the tokens to be translated
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['tokens']) && is_array($options['tokens'])) {
            $this->setTokens($options['tokens']);
        }

        return $this;
    }
}
