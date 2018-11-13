<?php

namespace Common\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Class return translateReplace to view
 */
class TranslateReplace extends AbstractHelper implements FactoryInterface
{
    /** @var  \Common\Service\Helper\TranslationHelperService */
    private $translator;

    /**
     * Factory
     *
     * @param \Zend\View\HelperPluginManager $sl Service Manager
     *
     * @return $this;
     */
    public function createService(ServiceLocatorInterface $sl)
    {

        $this->translator = $sl->getServiceLocator()->get('Helper\Translation');

        return $this;
    }

    /**
     * Allows you to replace variables after the string is translated
     *
     * @param string $translationKey
     * @param array $arguments
     * @param string $translateToWelsh 'Y' or 'N', Force the translation into welsh
     * @return string
     */
    public function __invoke($translationKey, array $arguments, $translateToWelsh = 'N')
    {
        return $this->translator->translateReplace($translationKey, $arguments, $translateToWelsh);
    }
}
