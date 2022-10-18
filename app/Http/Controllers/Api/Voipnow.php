<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SoapClient;
class Voipnow extends Controller
{
    public $SYSTEMAPI_KEY = "h.wAO6FJBN_2TnrR9st~B05TNLxq_xkn";
    public $SYSTEMAPI_SECRET = "O-EI9HhT62.6xOcTVtQ.ypnqCW53-nxK";
    public $TOKEN_URL = "https://sip3.melotel.com/oauth/token.php";
    public $SOAP_DOMAIN = "sip3.melotel.com";
    public $SOAP_VERSION = "3.5.0";

        function has_token(){
        if(!isset($_SESSION['access_token'])
           || $_SESSION['access_token']["token"] == ""
           || intval($_SESSION['access_token']["systemapi"]["expires_at"]) > time()-10 ){
            return false;
        }
        return false;
    }

    function set_access_token($token_info){
        $_SESSION['access_token'] = array("token"=>$token_info["token"],
                "expires_at"=>time()+intval($token_info["expires_at"]));
        return $token_info["token"];
    }


    function get_access_token($domain,$systemapi_key, $systemapi_secret){
         $token = $this->has_token();
        if($token){ return $token; }
        // $request->validate([
        //     'domain'=>'required',
        //     'systemapi_key'=>'required',
        //     'systemapi_secret'=>'required'
        // ]);
        set_time_limit(0);

        $data = array("grant_type"=>"client_credentials","redirect_uri"=>"https://melometer.ca/","client_id"=>$systemapi_key,"client_secret"=>$systemapi_secret,"state"=>"dev");

       $response  = $this->postCurl("https://".$domain."/oauth/token.php", $data,true);

        if($response["access_token"] == ""){ return false; }

        return array("token"=>$response["access_token"],"expires_at"=>time()+10000);
    }


    function create_client($token=null, $domain = null, $key = null, $secret = null){

        
        $client = new SoapClient('https://' . $domain . '/soap2/schema/' . $this->SOAP_VERSION . '/voipnowservice.wsdl',array('trace' => 1, 'exceptions' => 0));
        $auth = new \stdClass();
        $auth->accessToken = (is_null($token)||!$token)?$this->get_access_token($domain, $key, $secret):$token;
        $userCredentials = new SoapVar($auth, SOAP_ENC_OBJECT, 'http://4psa.com/HeaderData.xsd/' . $this->SOAP_VERSION);
        $header = new SoapHeader('http://4psa.com/HeaderData.xsd/' . $this->SOAP_VERSION, 'userCredentials', $userCredentials, false);
        dd($header);
        $client->__setSoapHeaders(array($header));
        return $client;

    }

     function do_soap_request($client, $method, $request){
        if($client){
        $result = call_user_func_array(array($client, $method), array($request));
        // $cu = \Melometer\Security\get_current_user();
        // \Melometer\DB\insert("voip_call", array("owner_id"=>  \Melometer\User\get_current_user_owner_id(),
        //     "user_id"=>$cu["id"],
        //     "call_type"=>"soap",
        //     "request"=>$client->__getLastRequest(),
        //     "response"=>$client->__getLastResponse(),
        //     "init_time"=>time()));
            return $result;
        }
        return null;
    }

     function get_service_providers($domain, $key, $secret){
        $response =  $this->do_soap_request($this->create_client(null, $domain, $key, $secret), "GetServiceProviders",array());
        return $response->serviceProvider;
    }
 function postCurl($url, $data, $json_decode = false){
        $fields = $data;

        $fields_string = http_build_query($fields);
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        if($json_decode){
            $result = json_decode($result, true);
        }
        return $result;
    }

}
