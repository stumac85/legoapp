<?php
namespace App\RebrickableAPI;

class RebrickableAPIResponse{
	 public $code;
	 public $hasError;
	 public $errorMessage;
	 public $results;
     public $rawRequest;
	 public $count = 0;
	 public $next;
	 public $previous;
	 public $pages;
	 private $rawResponse;
	 public $seshData;

	 public function __construct($ch, $response){
		 	$this->rawResponse = $response;
			//die($ch);
			 if(!is_string($ch)){
				 $this->rawRequest = curl_getinfo($ch)['url'];
				 $this->code = curl_getinfo($ch)['http_code'];
			 } else {
				$this->rawRequest = $ch;
				$this->code = 200;
			 }
             
			$responseObject = json_decode($response);
			
			if($this->hasError = (substr($this->code,0,1) !== '2')){
				if($responseObject)
					$this->errorMessage = $responseObject->detail;
				else
					$this->errorMessage = 'A search error occurred, please check the search URL';

				$this->hasError = true;
			}else{
                if(isset($responseObject->count)){
                    $this->count = $responseObject->count;
				} else {
					$this->count = 1;
				}
				if(isset($responseObject->next)){
					$nextMinusKey = $responseObject->next;
					$nextMinusKey = str_replace(env('REBRICKABLE_APP_ID'),'',$nextMinusKey);
                    $this->next = $responseObject->next;
				}
				if(isset($responseObject->previous)){
					$prevMinusKey = $responseObject->previous;
					$prevMinusKey = str_replace(env('REBRICKABLE_APP_ID'),'',$prevMinusKey);
                    $this->previous = $responseObject->previous;
				}
				if(isset($responseObject->results))
					$this->results = $responseObject->results;
				else
					$this->results = $responseObject;
			}
	 }

	 public function asJson(){
		 return $rawResponse;
	 }

	 public function setSessionData($sData){
		$this->seshData = $sData;
	 }
}
