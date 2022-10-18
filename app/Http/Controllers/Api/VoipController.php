<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RicorocksDigitalAgency\Soap\Facades\Soap;

class VoipController extends Controller
{
    public function getAccessToken(){
        $response = Soap::to('https://sip3.melotel.com/soap2/schema/3.5.0/voipnowservice.wsdl')->data(array('trace' => 1, 'exceptions' => 0));
        return $response;
    }
}
