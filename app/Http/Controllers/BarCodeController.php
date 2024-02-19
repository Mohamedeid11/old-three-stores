<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarCodeController extends Controller
{
    public function index ()
    {
        $bar_codes = array("RE-5501", "7513S", "7531", "5894", "48234", "02680",);
        $bar_codes_types = array("C39", "C39+", "C39E", "C39E+", "C93", "S25", "S25+", "I25", "I25+", "C128", "C128A", "C128B", "EAN2", 
        "EAN5", "EAN8", "EAN13", "UPCA", "UPCE", "MSI", "MSI+", "POSTNET", "PLANET", "RMS4CC", "KIX", "IMB", "CODABAR", "CODE11", "PHARMA");

        $bar_codes_types = array("C39");
        return view('bar_codes', compact(['bar_codes', 'bar_codes_types']));
    }
}
