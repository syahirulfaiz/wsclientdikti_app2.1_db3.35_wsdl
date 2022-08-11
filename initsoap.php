<?php

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);

session_start();

require_once('nusoap/nusoap.php');
require_once('nusoap/class.wsdlcache.php');

$url = 'http://localhost:8082/ws/live.php?wsdl';
//$url = 'http://localhost:8082/ws/sandbox.php?wsdl';
$client = new nusoap_client($url, true);

$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    die();
}

$proxy = $client->getProxy();



$username = '202006'; 
$password = '11890'; 
$result = $proxy->GetToken($username, $password); 
$token = $result;

$_SESSION['token'] = $token;


$filter_sms = "npsn ilike '%".$username."%'";
$temp_sms = $proxy->GetRecord($token,'satuan_pendidikan.raw',$filter_sms);
	if($temp_sms['result']){
		$id_sp = $temp_sms['result']['id_sp'];
		$nm_lemb = $temp_sms['result']['nm_lemb'];
	}


