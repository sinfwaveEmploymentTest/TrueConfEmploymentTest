<?php

namespace App\Controller\Api\Users\Methods;

use App\Controller\Api\Users\Methods;
use App\Controller\Api\Users\Entry;
use App\Controller\Msg\Error;

/**
 * Implementation of Users API (users.api) method GetUserById
 */
final class GetUserById extends Methods
{
	final protected static function run(Entry $entry)
	{
		// validate data
		if ( gettype($entry->req['data']) !== 'integer')
		return Error::Json(400, $entry->responseArg['sender'], 'ID must be integer type in "data" field');
		
		// get user from database
		$user = $entry->dbData['database']['data']['User'][$entry->req['data']];
		
		// create returned data array
		$data = [ 'id' => null ];
		
		// check if user not found, send response
		if ( $user === null )
		return $entry->ApiJsonResponse($data, "User not found");
		
		// if user found check final response
		$id = ['id' => $entry->req['data'] ];
		$data = $id + $user;
		return $entry->ApiJsonResponse($data, "ID {$entry->req['data']} user found");
	}
}