<?php

namespace App\Controller\Msg;

use Slim\Psr7\Response;
use App\Config;

trait Error
{
	/**
	 * JSON message function
	 *
	 * @param string $sender
	 * @param integer $code
	 * @param string $msg
	 * @return Response
	 * @example this Error::Json(200, 'MsgSender', 'Message body');
	 */
	final public static function Json(int $code, string $sender, string $msg): Response
	{
		// msg header
		$header = Config::get('headers.json.header');
	
		// msg type
		$type = Config::get('headers.json.type');
		
		// format message
		$info = json_encode(['code' => $code, 'sender' => $sender, 'message' => $msg], JSON_UNESCAPED_SLASHES);
		
		// send final response
		$response = new Response();
		$response->getBody()->write($info);
		return $response->withStatus($code)->withHeader($header, $type);
	}
}