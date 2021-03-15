<?php

namespace App\Controller\Api\Users\Methods;

use App\Controller\Api\Users\Methods;
use App\Controller\Api\Users\Entry;
use App\Controller\Msg\Error;

/**
 * Implementation of Users API (users.api) method GetUsersList
 */
final class GetUsersList extends Methods
{
	final protected static function run(Entry $entry)
	{
		// validate data
		if ( $entry->req['data'] !== 'All')
		return Error::Json(400, $entry->responseArg['sender'], 'User list getting parameter not found in data field, must be - All');
		
		// create returned data array
		$data = [];
		
		// create data array
		foreach ( $entry->dbData['database']['data']['User'] as $key => $value )
		{ $id = ['id' => $key]; $data[] = $id + $value; }
		
		// return api response
		return $entry->ApiJsonResponse($data, "*{$entry->req['data']}* directive list ready");
	}
}