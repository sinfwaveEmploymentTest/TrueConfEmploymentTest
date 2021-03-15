<?php

namespace App\Model\Api;

use App\Config;

/**
 * JSON database driver
 * validates authorization, read and writes data
 */
final class JsonDb
{
	private string $dbPath;
	
	private string $authDbName;
	
	private string $acceptedDbName = '';
	
	/**
	 * Class constructor, takes JSON database files path from config
	 */
	final public function __construct()
	{
		$this->dbPath = Config::get('database.json.path');
		$this->authDbName = Config::get('database.json.authDbName');
	}
	
	/**
	 * Authentication in JSON database
	 *
	 * @param string $inputLogin
	 * @param string $inputPass
	 * @param string $inputDbName
	 * @return boolean
	 */
	final public function auth(string $inputLogin, string $inputPass, string $inputDbName): bool
	{
		// take auth database
		$authData = json_decode(file_get_contents($this->dbPath.$this->authDbName), true);
		
		// check auth data
		$authDbName = $authData['database']['data'][$inputLogin]['database'];
		$authPassHash = $authData['database']['data'][$inputLogin]['password'];
		
		if ( password_verify($inputPass, $authPassHash) && $inputDbName == $authDbName )
		{ $this->acceptedDbName = $inputDbName; return true; }
		else return false;
	}
	
	/**
	 * Get database data array if authorized
	 *
	 * @return array
	 */
	final public function getData(): array
	{
		// return json database array if db name authorized
		if ( $this->acceptedDbName !== '' )
		return json_decode( file_get_contents($this->dbPath.$this->acceptedDbName.'.json'), true);
		else return ['error' => 'Access denied. Use auth() first.'];
	}
	
	/**
	 * Set new database data
	 *
	 * @param array $data
	 * @return void
	 */
	final public function setData(array $data)
	{
		// return json database array if db name authorized
		if ( $this->acceptedDbName !== '' )
		file_put_contents($this->dbPath.$this->acceptedDbName.'.json', json_encode($data, JSON_PRETTY_PRINT) );
	}
}