<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test1 extends Model
{
    //
    protected $table = 'test1';
    public $timestamps = false;


    public static function getCount(){
        return Test1::count();
    }

    public static function randomItem(){
        $id = rand(1,Test1::getCount());
        return Test1::where('id',$id)->first();
    }
}
