<?php

declare(strict_types=1);

namespace CommonTest\Form\Element;

use Common\Test\MockeryTestCase;
use Common\Form\Element\Button;
use InvalidArgumentException;
use Common\Form\Element\Attribute\ClassList;
use Mockery;

/**
 * @covers \Common\Form\Element\Button
 */
class ButtonTest extends MockeryTestCase
{
    protected const TYPE_ATTRIBUTE = 'type';
    protected const A_BUTTON_NAME = 'A BUTTON NAME';
    protected const A_BUTTON_LABEL = 'A BUTTON LABEL';
    protected const CLASS_ATTRIBUTE = 'class';
    protected const AN_INVALID_BUTTON_TYPE = 'AN INVALID BUTTON TYPE';
    protected const INVALID_BUTTON_TYPE_MESSAGE = 'Invalid type';
    protected const EMPTY_ARRAY = [];
    protected const AN_INVALID_BUTTON_SIZE = 'AN INVALID BUTTON SIZE';
    protected const INVALID_BUTTON_SIZE_MESSAGE = 'Invalid button size';
    protected const AN_INVALID_THEME = 'AN INVALID THEME';
    protected const INVALID_BUTTON_THEME_MESSAGE = 'Invalid button theme';

    /**
     * @var Button|null
     */
    protected $sut;

