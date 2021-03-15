<?php

namespace App\Middleware\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Controller\Msg\Error;
use Slim\Psr7\Response;
use App\Config;

final class JsonHttps
{	
	/**
	 * Api middleware invokable class
	 * Check secure HTTPS and SSL connection and Http Basic Auth existence
	 *
	 * @param  ServerRequest  $request PSR-7 request
	 * @param  RequestHandler $handler PSR-15 request handler
	 *
	 * @return Response
	 */
	final public function __invoke(Request $request, RequestHandler $handler): Response
	{
		// set config variables
		$header = Config::get('headers.json.header');
		$contentType = Config::get('headers.json.type');
		$sender = 'JsonHttpsMiddleware';
		
		// get request server parameters array to validate
		$serverParams = $request->getServerParams();
		
		// get request content type headers
		$contentTypeHeaders = $request->getHeader($header);
		
		// check ssl and port, if not valid - send response error header and message
		if (
			$serverParams['REQUEST_SCHEME'] !== 'https' &&
			$serverParams['SERVER_PORT'] !== '443'
		) return Error::Json(400, $sender, 'Not HTTPS request');
		
		// check content type header, if not valid or more than 1 - send response error header and message
		if (
			$contentTypeHeaders[0] !== $contentType ||
			count($contentTypeHeaders) > 1
		) return Error::Json(400, $sender, "Request require only one header of {$header}: {$contentType}");
		
		// check username and password existents, if don't exist - send response error header and message
		if (
			$serverParams['PHP_AUTH_USER'] == null ||
			$serverParams['PHP_AUTH_PW'] == null
		) return Error::Json(401, $sender,'Basic authorization username or password not set');
		
		// handle request and return response
		return $handler->handle($request);
	}
}