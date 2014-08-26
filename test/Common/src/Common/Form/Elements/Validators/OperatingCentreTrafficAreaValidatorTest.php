<?php

/**
 * Test OperatingCentreTrafficAreaValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Elements\Validators;

use Common\Form\Elements\Validators\OperatingCentreTrafficAreaValidator;
use CommonTest\Bootstrap;

/**
 * Test OperatingCentreTrafficAreaValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OperatingCentreTrafficAreaValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->validator = new OperatingCentreTrafficAreaValidator();
    }

    /**
     * Test isValid
     *
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $niFlag, $trafficArea, $expected)
    {

        $mockValidator = $this->getMock(
            'Common\Form\Elements\Validators\OperatingCentreTrafficAreaValidator',
            ['getServiceLocator']
        );
        $mockValidator->setNiFlag($niFlag);
        $mockValidator->setTrafficArea($trafficArea);

        $postcode = $this->getMock('stdClass', ['getTrafficAreaByPostcode']);
        $postcode->expects($this->any())
            ->method('getTrafficAreaByPostcode')
            ->will(
                $this->returnValueMap(
                    [
                        ['E15 1HS', ['K', 'London and the South East of England']],
                        ['LS1 4ES', ['B', 'North East of England']],
                        ['BF1 1EE', ['N', 'Northern Ireland']],
                        ['WRONGCODE', [null, null]],
                        ['', [null, null]],
                    ]
                )
            );

        $serviceLocator = $this->getMock('stdClass', ['get']);
        $serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnValue($postcode));

        $mockValidator->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocator));

        $this->assertEquals($expected, $mockValidator->isValid($value));

    }

    /**
     * Provider for isValid
     *
     * @return array
     */
    public function providerIsValid()
    {
        return [
            // Northern Ireland - first instance
            ['E15 1HS', 'Y', null, false],
            ['WRONGCODE', 'Y', null, true],
            ['', 'Y', null, true],
            ['BF1 1EE', 'Y', null, true],

            // Northern Ireland - second instance
            ['E15 1HS', 'Y', ['id' => 'N'], false],
            ['', 'Y', ['id' => 'N'], true],
            ['WRONGCODE', 'Y', ['id' => 'N'], true],

            // Any Traffic Area - first instance
            ['E15 1HS', 'N', null, true],
            ['', 'N', null, true],
            ['WRONGCODE', 'N', null, true],

            // Any Traffic Area - second instance
            ['E15 1HS', 'N', ['id' => 'K'], true],
            ['LS1 4ES', 'N', ['id' => 'K', 'name' => 'London and the South East of England'], false],
            ['', 'N', ['id' => 'K'], true],
            ['WRONGCODE', 'N', ['id' => 'K'], true],

        ];
    }

    /**
     * Test setNiFlag
     *
     * @dataProvider providerSetNiFlag
     */
    public function testNiFlag($input, $output)
    {
        $this->validator->setNiFlag($input);
        $this->assertEquals($output, $this->validator->getNiFlag());
    }

    /**
     * Provider for setNiFlag
     */
    public function providerSetNiFlag()
    {
        return array(
            array('NiFlag', 'NiFlag')
        );
    }

    /**
     * Test setOperatingCentresCount
     *
     * @dataProvider providerSetOperatingCentresCount
     */
    public function testOperatingCentresCount($input, $output)
    {
        $this->validator->setOperatingCentresCount($input);
        $this->assertEquals($output, $this->validator->getOperatingCentresCount());
    }

    /**
     * Provider for setOperatingCentresCount
     */
    public function providerSetOperatingCentresCount()
    {
        return array(
            array('OperatingCentresCount', 'OperatingCentresCount')
        );
    }

    /**
     * Test setTrafficArea
     *
     * @dataProvider providerSetTrafficArea
     */
    public function testTrafficArea($input, $output)
    {
        $this->validator->setTrafficArea($input);
        $this->assertEquals($output, $this->validator->getTrafficArea());
    }

    /**
     * Provider for setTrafficArea
     */
    public function providerSetTrafficArea()
    {
        return array(
            array('TrafficArea', 'TrafficArea')
        );
    }

    /**
     * Test setServiceLocator
     *
     * @dataProvider providerSetServiceLocator
     */
    public function testServiceLocator($input, $output)
    {
        $this->validator->setServiceLocator($input);
        $this->assertInstanceOf(get_class($output), $this->validator->getServiceLocator());
    }

    /**
     * Provider for setServiceLocator
     */
    public function providerSetServiceLocator()
    {
        $serviceManager = Bootstrap::getServiceManager();
        return array(
            array($serviceManager, $serviceManager)
        );
    }
}
