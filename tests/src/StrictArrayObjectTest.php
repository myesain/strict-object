<?php
/**
 * Myesain\Strict\StrictObjectTest PHPUnit Test Case File
 *
 * @copyright 	2016 myesain
 * @since 		2016-07-16
 */

namespace Myesain\Strict\Test;

use Myesain\Strict\StrictArrayObject;

class MyArrayObject extends StrictArrayObject
{
	protected $properties = array(
		'id',
		'name',
		'title',
	);
}

class StrictArrayObjectTest extends \PHPUnit_Framework_TestCase
{
	/* ArrayAccess Interface */
	public function testAccessingObjectDataWithArrayKeysIsSuccessful()
	{
		$object = new MyArrayObject();

		$object['id'] = 12;
		$object['name'] = "myesain";
		$object['title'] = "Overlord";

		$this->assertEquals(12, $object['id']);
		$this->assertEquals("myesain", $object['name']);
		$this->assertEquals("Overlord", $object['title']);

		$this->assertEquals(12, $object->id);
		$this->assertEquals("myesain", $object->name);
		$this->assertEquals("Overlord", $object->title);
	}

	public function testAccessingObjectDataAfterHydrationWithArrayKeysIsSuccessful()
	{
		$object = new MyArrayObject();

		$data = array(
			'id'    => 12,
			'name'  => 'myesain',
			'title' => 'Overlord',
		);

		$object->hydrate($data);

		$this->assertEquals(12, $object['id']);
		$this->assertEquals("myesain", $object['name']);
		$this->assertEquals("Overlord", $object['title']);
	}

	public function testCallingIssetAgainstArrayKeysIsSuccesful()
	{
		$object = new MyArrayObject();

		$this->assertFalse(isset($object['id']));

		$object['id'] = 12;

		$this->assertTrue(isset($object['id']));
	}

	public function testAccessingNonExistentPropertiesWithArrayKeysThrowsException()
	{
		$object = new MyArrayObject();

		$this->setExpectedException("Myesain\Strict\Exception\NonExistentPropertyException");

		$test = $object['nonExistentProperty'];
	}

	public function testAttemptingToSetNonExistentPropertiesWithArrayKeysThrowsException()
	{
		$object = new MyArrayObject();

		$this->setExpectedException("Myesain\Strict\Exception\NonExistentPropertyException");

		$object['nonExistentProperty'] = "some value";
	}

	public function testAttemptingToAddValuesToEndOfArrayObjectUsingBracketSyntaxThrowsException()
	{
		$object = new MyArrayObject();

		$this->setExpectedException("Myesain\Strict\Exception\NonExistentPropertyException");

		$object[] = "some value";
	}

	public function testUnsettingPropertyUsingArraySyntaxSuccessfullyUnsetsProperty()
	{
		$data = array(
			'id'    => 12,
			'name'  => 'myesain',
			'title' => 'Overlord',
		);

		$object = new MyArrayObject($data);

		$this->assertEquals(12, $object['id']);

		unset($object['id']);

		$this->assertFalse(isset($object->id));
	}



	/* Countable Interface */
	public function testCountReturnsNumberOfProperties()
	{
		$object = new MyArrayObject();

		$this->assertEquals(3, count($object));
	}



	/* IteratorAggregate Interface */
	public function testForeachOverArrayObjectReturnsOnlyValuesContainingValues()
	{
		$data = array(
			'id'    => 12,
			'name'  => 'myesain',
			'title' => 'Overlord',
		);

		$object = new MyArrayObject($data);

		$x = 0;

		foreach ($object as $key => $value) {
			$x++;
			$this->assertEquals($data[$key], $value);
		}

		$this->assertEquals(3, $x);



		$data = array(
			'id'    => 12,
			'name'  => 'myesain',
		);

		$object = new MyArrayObject($data);

		$x = 0;

		foreach ($object as $key => $value) {
			$x++;
			$this->assertEquals($data[$key], $value);

			$object[$key] = $value . $value;
		}

		$this->assertEquals(2, $x);

		$this->assertEquals('1212', $object['id']);
		$this->assertEquals('myesainmyesain', $object['name']);
	}
}
