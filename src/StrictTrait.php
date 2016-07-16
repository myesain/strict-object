<?php
/**
 * Myesain\Strict\StrictTrait file
 *
 *
 *
 * @copyright 	2016 myesain
 * @since 		2016-07-16
 */

namespace Myesain\Strict;

use Myesain\Strict\Exception\NonExistentPropertyException;

trait StrictTrait
{
	/**
	 * @var array - defines public properties that should be exposed
	 */
	// protected $properties = array();

	/**
	 * @var array - contains current values for properties
	 */
	protected $values = array();


	/**
	 * @param array $data
	 * @return null
	 */
	public function hydrate(array $data)
	{
		if (!$data) {
			return;
		}

		foreach ($this->properties as $prop) {

			$value = isset($data[$prop]) ? $data[$prop] : "";

			// if ($value && in_array($prop, $this->dateProperties) && !($value instanceof \DateTime)) {
			// 	$value = new \DateTime($value);
			// }

			$this->values[$prop] = ($value) ? $value : "";
		}
	}

	public function __set($name, $value)
	{
		if (!in_array($name, $this->properties)) {
			throw new NonExistentPropertyException("Property {$name} not a valid property");
		}

		// if (in_array($name, $this->dateProperties) && !($value instanceof \DateTime)) {
		// 	$value = new \DateTime($value);
		// }

		$this->values[$name] = $value;
	}

	public function &__get($name)
	{
		return $this->values[$name];
	}

	public function __isset($name)
	{
		return isset($this->values[$name]);
	}

	public function __unset($name)
	{
		unset($this->values[$name]);
	}

	public function jsonSerialize()
	{
		$content = new \StdClass;

		foreach ($this->properties as $property) {
			$content->$property = $this->values[$property];
		}

		return $content;
	}
}
