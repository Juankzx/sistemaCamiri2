<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\Sucursale;



class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $sucursales = Sucursale::all();
        $selectedSucursal = $request->input('sucursal_id', $sucursales->first()->id);

        

        return view('home', compact('sucursales'));
    }
}