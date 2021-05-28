<?php

declare(strict_types=1);

namespace CommonTest\Form\Element\Attribute;

use Common\Test\MockeryTestCase;
use Common\Form\Element\Attribute\ClassList;

/**
 * @covers \Common\Form\Element\Attribute\ClassList
 */
class ClassListTest extends MockeryTestCase
{
    protected const A_CLASS = 'A_CLASS';
    protected const A_CLASS_ARRAY = [self::A_CLASS];
    protected const B_CLASS = 'B_CLASS';
    protected const B_CLASS_ARRAY = [self::B_CLASS];
    protected const AB_CLASS_STRING = self::A_CLASS . ' ' . self::B_CLASS;
    protected const AB_CLASS_ARRAY = [self::A_CLASS, self::B_CLASS];
    protected const EMPTY_CLASS_STRING = '';
    protected const AA_CLASS_ARRAY = [self::A_CLASS, self::A_CLASS];

    /**
     * @var ClassList|null
     */
    protected $sut;

    /**
     * @test
     */
    public function __toString_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__toString']);
    }

    /**
     * @test
     * @depends __toString_IsCallable
     */
    public function __toString_ReturnsAnEmptyString_WhenNoClassesHaveBeenAdded()
    {
        // Setup
        $this->setUpSut();

        $result = (string) $this->sut;

        // Assert
        $this->assertIsString($result);
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function add_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'add']);
    }

    /**
     * @test
     * @depends add_IsCallable
     */
    public function add_ReturnsSelf()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->add(static::A_CLASS);

        // Assert
        $this->assertEquals($this->sut, $result);
    }

    /**
     * @test
     * @depends add_IsCallable
     * @depends __toString_IsCallable
     */
    public function add_AddsAClass_WhenPassedAString()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::A_CLASS);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::A_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     */
    public function add_AddsMultipleClasses_WhenPassedAString()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::AB_CLASS_STRING);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_IsCallable
     * @depends __toString_IsCallable
     */
    public function add_AddsAClass_WhenPassedAnArray()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::A_CLASS_ARRAY);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::A_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     */
    public function add_AddsMultipleClasses_WhenPassedAnArray()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::AB_CLASS_ARRAY);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAnArray
     */
    public function add_AddsMultipleClasses_WhenPassedOneAtATime()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::A_CLASS_ARRAY);
        $this->sut->add(static::B_CLASS_ARRAY);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAnArray
     */
    public function add_AddsAClass_WhenPassedAClassList()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $otherClassList = new ClassList();
        $otherClassList->add(static::A_CLASS_ARRAY);
        $this->sut->add($otherClassList);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::A_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsMultipleClasses_WhenPassedOneAtATime
     */
    public function add_AddsMultipleClasses_WhenPassedAClassList()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $otherClassList = new ClassList();
        $otherClassList->add(static::A_CLASS_ARRAY);
        $otherClassList->add(static::B_CLASS_ARRAY);
        $this->sut->add($otherClassList);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_AddsMultipleClasses_WhenPassedAString
     */
    public function add_DoesNotAddDuplicateClasses()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add([static::A_CLASS, static::A_CLASS, static::B_CLASS]);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_IsCallable
     * @depends __toString_IsCallable
     */
    public function __construct_AddsAClass_WhenPassedAString()
    {
        // Execute
        $this->setUpSut(static::A_CLASS);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::A_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     */
    public function __construct_AddsMultipleClasses_WhenPassedAString()
    {
        // Execute
        $this->setUpSut(static::AB_CLASS_STRING);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_IsCallable
     * @depends __toString_IsCallable
     */
    public function __construct_AddsAClass_WhenPassedAnArray()
    {
        // Execute
        $this->setUpSut(static::A_CLASS_ARRAY);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::A_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     */
    public function __construct_AddsMultipleClasses_WhenPassedAnArray()
    {
        // Execute
        $this->setUpSut(static::AB_CLASS_ARRAY);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAnArray
     */
    public function __construct_AddsAClass_WhenPassedAClassList()
    {
        // Setup
        $otherClassList = new ClassList();
        $otherClassList->add(static::A_CLASS_ARRAY);

        // Execute
        $this->setUpSut($otherClassList);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::A_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsMultipleClasses_WhenPassedOneAtATime
     */
    public function __construct_AddsMultipleClasses_WhenPassedAClassList()
    {
        // Setup
        $otherClassList = new ClassList();
        $otherClassList->add(static::A_CLASS_ARRAY);
        $otherClassList->add(static::B_CLASS_ARRAY);

        // Execute
        $this->setUpSut($otherClassList);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_AddsMultipleClasses_WhenPassedAString
     */
    public function __construct_DoesNotAddDuplicateClasses()
    {
        // Execute
        $this->setUpSut([static::A_CLASS, static::A_CLASS, static::B_CLASS]);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::AB_CLASS_STRING, $result);
    }

    /**
     * @test
     */
    public function remove_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'remove']);
    }

    /**
     * @test
     * @depends remove_IsCallable
     */
    public function remove_RemovesClassThatWasNeverAdded()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->remove(static::A_CLASS);

        // Assert
        $this->assertTrue(true);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     * @depends remove_IsCallable
     */
    public function remove_ReturnsSelf()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::A_CLASS);
        $result = $this->sut->remove(static::A_CLASS);

        // Assert
        $this->assertEquals($this->sut, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     * @depends remove_IsCallable
     */
    public function remove_RemovesAPreviouslyAddedClass_WhenPassedAString()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::A_CLASS);
        $this->sut->add(static::B_CLASS);
        $this->sut->remove(static::A_CLASS);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::B_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     * @depends remove_IsCallable
     * @depends __toString_ReturnsAnEmptyString_WhenNoClassesHaveBeenAdded
     */
    public function remove_RemovesMultiplePreviouslyAddedClasses_WhenPassedAString()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::A_CLASS);
        $this->sut->add(static::B_CLASS);
        $this->sut->remove(static::AB_CLASS_STRING);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::EMPTY_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_AddsMultipleClasses_WhenPassedAnArray
     * @depends __toString_ReturnsAnEmptyString_WhenNoClassesHaveBeenAdded
     */
    public function remove_RemovesAPreviouslyAddedClass_WhenPassedAnArray()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::AB_CLASS_ARRAY);
        $this->sut->remove(static::A_CLASS_ARRAY);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::B_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsAClass_WhenPassedAString
     * @depends __toString_ReturnsAnEmptyString_WhenNoClassesHaveBeenAdded
     */
    public function remove_RemovesMultiplePreviouslyAddedClasses_WhenPassedAnArray()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->add(static::A_CLASS);
        $this->sut->add(static::B_CLASS);
        $this->sut->remove(static::AB_CLASS_ARRAY);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::EMPTY_CLASS_STRING, $result);
    }

    /**
     * @test
     * @depends add_AddsMultipleClasses_WhenPassedAnArray
     * @depends __toString_ReturnsAnEmptyString_WhenNoClassesHaveBeenAdded
     */
    public function remove_RemovesAPreviouslyAddedClass_WhenPassedAClassList()
    {
        // Setup
        $this->setUpSut();
        $otherClassList = new ClassList();
        $otherClassList->add(static::A_CLASS_ARRAY);
        $this->sut->add(static::AB_CLASS_ARRAY);

        // Execute
        $this->sut->remove($otherClassList);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::B_CLASS, $result);
    }

    /**
     * @test
     * @depends add_AddsMultipleClasses_WhenPassedAnArray
     * @depends __toString_ReturnsAnEmptyString_WhenNoClassesHaveBeenAdded
     */
    public function remove_RemovesMultiplePreviouslyAddedClasses_WhenPassedAClassList()
    {
        // Setup
        $this->setUpSut();
        $otherClassList = new ClassList();
        $otherClassList->add(static::AB_CLASS_ARRAY);
        $this->sut->add(static::AB_CLASS_ARRAY);

        // Execute
        $this->sut->remove($otherClassList);
        $result = (string) $this->sut;

        // Assert
        $this->assertEquals(static::EMPTY_CLASS_STRING, $result);
    }

    /**
     * @test
     */
    public function toArray_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'toArray']);
    }

    /**
     * @test
     * @depends toArray_IsCallable
     */
    public function toArray_ReturnsAnArray()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->toArray();

        // Assert
        $this->assertIsArray($result);
    }

    /**
     * @test
     * @depends toArray_ReturnsAnArray
     * @depends __construct_AddsMultipleClasses_WhenPassedAnArray
     */
    public function toArray_ReturnsAnArrayOfClassNames()
    {
        // Setup
        $this->setUpSut(static::AB_CLASS_ARRAY);

        // Execute
        $result = $this->sut->toArray();

        // Assert
        $this->assertEquals(static::AB_CLASS_ARRAY, $result);
    }

    /**
     * @test
     * @depends toArray_ReturnsAnArrayOfClassNames
     */
    public function toArray_ReturnsAnArray_IndexedNumerically()
    {
        // Setup
        $this->setUpSut(static::AB_CLASS_ARRAY);

        // Execute
        $result = $this->sut->toArray();

        // Assert
        $this->assertEquals(array_values($result), $result);
    }

    /**
     * @test
     * @depends toArray_ReturnsAnArrayOfClassNames
     */
    public function toArray_RemovesDuplicateClassNames()
    {
        // Setup
        $this->setUpSut(static::AA_CLASS_ARRAY);

        // Execute
        $result = $this->sut->toArray();

        // Assert
        $this->assertEquals(static::A_CLASS_ARRAY, $result);
    }

    /**
     * @test
     */
    public function fromString_IsCallable()
    {
        // Assert
        $this->assertIsCallable([ClassList::class, 'fromString']);
    }

    /**
     * @test
     * @depends fromString_IsCallable
     */
    public function fromString_ReturnsAClassList()
    {
        // Execute
        $result = ClassList::fromString(static::AB_CLASS_STRING);

        // Assert
        $this->assertInstanceOf(ClassList::class, $result);
    }

    /**
     * @test
     * @depends fromString_ReturnsAClassList
     */
    public function fromString_ReturnsAClassList_WithEachClassInAString()
    {
        // Execute
        $result = ClassList::fromString(static::AB_CLASS_STRING);

        // Assert
        $this->assertEquals(new ClassList(static::AB_CLASS_ARRAY), $result);
    }

    /**
     * @test
     */
    public function has_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'has']);
    }

    /**
     * @test
     * @depends has_IsCallable
     * @depends add_AddsAClass_WhenPassedAString
     */
    public function has_ReturnsFalseWhenAClassListIsEmpty_AndPassedAString()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->has(static::A_CLASS);

        // Assert
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     * @depends has_IsCallable
     * @depends add_AddsAClass_WhenPassedAString
     */
    public function has_ReturnsTrueWhenAStringClassIsInAClassList()
    {
        // Setup
        $this->setUpSut();
        $this->sut->add(static::A_CLASS);

        // Execute
        $result = $this->sut->has(static::A_CLASS);

        // Assert
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     * @depends has_IsCallable
     */
    public function has_ReturnsFalseWhenAClassListIsEmpty_AndPassedAnArray()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->has(static::A_CLASS_ARRAY);

        // Assert
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     * @depends has_IsCallable
     * @depends add_AddsMultipleClasses_WhenPassedAnArray
     */
    public function has_ReturnsTrueWhenAllClassesInAnArrayOfClassesAreInAClassList()
    {
        // Setup
        $this->setUpSut(static::AB_CLASS_ARRAY);

        // Execute
        $result = $this->sut->has(static::A_CLASS_ARRAY);

        // Assert
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     * @depends has_IsCallable
     * @depends __construct_AddsMultipleClasses_WhenPassedAnArray
     */
    public function has_ReturnsFalseWhenAClassListIsEmpty_AndPassedAClassList()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->has(new ClassList(static::A_CLASS_ARRAY));

        // Assert
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     * @depends has_IsCallable
     * @depends add_AddsMultipleClasses_WhenPassedAnArray
     * @depends __construct_AddsMultipleClasses_WhenPassedAnArray
     */
    public function has_ReturnsTrueWhenAllClassesOfAClassListAreInAClassList()
    {
        // Setup
        $this->setUpSut(static::AB_CLASS_ARRAY);

        // Execute
        $result = $this->sut->has(new ClassList(static::A_CLASS_ARRAY));

        // Assert
        $this->assertEquals(true, $result);
    }

    /**
     * @param mixed ...$args
     */
    protected function setUpSut(...$args)
    {
        $this->sut = new ClassList(...$args);
    }
}
