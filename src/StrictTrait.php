<?php
/**
 * Myesain\Strict\StrictTrait file
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 	2016 myesain
 * @since 		2016-07-16
 */

namespace Myesain\Strict;

use Myesain\Strict\Exception\NonExistentPropertyException;

trait StrictTrait
{
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

			if (!isset($data[$prop])) {
				continue;
			}

			$value = isset($data[$prop]) ? $data[$prop] : "";

			$this->values[$prop] = ($value) ? $value : "";
		}
	}

	public function __set($name, $value)
	{
		if (!in_array($name, $this->properties)) {
			throw new NonExistentPropertyException("Property {$name} not a valid property of " . get_class($this));
		}

		$this->values[$name] = $value;
	}

	public function &__get($name)
	{
		if (!in_array($name, $this->properties)) {
			throw new NonExistentPropertyException("Property {$name} not a valid property of " . get_class($this));
		}
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
}
