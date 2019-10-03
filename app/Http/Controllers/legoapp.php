<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RebrickableAPI\RebrickableAPI;

class legoapp extends Controller
{
    public $BricklinkApi = null;
    public $RebrickableApi = null;

    public function __construct(){
      $this->RebrickableAPI = new RebrickableAPI();
    }

    public function index(Request $request){
        
        $aReturn = array(
            'aCategories' => $this->listCategories('array'),
            'aColours' => $this->listColours('array'),
            'aBasket' => array()
        );

        if ($request->session()->has('Legobasket')) {
            $aBasket = $request->session()->get('Legobasket');
            $aReturn['aBasket'] = $aBasket;
        }
        
        return view('legoapp')->with($aReturn);
    }

    public function listCategories($return = 'json', $part = null){
        $response = $this->RebrickableAPI->request('GET', '/part_categories/', array('ordering' => 'name'))->execute();

        $aCategoriesLink = array();
        if(!$response->hasError){

            foreach($response->results as $cat){
                $aCategoriesLink[] = array(
                    'id' => intval($cat->id),
                    'name' => $cat->name . ' (' . $cat->part_count . ')'
                );
            }
            
            if($return=='json')
                return response()->json($aCategoriesLink);
            else
                return $aCategoriesLink;

        } else {
            return response()->json($response);
        }    
        
    }

    public function listColours($return = 'json', $part = null){
        $response = $this->RebrickableAPI->request('GET', '/colors/', array('page_size' => 200,'ordering' => 'name'))->execute();

        $aColorLink = array();
        if(!$response->hasError){
        
            foreach($response->results as $col){
                //Filter out data unlikely to be used
                if(isset($col->external_ids->LEGO)){
                    $aColorLink[] = array(
                        'id' => intval($col->id),
                        'name' => $col->name
                    );
                }
                
            }

            if($return=='json')
                return response()->json($aColorLink);
            else
                return $aColorLink;

        } else {
            return response()->json($response);
        }
        
    }

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

    public function addItem(Request $request){
        $request->validate([
            'part_num' => 'required',
            'color_id' => 'required',
            'color_name' => 'required'
        ]);

        $requestLink = '/parts/'.$request->input('part_num').'/colors/'.$request->input('color_id').'/';
        
        $response = $this->RebrickableAPI->request('GET', $requestLink)->execute();

        if (!$request->session()->has('Legobasket')) {
            $request->session()->put('Legobasket', array());
        }
        
        if(!$response->hasError){
            $existingSession = $request->session()->get('Legobasket');
            $aSessionData = array(
                'part_num' => $request->input('part_num'),
                'color_id' => $request->input('color_id'),
                'part_img_url' => $response->results->part_img_url,
                'num_sets' => $response->results->num_sets,
                'color_name' => $request->input('color_name')
            );
            $response->sessionData = $request->input('part_num').$request->input('color_id');
            $existingSession[$request->input('part_num').$request->input('color_id')] = $aSessionData;
            $request->session()->put('Legobasket', $existingSession);
        }
        
        return response()->json($response);
    }

    public function removeItem(Request $request){
        $validatedData = $request->validate([
            'identifier' => 'required'
        ]);

        $aReturn = array();
        $aReturn['status'] = 0;

        if ($request->session()->has('Legobasket')) {
            $aBasket = $request->session()->get('Legobasket');
            if(isset($aBasket[$request->input('identifier')])){
                unset($aBasket[$request->input('identifier')]);
                $aReturn['status'] = 1;
            }
            $request->session()->put('Legobasket', $aBasket);
        }

        return response()->json($aReturn);
    }

    public function getSetsByPart($aPartData){
        $requestLink = '/parts/'.$aPartData['part_num'].'/colors/'.$aPartData['color_id'].'/sets/';
        $searchParams = array(
            'page_size' => 1000,
            'ordering' => 'name'
        );
        $response = $this->RebrickableAPI->request('GET', $requestLink, $searchParams)->execute();
        
        if(!$response->hasError){
            return $response;
        } else {
            return false;
        }
    }

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

    public function getSets(Request $request){
        ini_set('memory_limit','512M');
        if ($request->session()->has('Legobasket')) {
            $aSets = array();

            $aBasket = $request->session()->get('Legobasket');
            foreach($aBasket as $key => $basketItem){
                $response = $this->getSetsByPart($basketItem);
                if($response!==false){
                    foreach($response->results as $oResult){
                        if(!isset($aSets[$oResult->set_num])){
                            $aInsert = array(
                                'set_num' => $oResult->set_num,
                                'name' => $oResult->name,
                                'year' => $oResult->year,
                                'image' => $oResult->set_img_url,
                                'parts' => $oResult->num_parts,
                                'external' => $oResult->set_url,
                                'parts_included' => array( 0 => array(
                                        'part_num' => $basketItem['part_num'],
                                        'color_name' => $basketItem['color_name'],
                                        'part_img_url' => $basketItem['part_img_url']
                                    )
                                ),
                                'count' => 1
                            );
                            $aSets[$oResult->set_num] = $aInsert;
                        } else {
                            
                            $aSets[$oResult->set_num]['parts_included'][] = array(
                                'part_num' => $basketItem['part_num'],
                                'color_name' => $basketItem['color_name'],
                                'part_img_url' => $basketItem['part_img_url']
                            );
                            $aSets[$oResult->set_num]['count']++;
                        }
                    }
                }
            }

            $aSorted = array();
            foreach($aSets as $key => $aData){
                if($aData['count']==1){
                    unset($aSets[$key]);
                } else {
                    $aSorted[$aData['count']][$key] = $aData;
                    unset($aSets[$key]);
                }
            }
            krsort($aSorted);

            if(!empty($aSorted)){
                return view('legoappviews.success')->with(array('aSorted' => $aSorted));
            } else {
                return view('legoappviews.empty');
            }
        } else {
            return view('legoappviews.failure');
        }
    }

    public function pdf($setId,Request $request){
        //Get set data
        $setRequestLink = '/sets/' . $setId . '/';
        $setResponse = $this->RebrickableAPI->request('GET', $setRequestLink, array())->execute();
        
        $requestLink = '/sets/' . $setId . '/parts/';
        $searchParams = array(
            'page_size' => 1000
        );
        $response = $this->RebrickableAPI->request('GET', $requestLink, $searchParams)->execute();
        if(!$response->hasError and !$setResponse->hasError){
            $aParts = array();
            $aSpares = array();
            foreach($response->results as $partdata){
                $aPart = array(
                    'name' => $partdata->part->name,
                    'partnum' => $partdata->part->part_num,
                    'color' => $partdata->color->name,
                    'image' => $partdata->part->part_img_url,
                    'qty' => $partdata->quantity
                ); 

                if($partdata->is_spare==false)
                    $aParts[] = $aPart;
                else
                    $aSpares[] = $aPart;
            }
            
            if($request->input('view'))
                return view('legoappviews.basiclist')->with(array('aParts' => $aParts,'aSpares' => $aSpares, 'oSet' => $setResponse));
            else
                return view('legoappviews.pdfcontent')->with(array('aParts' => $aParts,'aSpares' => $aSpares, 'oSet' => $setResponse));
        } else {
            //Redirect to 404
            
        }
    }
}
