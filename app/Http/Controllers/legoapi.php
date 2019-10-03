<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RebrickableAPI\RebrickableAPI;

class legoapi extends Controller
{
    public $RebrickableApi = null;

    public function __construct(){
      $this->RebrickableAPI = new RebrickableAPI();
    }

    /*
        @desc search lego parts based on search parameters search term, category, colour and page number
		@param Request object $request
		@return Response
	*/
    public function search(Request $request){
        $validatedData = $request->validate([
            'inputName' => 'max:512'
        ]);
        
        $searchTerm = $request->input('inputName');
        $categoryId = $request->input('inputCategories');
        $colorId = $request->input('inputColours');
        $page = $request->input('page');

        $searchParams = array(
            'page_size' => 10,
            'ordering' => 'name',
            'page' => $page
        );
        if($searchTerm)
            $searchParams['search'] = $searchTerm;
        if($categoryId)
            $searchParams['part_cat_id'] = $categoryId;
        if($colorId!='')
            $searchParams['color_id'] = $colorId;

        $response = $this->RebrickableAPI->request('GET', '/parts/', $searchParams)->execute();
        
        return response()->json($response);
    }

    /*
        @desc call the api for colour results when we have the part number
		@param Request object $request
		@return Response (json)
	*/
    public function getColourByPart(Request $request){
        $validatedData = $request->validate([
            'part_num' => 'required'
        ]);

        $requestLink = '/parts/'.$request->input('part_num').'/colors/';
        $searchParams = array(
            'page_size' => 1000
        );
        $response = $this->RebrickableAPI->request('GET', $requestLink, $searchParams)->execute();
        
        if(!$response->hasError){
            //For some reason ordering by name doesn't work for this call, so we need to re-order the results using ksort
            $aReturn = array();
            foreach ($response->results as $oResult) {
                if($oResult->num_sets!=0){
                    $aReturn[$oResult->color_name] = array(
                        'img_url' => $oResult->part_img_url,
                        'set_count' => $oResult->num_sets,
                        'color_id' => $oResult->color_id
                    );
                }
            }

            ksort($aReturn);

            return response()->json($aReturn);
        } else {
            return false;
        }
    }

}