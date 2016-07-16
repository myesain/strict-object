<?php
/**
 * Myesain\Strict\StrictArrayObject file
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 	2016 myesain
 * @since 		2016-07-16
 */

namespace Myesain\Strict;

use Myesain\Strict\Exception\NonExistentPropertyException;

abstract class StrictArrayObject extends StrictObject implements \ArrayAccess, \Countable, \IteratorAggregate
{
	/**
	 * @inheritDoc
	 */
	public function offsetExists($offset)
	{
		// return in_array($offset, $this->properties);
		return array_key_exists($offset, $this->values);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet($offset)
	{
		if (!in_array($offset, $this->properties)) {
			throw new NonExistentPropertyException("Property {$offset} not a valid property of " . get_class($this));
		}
		return $this->values[$offset];
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet($offset, $value)
	{
		if (!in_array($offset, $this->properties)) {
			throw new NonExistentPropertyException("Property {$offset} not a valid property of " . get_class($this));
		}
		$this->values[$offset] = $value;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset($offset)
	{
		unset($this->values[$offset]);
	}

	/**
	 * @inheritDoc
	 */
	public function count()
	{
		return count($this->values);
	}

	/**
	 * @inheritDoc
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->values);
	}
}
