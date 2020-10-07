<?php
namespace App\Http\Controllers;

use App\Http\Controllers\main;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

use Storage;
class main extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */

    public static function init()
    {
        //list all cars
        $cars = DB::table('carinfo_storage')->get();


        return $cars;
    }
    public static function search($req)
    {
        //list after search
        //switch between choosen search method
        switch ($req->post()['choose']) {
            case 'brand':
                //check if searching for company cars
                if(isset($req->post()['company'])){
                    //database quary
                    $cars = DB::table('carinfo_storage')
                        ->where('brand', 'like', $req->post()['brand'])
                        ->where('level', '=', 'company')
                        ->get();
                }else{
                    //database quary
                    $cars = DB::table('carinfo_storage')
                        ->where('brand', 'like', $req->post()['brand'])
                        ->where('level', '=', 'private')
                        ->get();
                }
                
                break;
            case 'year':
                //check if searching for company cars
                if(isset($req->post()['company'])){
                    //database quary
                    $cars = DB::table('carinfo_storage')
                        ->where('year', '=', $req->post()['year'])
                        ->where('level', '=', 'company')
                        ->get();
                }else{
                    //database quary
                    $cars = DB::table('carinfo_storage')
                        ->where('year', '=', $req->post()['year'])
                        ->where('level', '=', 'private')
                        ->get();
                }
                break;
            case 'reg':
                //check if searching for company cars
                if(isset($req->post()['company'])){
                    //database quary
                    $cars = DB::table('carinfo_storage')
                        ->where('LicensePlate', '=', $req->post()['reg'])
                        ->where('level', '=', 'company')
                        ->get();
                }else{
                    //database quary
                    $cars = DB::table('carinfo_storage')
                        ->where('LicensePlate', '=', $req->post()['reg'])
                        ->where('level', '=', 'private')
                        ->get();
                }
        }
        
        return $cars;
    }

}
?>