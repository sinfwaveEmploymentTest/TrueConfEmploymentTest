<?php

namespace App\Controller\Api\Users\Methods;

use App\Controller\Api\Users\Methods;
use App\Controller\Api\Users\Entry;
use App\Controller\Msg\Error;

/**
 * Implementation of Users API (users.api) method EditUserById
 */
final class EditUserById extends Methods
{
	final protected static function run(Entry $entry)
	{
		// get user id from request
		$id = $entry->req['data']['id'];
		
		// validate request user id field
		if ($id === null || gettype($id) !== 'integer')
		return Error::Json(400, $entry->responseArg['sender'],
		'ID field don\'t exist or must be integer type in user data to update');
		
		// validate user id existence
		$data = [];
		if ($entry->dbData['database']['data']['User'][$id] === null)
		return $entry->ApiJsonResponse($data, "ID {$id} user not found");
		
		// validate and update user fields
		$data['old'] = [];
		$data['new'] = [];
		foreach ($entry->req['data'] as $key => $value) {
			if ( $key == 'id' || ($entry->fields[$key] !== null && $value !== false) ) {
				
				if ( $key !== 'id' ) {
					
					$data['old'] = $data['old'] + [ "{$key}" => $entry->dbData['database']['data']['User'][$id][$key] ];
					$data['new'] = $data['new'] + [ "{$key}" => $entry->req['data'][$key] ];
					$entry->dbData['database']['data']['User'][$id][$key] = $entry->req['data'][$key];
				}
			}
			else return Error::Json(400, $entry->responseArg['sender'], "User field *{$key}* can't be updated because don't allowed in data schema");
		}
		
		// write modified data
		$entry->jsonDb->setData($entry->dbData);
		
		// return api response
		return $entry->ApiJsonResponse($data, "ID {$id} user modified");
	}
}