    /**
     * @test
     */
    public function __construct_SetsTypeAttributeToButton()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertEquals(Button::BUTTON, $this->sut->getAttribute(static::TYPE_ATTRIBUTE));
    }

    /**
     * @test
     */
    public function __construct_SetsClassToClassList()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertInstanceOf(ClassList::class, $this->sut->getAttribute(static::CLASS_ATTRIBUTE));
    }

    /**
     * @test
     */
    public function __construct_SetsName()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertEquals(static::A_BUTTON_NAME, $this->sut->getName());
    }

    /**
     * @test
     */
    public function __construct_SetsLabel()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertEquals(static::A_BUTTON_LABEL, $this->sut->getLabel());
    }

    /**
     * @test
     */
    public function __construct_SetsSizeToLarge()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertTrue($this->sut->getAttribute('class')->has(Button::LARGE));
    }

    /**
     * @test
     */
    public function setAttribute_IsCallable()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertIsCallable([$this->sut, 'setAttribute']);
    }

    /**
     * @test
     * @depends setAttribute_IsCallable
     */
    public function setAttribute_ThrowsExceptionIfButtonTypeIsInvalid()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(static::INVALID_BUTTON_TYPE_MESSAGE);

        // Execute
        $this->sut->setAttribute(static::TYPE_ATTRIBUTE, static::AN_INVALID_BUTTON_TYPE);
    }

    /**
     * @return array
     */
    public function validButtonTypesDataProvider(): array
    {
        return [
            'type button' => [Button::BUTTON],
            'type submit' => [Button::SUBMIT],
            'type reset' => [Button::RESET],
        ];
    }

    /**
     * @param string $buttonType
     * @test
     * @depends setAttribute_IsCallable
     * @dataProvider validButtonTypesDataProvider
     */
    public function setAttribute_AcceptsValidButtonTypes(string $buttonType)
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Execute
        $this->sut->setAttribute(static::TYPE_ATTRIBUTE, $buttonType);

        // Assert
        $this->assertTrue(true);
    }

    protected const A_CLASS = 'A_CLASS';
    protected const B_CLASS = 'B_CLASS';
    protected const AB_STRING_CLASS_LIST = self::A_CLASS . ' ' . self::B_CLASS;
    protected const AB_ARRAY_CLASS_LIST = [self::A_CLASS, self::B_CLASS];

    /**
     * @test
     * @depends setAttribute_IsCallable
     */
    public function setAttribute_ConvertsClassValuesToClassLists_WhenSettingAStringClassList()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Execute
        $this->sut->setAttribute(static::CLASS_ATTRIBUTE, static::AB_STRING_CLASS_LIST);
        $result = $this->sut->getAttribute('class');

        // Assert
        $this->assertInstanceOf(ClassList::class, $result);
        $this->assertEquals(ClassList::fromString(static::AB_STRING_CLASS_LIST), $result);
    }

    /**
     * @test
     * @depends setAttribute_IsCallable
     */
    public function setAttribute_ConvertsClassValuesToClassLists_WhenSettingAnArrayClassList()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Execute
        $this->sut->setAttribute(static::CLASS_ATTRIBUTE, static::AB_ARRAY_CLASS_LIST);
        $result = $this->sut->getAttribute('class');

        // Assert
        $this->assertInstanceOf(ClassList::class, $result);
        $this->assertEquals(new ClassList(static::AB_ARRAY_CLASS_LIST), $result);
    }

    /**
     * @test
     * @depends setAttribute_IsCallable
     */
    public function setAttribute_KeepsClassLists_WhenSettingAClassList()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Execute
        $this->sut->setAttribute(static::CLASS_ATTRIBUTE, $classList = ClassList::fromString(static::AB_STRING_CLASS_LIST));
        $result = $this->sut->getAttribute(static::CLASS_ATTRIBUTE);

            // Assert
        $this->assertSame($classList, $result);
    }

    /**
     * @test
     */
    public function setSize_IsCallable()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertIsCallable([$this->sut, 'setSize']);
    }

    /**
     * @test
     * @depends setSize_IsCallable
     */
    public function setSize_ThrowsExceptionIfSizeValueIsInvalid()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(static::INVALID_BUTTON_SIZE_MESSAGE);

        // Execute
        $this->sut->setSize(static::AN_INVALID_BUTTON_SIZE);
    }

    public function validButtonSizeDataProvider(): array
    {
        return [
            'large size' => [Button::LARGE],
        ];
    }

    /**
     * @test
     * @depends setSize_IsCallable
     * @dataProvider validButtonSizeDataProvider
     */
    public function setSize_AcceptsValidSizes(string $val)
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Execute
        $this->sut->setSize($val);

        // Assert
        $this->assertTrue(true);
    }

    /**
     * @test
     * @depends setSize_AcceptsValidSizes
     */
    public function setSize_SetsSizeAsClass()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Execute
        $this->sut->setSize(Button::LARGE);

        // Assert
        $this->assertTrue($this->sut->getAttribute('class')->has(Button::LARGE));
    }

    /**
     * @test
     * @depends setSize_AcceptsValidSizes
     */
    public function setSize_RemovesAnyExistingSizeClassesFromClassList()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);
        $mockClassList = Mockery::mock(ClassList::class)->makePartial();
        $this->sut->setAttribute('class', $mockClassList);

        // Expect
        $mockClassList->expects('remove')->with(Button::SIZES);

        // Execute
        $this->sut->setSize(Button::LARGE);
    }

    /**
     * @test
     */
    public function setTheme_IsCallable()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Assert
        $this->assertIsCallable([$this->sut, 'setTheme']);
    }

    /**
     * @test
     */
    public function setTheme_ThrowsExceptionIfThemeIsInvalid()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(static::INVALID_BUTTON_THEME_MESSAGE);

        // Execute
        $this->sut->setTheme(static::AN_INVALID_THEME);
    }

    /**
     * @return array[]
     */
    public function validThemesDataProvider(): array
    {
        return [
            'primary theme' => [Button::PRIMARY],
            'tertiary theme' => [Button::TERTIARY],
        ];
    }

    /**
     * @param string $theme
     * @test
     * @depends setTheme_IsCallable
     * @dataProvider validThemesDataProvider
     */
    public function setTheme_AcceptsValidThemes(string $theme)
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);

        // Execute
        $this->sut->setTheme($theme);

        // Assert
        $this->assertTrue($this->sut->getAttribute('class')->has($theme));
    }

    /**
     * @test
     * @depends setTheme_IsCallable
     */
    public function setTheme_RemovesAnyExistingThemes()
    {
        // Setup
        $this->setUpSut(static::A_BUTTON_NAME, static::A_BUTTON_LABEL);
        $mockClassList = Mockery::mock(ClassList::class)->makePartial();
        $this->sut->setAttribute('class', $mockClassList);

        // Expect
        $mockClassList->expects('remove')->with(Button::THEMES);

        // Execute
        $this->sut->setTheme(Button::PRIMARY);
    }

    protected function setUpSut(...$args)
    {
        $this->sut = new Button(...$args);
    }
}
