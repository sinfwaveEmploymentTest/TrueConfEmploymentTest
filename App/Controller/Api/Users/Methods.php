<?php

namespace App\Controller\Api\Users;

abstract class Methods extends Entry
{
	protected static function RunMethod(Entry $entry)
	{
		return static::run($entry);
	}
	
	// core method to implements in API method classes
	abstract protected static function run(Entry $entry);
}