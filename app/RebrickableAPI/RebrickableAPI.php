<?php
namespace App\RebrickableAPI;
include_once(__DIR__.'/RebrickableAPIRequest.php');
include_once(__DIR__.'/RebrickableAPIResponse.php');
Use App\rebrickApiCache;
use Illuminate\Support\Carbon;

class RebrickableAPI{
    private $endpoint = 'https://rebrickable.com/api/v3/lego/';
    private $apiAccess;
    private $request;
	private $isDevelopment = true;
	private $perpage;

    public function __construct(){
        $this->apiAccess = env('REBRICKABLE_APP_ID');
        $this->isDevelopment = env('REBRICKABLE_DEV');
	}
	
	private function checkCache($reuquestUrl){
		$aParams = array(
				'request' => $reuquestUrl,
				'dateCreated' => Carbon::today()
		);
		$results = \App\rebrickApiCache::where($aParams)->first();
		
		if(!$results){
			return false;
		} else {
			return $results->response;
		}
	}

    public function execute(){
        $request = $this->request;
		
		$curl_url=$request->path."?key=".$this->apiAccess;
		if("GET"==$request->method && count($request->params))
		{
            $curl_url=$request->path."?".http_build_query($request->params)."&key=".$this->apiAccess;
		}

		$response = $this->checkCache($curl_url);

		if(!$response){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->endpoint.$curl_url);
			
			if($this->isDevelopment){
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			/*if($request->method == 'DELETE' || $request->method == 'PUT')
			{
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->method);
			}
			if($request->method=='PUT')
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			}
			if($request->method=='POST')
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			}*/

			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				//Execute
			$response = curl_exec($ch);
			
			$RebrickableApiResponse = new RebrickableAPIResponse($ch, $response);

			if(!$RebrickableApiResponse->hasError){
				$oRequest = new \App\rebrickApiCache;
				$oRequest->request = $curl_url;
				$oRequest->dateCreated = Carbon::today();
				$oRequest->response = $response;
				$oRequest->save();
			}

			curl_close($ch);
		} else {
			$RebrickableApiResponse = new RebrickableAPIResponse($this->endpoint.$curl_url, $response);
		}

		//Actions performed if successful
		if(isset($RebrickableApiResponse->count)){
			//Remove any mention of our key (just incase)
			$RebrickableApiResponse->next = str_replace($this->apiAccess,'',$RebrickableApiResponse->next);
			$RebrickableApiResponse->previous = str_replace($this->apiAccess,'',$RebrickableApiResponse->previous);
			$RebrickableApiResponse->rawRequest = str_replace($this->apiAccess,'',$RebrickableApiResponse->rawRequest);

			//for pagination
			$RebrickableApiResponse->pages = ceil($RebrickableApiResponse->count/$this->perpage);
		}

			//Close and return response
		
		return $RebrickableApiResponse;
	}
    
    public function request($method, $url, $params=[]){
		if(isset($params['page_size'])){
			$this->perpage = $params['page_size'];
		} else {
			//$params['page_size'] = 200;
			$this->perpage = 200;
		}
		$request = new RebrickableAPIRequest([
			'method'=>$method,
			'path'=> $url,
			'params' => $params
			]);
		
		$this->request = $request;

		return $this;
	}
}