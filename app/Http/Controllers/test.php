<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Test1;

class test extends Controller
{
    //
    public function __invoke(){
        return view('welcome')->with('jumboData', \App\Test1::randomItem());
    }
}
