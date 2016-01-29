<?php

/**
 * Translator Delegator Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Util;

use Common\Util\TranslatorDelegator;
use Common\Util\TranslatorDelegatorFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator Delegator Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslatorDelegatorFactoryTest extends MockeryTestCase
{
    public function testCreatedDelegatorWithName()
    {
        $config = [
            'translator' => [
                'replacements' => [
                    'foo' => 'bar'
                ]
            ]
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);

        $name = 'foo';
        $requestedName = 'foo';

        $realTranslator = m::mock(Translator::class);

        $callback = function () use ($realTranslator) {
            return $realTranslator;
        };

        $sut = new TranslatorDelegatorFactory();
        $return = $sut->createDelegatorWithName($sm, $name, $requestedName, $callback);

        $this->assertInstanceOf(TranslatorDelegator::class, $return);
    }
}
