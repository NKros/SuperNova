<?php

/**
 * Class PropertyHiderTested
 *
 * @property int    test
 * @property int    testGetSet
 * @property array  testWithDiff
 *
 * @property int    testInteger
 * @property float  testFloat
 * @property string testString
 * @property array  testArray
 *
 * @property null   testNull
 */
class PropertyHiderTested extends PropertyHider {
  protected $_test = -2;
  protected $_testGetSet = -1;
  protected $_testWithDiff = array();

  protected $_testInteger = 0;
  protected $_testFloat = 0.0;
  protected $_testString = '';
  protected $_testArray = array();

  protected $_testNull = null;

  protected function getTestGetSet() {
    return $this->_testGetSet + 1;
  }

  protected function setTestGetSet($value) {
    $this->_testGetSet = $value + 2;
  }

  /**
   * @param int $value
   *
   * @return int
   */
  protected function adjTestGetSet($value) {
    return $this->_testGetSet + $value + 4;
  }


  /**
   * @return array
   */
  protected function gettestWithDiff() {
    return $this->_testWithDiff;
  }

  /**
   * @param array $value
   */
  protected function settestWithDiff($value) {
    $this->_testWithDiff = $value;
  }

  /**
   * @param array $value
   *
   * @return array
   */
  protected function adjtestWithDiff($diff) {
//    if(!isset($this->propertiesAdjusted['testWithDiff']) || !is_array($this->propertiesAdjusted['testWithDiff'])) {
//      $this->propertiesAdjusted['testWithDiff'] = array();
//    }

    HelperArray::merge($this->_testWithDiff, $diff, HelperArray::MERGE_PHP);

    return $this->_testWithDiff;
  }

  /**
   * @param array $value
   */
  protected function adjtestWithDiffDiff($diff) {
    if (!is_array($this->propertiesAdjusted['testWithDiff'])) {
      $this->propertiesAdjusted['testWithDiff'] = array();
    }

    HelperArray::merge($this->propertiesAdjusted['testWithDiff'], $diff, HelperArray::MERGE_PHP);

    return $this->propertiesAdjusted['testWithDiff'];
  }

}

/**
 * Class PropertyHiderTest
 *
 * @coversDefaultClass PropertyHider
 */
class PropertyHiderTest extends PHPUnit_Framework_TestCase {
  /**
   * @var PropertyHiderTested $object
   */
  protected $object;
  protected $testProperties = array(
    'test'                    => array(),
    'testGetSet'              => array(),
    'testWithDiff'            => array(),
    'noClassPropertyOrMethod' => array(),
    'testInteger'             => array(),
    'testFloat'               => array(),
    'testString'              => array(),
    'testArray'               => array(),
    'testNull'                => array(),
  );

