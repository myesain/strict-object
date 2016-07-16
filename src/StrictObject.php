<?php
/**
 * Myesain\Strict\StrictObject file
 *
 * @copyright 	2016 myesain
 * @since 		2016-07-16
 */

namespace Myesain\Strict;

abstract class StrictObject implements \JsonSerializable
{
	use StrictTrait;

	public function __construct(array $data = array())
	{
		$this->hydrate($data);
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
