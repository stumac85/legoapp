<?php
namespace App\PHPBricklinkApi;

class BricklinkApiRequest{
	public $method = "GET";
	public $path = "";
	public $params = "";
	public $authorization = null;

	public function __construct(array $params){
		foreach($params as $key=>$value){
			$this->{$key} = $value;
		}
	}
}
