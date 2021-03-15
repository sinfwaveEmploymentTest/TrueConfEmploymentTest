<?php

namespace App\Controller\Api\Users;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controller\Msg\Error;
use App\Model\Api\JsonDb;
use App\Config;

/**
 * Users JSON Api invokable class
 */
class Entry
{
	// API implemented and future methods array list from config
	protected array $methods;
	
	// API accepted user fields from config
	protected array $fields;
	
	// response arguments code, sender, header and type
	protected array $responseArg;
	
	// JSON database instance
	protected JsonDb $jsonDb;
	
	// JSON database data
	protected array $dbData;
	
	// User API request body
	protected ?array $req;
	
	/**
	 * Main API logic flow
	 * validates incoming data for API implementation standard
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param [type] $args
	 * @return Response
	 */
	final public function __invoke(Request $request, Response $response, $args): Response
	{
		// set config data
		$this->methods = Config::get('api.json.Users.methods');
		$this->fields = Config::get('api.json.Users.fields');
		$this->responseArg = Config::get('api.json.Users.responseArg');
		
		// construct core class
		$this->jsonDb = new JsonDb();
		
		// get login pass from request
		$serverParams = $request->getServerParams();
		$login = $serverParams['PHP_AUTH_USER'];
		$pass = $serverParams['PHP_AUTH_PW'];
		
		// authorize api user into database, send error response if not valid auth data
		$auth = $this->jsonDb->auth($login, $pass, 'Users');
		if ( ! $auth ) return Error::Json(401, $this->responseArg['sender'], 'Login or password is incorrect');
		
		// get request body, error response if not valid
		$this->req = json_decode( $request->getBody(), true );
		$error = json_last_error();
		if ( $error > 0 || ! $this->req )
		return Error::Json(400, $this->responseArg['sender'], "JSON request not valid. Error - {$error}");
		
		// check core field method
		if ( ! $this->req['method'] )
		return Error::Json(400, $this->responseArg['sender'], 'Required core field *method* in JSON request not found');
		
		// check core field data
		if ( ! $this->req['data'] )
		return Error::Json(400, $this->responseArg['sender'], 'Required core field "data" in JSON request not found');
		
		// check api method if method not valid, send error response
		if ( $this->methods[ $this->req['method'] ] === null || $this->methods[ $this->req['method'] ] === false )
		return Error::Json(400, $this->responseArg['sender'], 'JSON request method not found or not supported yet');
		
		// get database data
		$this->dbData = $this->jsonDb->getData();
		
		// run api method and return response via abstract class else return json error
		$class = __NAMESPACE__.'\Methods\\'.$this->req['method'];
		$methodResponse = $class::RunMethod($this);
		
		if (gettype($methodResponse) === 'object') return $methodResponse;
		else
		$response->getBody()->write( $methodResponse );
		return $response->withStatus($this->responseArg['code'])
		->withHeader($this->responseArg['header'], $this->responseArg['type']);
	}
	
	/**
	 * Positive by API logic response function
	 *
	 * @param array $data
	 * @param string $message
	 * @return string
	 */
	protected function ApiJsonResponse(array $data, string $message): string
	{
		return json_encode([
			'code' => $this->responseArg['code'],
			'sender' => $this->responseArg['sender'],
			'message' => $message,
			'data' => $data
		]);
	}
}