<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
public function home(){
$data = "Hello World!";
return view('home',['data'=>$data]);
}
}