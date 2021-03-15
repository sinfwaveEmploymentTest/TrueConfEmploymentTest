<?php

namespace App\Controller\Api\Users\Methods;

use App\Controller\Api\Users\Methods;
use App\Controller\Api\Users\Entry;
use App\Controller\Msg\Error;

/**
 * Implementation of Users API (users.api) method DeleteUserById
 */
final class DeleteUserById extends Methods
{
	final protected static function run(Entry $entry)
	{
		// get user id from request
		$id = $entry->req['data'];
		
		// validate request user id field
		if ($id === null || gettype($id) !== 'integer')
		return Error::Json(400, $entry->responseArg['sender'],
		'ID field don\'t exist or must be integer type to delete user data');
		
		// validate user id existence
		$data = [];
		if ($entry->dbData['database']['data']['User'][$id] === null)
		return $entry->ApiJsonResponse($data, "ID {$id} user not found");
		
		// create response data
		$data = ['id' => $id] + $entry->dbData['database']['data']['User'][$id];
		
		// delete user
		unset($entry->dbData['database']['data']['User'][$id]);
		
		// write modified data
		$entry->jsonDb->setData($entry->dbData);
		
		// return api response
		return $entry->ApiJsonResponse($data, 'User deleted');
	}
}