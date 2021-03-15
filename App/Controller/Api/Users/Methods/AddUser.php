<?php

namespace App\Controller\Api\Users\Methods;

use App\Controller\Api\Users\Methods;
use App\Controller\Api\Users\Entry;
use App\Controller\Msg\Error;

/**
 * Implementation of Users API (users.api) method AddUser
 */
final class AddUser extends Methods
{
	final protected static function run(Entry $entry)
	{
		// set 0 key for schema sorting
		$entry->dbData['database']['data']['User'][0] = 0;
		
		// validate for required schema fields
		foreach ($entry->fields as $key => $value)
		if ( $entry->req['data'][$key] === null && $value === true )
		return Error::Json(400, $entry->responseArg['sender'], "Required user field *{$key}* not found");
		
		// validate for non schema non required fields in request data
		foreach ($entry->req['data'] as $key => $value)
		if ( $entry->fields[$key] === null )
		return Error::Json(400, $entry->responseArg['sender'], "User field *{$key}* with value *{$value}* not supported");
		
		// find free id
		$id = 1; while( $entry->dbData['database']['data']['User'][$id] !== null) $id++;
		
		// add validated data
		$entry->dbData['database']['data']['User'][$id] = $entry->req['data'];
		
		// sort array by key
		ksort($entry->dbData['database']['data']['User'], SORT_NUMERIC);
		
		// create response data
		$data = ['id' => $id] + $entry->req['data'];
		
		// unset 0 key for schema sorting
		unset($entry->dbData['database']['data']['User'][0]);
		
		// write modified data
		$entry->jsonDb->setData($entry->dbData);
		
		// return api response
		return $entry->ApiJsonResponse($data, 'New user added');
	}
}