  public function setUp() {
    parent::setUp();
    $this->object = new PropertyHiderTested();
    PropertyHiderTested::setProperties($this->testProperties);
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::getPhysicalPropertyName
   */
  public function testGetPhysicalPropertyName() {
    $this->assertEquals('_test', invokeMethod($this->object, 'getPhysicalPropertyName', array('test')));
  }

  /**
   * @covers ::isPropertyDeclared
   */
  public function testIsPropertyDeclared() {
    $this->assertTrue(invokeMethod($this->object, 'isPropertyDeclared', array('test')));
    $this->assertFalse(invokeMethod($this->object, 'isPropertyDeclared', array('nonDeclaredClassProperty')));
  }

  /**
   * @covers ::__construct
   * @covers ::setProperties
   * @covers ::getProperties
   */
  public function testSetGetProperties() {
    $this->assertEquals('PropertyHiderTested', get_class($test = new PropertyHiderTested()));

    $this->assertEquals(
      $this->testProperties,
      PropertyHiderTested::getProperties()
    );

    PropertyHiderTested::setProperties(array('asd' => 'qwe'));
    $this->assertEquals(array('asd' => 'qwe'), PropertyHiderTested::getProperties());
  }

  /**
   * @covers ::checkPropertyExists
   */
  public function testCheckPropertyExists() {
    invokeMethod($this->object, 'checkPropertyExists', array('test'));
  }

  /**
   * @covers ::checkPropertyExists
   * @expectedException ExceptionPropertyNotExists
   */
  public function testCheckPropertyExistsException() {
    invokeMethod($this->object, 'checkPropertyExists', array('nonExistsInClassProperty'));
  }


  public function dataSetGetException() {
    return array(
      array('notInPropertyArray'),
      array('noClassPropertyOrMethod'),
    );
  }

  /**
   * @covers ::__get
   */
  public function test__get() {
    // Simple get
    $this->assertEquals(-2, $this->object->test);

    // Getter = +1 to real value
    $this->assertEquals(0, $this->object->testGetSet);
  }

  /**
   * @param mixed $badPropertyName
   *
   * @dataProvider dataSetGetException
   *
   * @covers ::__get
   * @expectedException ExceptionPropertyNotExists
   */
  public function test__getExceptionPropertyNotExists($badPropertyName) {
    $test = $this->object->$badPropertyName;
  }

  /**
   * @covers ::_setUnsafe
   */
  public function test_setUnsafe() {
    $this->assertEquals(-2, $this->object->test);
    // Simple set
    invokeMethod($this->object, '_setUnsafe', array('test', 5));
    $this->assertEquals(5, $this->object->test);
    $this->assertEquals(array('test' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));

    // Test with calling getters/setters
    // Getter = +1 to real value
    $this->assertEquals(0, $this->object->testGetSet);
    // Setter = +2 to setting value
    invokeMethod($this->object, '_setUnsafe', array('testGetSet', 0));
    // Initial + Getter + Setter = 0 + 1 + 2
    $this->assertEquals(3, $this->object->testGetSet);
    // Checking that all changed properties still marked
    $this->assertEquals(array('test' => true, 'testGetSet' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
  }

  /**
   * @param mixed $badPropertyName
   *
   * @dataProvider dataSetGetException
   *
   * @covers ::_setUnsafe
   * @expectedException ExceptionPropertyNotExists
   */
  public function test_setUnsafeExceptionPropertyNotExists($badPropertyName) {
    invokeMethod($this->object, '_setUnsafe', array($badPropertyName, 0));
  }

  /**
   * @covers ::__set
   */
  public function test__set() {
    $this->assertEquals(-2, $this->object->test);
    // Simple set
    $this->object->test = 5;
    $this->assertEquals(5, $this->object->test);
    $this->assertEquals(array('test' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));

    // Test with calling getters/setters
    // Getter = +1 to real value
    $this->assertEquals(0, $this->object->testGetSet);
    // Setter = +2 to setting value
    $this->object->testGetSet = 0;
    // Initial + Getter + Setter = 0 + 1 + 2
    $this->assertEquals(3, $this->object->testGetSet);
    // Checking that all changed properties still marked
    $this->assertEquals(array('test' => true, 'testGetSet' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
  }


  public function dataAdjustValueType() {
    return array(
      array('_adjustValue', 'testInteger', 0, 3.5, 3, 'integer'),
      array('_adjustValueDiff', 'testInteger', 0, 3.5, 3, 'integer'),

      array('_adjustValueInteger', 'testInteger', 0, 3.5, 3, 'integer'),
      array('_adjustValueIntegerDiff', 'testInteger', 0, 3.5, 3, 'integer'),
      array('_adjustValueDouble', 'testFloat', 0.0, 5.7, 5.7, 'double'),
      array('_adjustValueDoubleDiff', 'testFloat', 0.0, 5.7, 5.7, 'double'),
      array('_adjustValueString', 'testString', '', 'foo', 'foo', 'string'),
      array('_adjustValueStringDiff', 'testString', '', 'foo', 'foo', 'string'),
      array('_adjustValueArray', 'testArray', array(), array('a' => 'b'), array('a' => 'b'), 'array'),
      array('_adjustValueArrayDiff', 'testArray', array(), array('a' => 'b'), array('a' => 'b'), 'array'),
    );
  }

  /**
   * @dataProvider dataAdjustValueType
   *
   * @covers ::_adjustValue
   * @covers ::_adjustValueInteger
   * @covers ::_adjustValueDouble
   * @covers ::_adjustValueString
   * @covers ::_adjustValueArray
   * @covers ::_adjustValueDiff
   * @covers ::_adjustValueIntegerDiff
   * @covers ::_adjustValueDoubleDiff
   * @covers ::_adjustValueStringDiff
   * @covers ::_adjustValueArrayDiff
   *
   * @param $methodName
   * @param $fieldName
   * @param $start
   * @param $diff
   * @param $expected
   * @param $type
   */
  public function test_adjustValueTypeAndDiff($methodName, $fieldName, $start, $diff, $expected, $type) {
    $this->assertEquals($start, $this->object->$fieldName);
    $result = invokeMethod($this->object, $methodName, array($fieldName, $diff));
    $this->assertEquals($expected, $result);
    $this->assertInternalType($type, $result);
  }


//  protected function propertyMethodResult($name, $diff, $suffix = ''){}


  public function dataPropertyMethodResult() {
    return array(
      array('testInteger', 3.5, 3, 'integer', ''),
      array('testFloat', 5.7, 5.7, 'double', ''),
      array('testString', 'foo', 'foo', 'string', ''),
      array('testArray', array('a' => 'b'), array('a' => 'b'), 'array', ''),
      array('testInteger', 3.5, 3, 'integer', 'Diff'),
      array('testFloat', 5.7, 5.7, 'double', 'Diff'),
      array('testString', 'foo', 'foo', 'string', 'Diff'),
      array('testArray', array('a' => 'b'), array('a' => 'b'), 'array', 'Diff'),
    );
  }

  /**
   * @dataProvider dataPropertyMethodResult
   * @covers ::propertyMethodResult
   */
  public function testPropertyMethodResult($varName, $value, $expected, $type, $suffix) {
    $result = invokeMethod($this->object, 'propertyMethodResult', array($varName, $value, $suffix));
    $this->assertEquals($expected, $result);
    $this->assertInternalType($type, $result);
  }

  /**
   * Test exception when trying to adjust unsupported property type
   *
   * @covers ::propertyMethodResult
   * @expectedException ExceptionTypeUnsupported
   */
  public function testExceptionTypeUnsupported() {
    invokeMethod($this->object, 'propertyMethodResult', array('testNull', null, ''));
  }

//  protected function _adjustValue($name, $diff) {
//    return $this->propertyMethodResult($name, $diff);
//  }
//
//$this->propertiesAdjusted[$name] = $this->_adjustValueDiff($name, $diff);


  /**
   * Testing adjusters
   *
   * @covers ::__adjust
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjust() {
    // Simple adjust
    $this->assertEquals(-2, $this->object->test);
    $this->assertEquals(8, $this->object->__adjust('test', 10));
    $this->assertEquals(array('test' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('test' => 10), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));

    // Test with calling getters/setters
    // Getter = +1 to real value
    $this->assertEquals(0, $this->object->testGetSet);
    // $Diff (8) + Adjuster (+4) + Setter(+2) + Getter (+1) + real value (-1) = 14
    $this->assertEquals(14, $this->object->__adjust('testGetSet', 8));
    $this->assertEquals(array('test' => true, 'testGetSet' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('test' => 10, 'testGetSet' => 8), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }

  /**
   * Test exception when trying to set in already adjusted property
   *
   * @covers ::checkOverwriteAdjusted
   * @expectedException PropertyAccessException
   */
  public function testPropertyAccessException() {
    $this->assertEquals(-2, $this->object->test);
    $this->assertEquals(8, $this->object->__adjust('test', 10));
    $this->object->test = 20;
  }


  /**
   * @covers ::__adjust
   * @covers ::checkOverwriteAdjusted
   */
  public function test__adjustWithDiffCallback() {
    // Test with calling getters/setters and DIFF adjuster on array
    $this->assertEquals(array(), $this->object->testWithDiff);
    // Testing setter
    $this->object->__set('testWithDiff', array('a' => 'p'));
    $this->assertEquals(array('a' => 'p'), $this->object->testWithDiff);
    $this->assertEquals(array('testWithDiff' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    // Testing adjuster
    $this->assertEquals(array('a' => 'p', 'b' => 'q'), $this->object->__adjust('testWithDiff', array('b' => 'q')));
    $this->assertEquals(array('a' => 'p', 'b' => 'q'), $this->object->testWithDiff);
    $this->assertEquals(array('testWithDiff' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testWithDiff' => array('b' => 'q')), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
    // Testing adjuster incremental
    $this->assertEquals(array('a' => 'p', 'b' => 'q', 'с' => 'w'), $this->object->__adjust('testWithDiff', array('с' => 'w')));
    $this->assertEquals(array('a' => 'p', 'b' => 'q', 'с' => 'w'), $this->object->testWithDiff);
    $this->assertEquals(array('testWithDiff' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
    $this->assertEquals(array('testWithDiff' => array('b' => 'q', 'с' => 'w')), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
  }


//  /**
//   * Testing built-in type adjusters
//   *
//   * @covers ::__set
//   * @covers ::_setUnsafe
//   * @covers ::__get
//   * @covers ::__adjust
//   * @covers ::_adjustValue
//   * @covers ::_adjustValueDiff
//   * @covers ::_adjustValueDouble
//   * @covers ::_adjustValueDoubleDiff
//   * @covers ::propertyMethodResult
//   * @covers ::checkOverwriteAdjusted
//   */
//  public function test__adjustValueFloat() {
//    // Test with calling getters/setters and DIFF adjuster on array
//    $this->assertEquals(0.0, $this->object->testFloat);
//    $this->assertEquals(3.1, $this->object->__adjust('testFloat', 3.1));
//    $this->assertEquals(array('testFloat' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
//    $this->assertEquals(array('testFloat' => 3.1), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
//  }
//
//  /**
//   * Testing built-in type adjusters
//   *
//   * @covers ::__set
//   * @covers ::_setUnsafe
//   * @covers ::__get
//   * @covers ::__adjust
//   * @covers ::_adjustValue
//   * @covers ::_adjustValueDiff
//   * @covers ::_adjustValueString
//   * @covers ::_adjustValueStringDiff
//   * @covers ::propertyMethodResult
//   * @covers ::checkOverwriteAdjusted
//   */
//  public function test__adjustValueString() {
//    // Test with calling getters/setters and DIFF adjuster on array
//    $this->assertEquals('', $this->object->testString);
//    $this->assertEquals('foo', $this->object->__adjust('testString', 'foo'));
//    $this->assertEquals(array('testString' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
//    $this->assertEquals(array('testString' => 'foo'), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
//
//    $this->assertEquals('foobar', $this->object->__adjust('testString', 'bar'));
//    $this->assertEquals(array('testString' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
//    $this->assertEquals(array('testString' => 'foobar'), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
//  }
//
//  /**
//   * Testing built-in type adjusters
//   *
//   * @covers ::__set
//   * @covers ::_setUnsafe
//   * @covers ::__get
//   * @covers ::__adjust
//   * @covers ::_adjustValue
//   * @covers ::_adjustValueDiff
//   * @covers ::_adjustValueArray
//   * @covers ::_adjustValueArrayDiff
//   * @covers ::propertyMethodResult
//   * @covers ::checkOverwriteAdjusted
//   */
//  public function test__adjustValueArray() {
//    // Test with calling getters/setters and DIFF adjuster on array
//
//    $this->assertEquals(array(), $this->object->testArray);
//    // Testing setter
//    $this->object->__set('testArray', array('a' => 'p'));
//    $this->assertEquals(array('a' => 'p'), $this->object->testArray);
//    $this->assertEquals(array('testArray' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
//    // Testing adjuster
//    $this->assertEquals(array('a' => 'p', 'b' => 'q'), $this->object->__adjust('testArray', array('b' => 'q')));
//    $this->assertEquals(array('a' => 'p', 'b' => 'q'), $this->object->testArray);
//    $this->assertEquals(array('testArray' => true), getPrivateProperty('PropertyHiderTested', 'propertiesChanged')->getValue($this->object));
//    $this->assertEquals(array('testArray' => array('b' => 'q')), getPrivateProperty('PropertyHiderTested', 'propertiesAdjusted')->getValue($this->object));
//  }

}