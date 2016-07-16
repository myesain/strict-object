<?php
/**
 * Myesain\Strict\StrictObjectTest PHPUnit Test Case File
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 	2016 myesain
 * @since 		2016-07-16
 */

namespace Myesain\Strict\Test;

use Myesain\Strict\StrictObject;

class MyNewObject extends StrictObject
{
	protected $properties = array(
		'id',
		'name',
		'title',
	);
}

class StrictObjectTest extends \PHPUnit_Framework_TestCase
{
	public function testProvidingDataToConstructorProperlyHydratesObject()
	{
		$data = array(
			'id'    => 12,
			'name'  => 'myesain',
			'title' => 'Overlord',
		);

		$object = new MyNewObject($data);

		$this->assertEquals(12, $object->id);
		$this->assertEquals("myesain", $object->name);
		$this->assertEquals("Overlord", $object->title);
	}

	public function testSerializingToJsonRetainsAllPropertiesCorrectly()
	{
		$data = array(
			'id'    => 12,
			'name'  => 'myesain',
			'title' => 'Overlord',
		);

		$object = new MyNewObject();

		$object->hydrate($data);

		$object2 = json_decode(json_encode($object));

		$this->assertEquals(12, $object2->id);
		$this->assertEquals("myesain", $object2->name);
		$this->assertEquals("Overlord", $object2->title);
	}
}
