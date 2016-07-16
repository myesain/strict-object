<?php
/**
 * Myesain\Strict\StrictTrait PHPUnit Test Case File
 *
 * @copyright 	2016 myesain
 * @since 		2016-07-16
 */

namespace Myesain\Strict\Test;

use Myesain\Strict\StrictTrait;

class MyObject
{
	protected $properties = array(
		'id',
		'name',
		'title',
	);

	use \Myesain\Strict\StrictTrait;
}

class StrictTraitTest extends \PHPUnit_Framework_TestCase
{
	public function testUsingTraitAllowsSettingPropertiesCorrectly()
	{
		$object = new MyObject();

		$object->id    = 12;
		$object->name  = "myesain";
		$object->title = "Overlord";

		$this->assertEquals(12, $object->id);
		$this->assertEquals("myesain", $object->name);
		$this->assertEquals("Overlord", $object->title);
	}

	public function testUsingTraitAttemptingToSetInvalidPropertyThrowsException()
	{
		$object = new MyObject();

		$this->setExpectedException("Myesain\Strict\Exception\NonExistentPropertyException");

		$object->nonExistentProperty = "some value";
	}

	public function testUsingTraitHydratingWithValidPropertiesHydratesProperly()
	{
		$data = array(
			'id'    => 12,
			'name'  => 'myesain',
			'title' => 'Overlord',
		);

		$object = new MyObject();

		$object->hydrate($data);

		$this->assertEquals(12, $object->id);
		$this->assertEquals("myesain", $object->name);
		$this->assertEquals("Overlord", $object->title);
	}

	public function testUsingTraitHydratingWithInvalidPropertyIsProperlyHydratedAndDoes_NOT_ThrowException()
	{
		$data = array(
			'id'                  => 12,
			'name'                => 'myesain',
			'title'               => 'Overlord',
			'nonExistentProperty' => 'some value'
		);

		$object = new MyObject();

		$object->hydrate($data);

		$this->assertEquals(12, $object->id);
		$this->assertEquals("myesain", $object->name);
		$this->assertEquals("Overlord", $object->title);

	}
}
