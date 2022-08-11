<?php

//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp
//GANTI $this->token .... $token

include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$id_smt = $data['id_smt'];
$kode_mk = $data['kode_mk'];
$nm_mk = $data['nm_mk'];
$nm_kls = $data['nm_kls'];
$kode_prodi = $data['kode_prodi'];

$path_directory_LOG = '../LOG/'.$id_smt.'/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$id_smt."-".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

$table = 'kelas_kuliah';			

$filter_sms = "id_sp='".$id_sp."' and kode_prodi ilike '%".$kode_prodi."%'";
$temp_sms = $proxy->GetRecord($token,'sms',$filter_sms);
if($temp_sms['result']){
	$id_sms = $temp_sms['result']['id_sms'];
	$id_jenj_didik = $temp_sms['result']['id_jenj_didik'];
}

//Filter MATA_KULIAH
$id_mk='';
$temp_filter = "kode_mk ilike '".$kode_mk."' and nm_mk ilike '%".substr($nm_mk,0,6)."%'";	
$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
if($temp_query['result']){
	$id_mk = $temp_query['result']['id_mk'];	
}else{
	//jaga-jaga tidak masuk
	$temp_filter = "kode_mk ilike '".$kode_mk."'";	
	$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
	if($temp_query['result']){
		$id_mk = $temp_query['result']['id_mk'];	
	}
}

//filter utk SKS  KELAS
$temp_filter = "id_mk = '".$id_mk."'";	
$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
if($temp_query['result']){
	$sks_mk=$temp_query['result']['sks_mk'];
	$sks_tm=$temp_query['result']['sks_tm'];
	$sks_prak=$temp_query['result']['sks_prak'];
	$sks_prak_lap=$temp_query['result']['sks_prak_lap'];
	$sks_sim=$temp_query['result']['sks_sim'];
}

//echo "sks_mk : ".$temp_filter;


//EXE KELAS_KULIAH
/*
$temp_filter = "p.id_sms='".$id_sms."' AND p.id_smt='".$id_smt."' AND nm_kls ilike '%".$nm_kls."%' AND id_mk = '".$id_mk."'";
$temp_query = $proxy->GetRecord($token,'kelas_kuliah.raw',$temp_filter);
if($temp_query['result']){
	$id_kls = $temp_query['result']['id_kls'];	
}
*/

$temp_filter = "p.id_sms='".$id_sms."' AND p.id_smt='".$id_smt."' AND nm_kls ilike '".$nm_kls."' AND id_mk = '".$id_mk."'";
$temp_query = $proxy->GetRecord($token,'kelas_kuliah.raw',$temp_filter);
if($temp_query['result']){
	$id_kls = $temp_query['result']['id_kls'];	
}

$record['id_sms'] = $id_sms ;
$record['id_smt'] = $id_smt;
$record['nm_kls'] = $nm_kls;
$record['id_mk'] = $id_mk;

$record['sks_mk'] = $sks_mk;
$record['sks_tm'] = $sks_tm;
$record['sks_prak'] = $sks_prak;
$record['sks_prak_lap'] = $sks_prak_lap;
$record['sks_sim'] = $sks_sim;


$data_kelas_kuliah = $record;

$update_kelas_kuliah ='';
$insert_kelas_kuliah = '';
					
$error_update_kelas_kuliah ='';
$error_insert_kelas_kuliah = '';

if($id_kls !=''){
	$temp_key = array('id_kls' => $id_kls);
	$array_update = array('key'=>$temp_key,'data'=>$data_kelas_kuliah);
	$temp_execution = $proxy->UpdateRecord($token,'kelas_kuliah',json_encode($array_update));
	$update_kelas_kuliah = $temp_execution;
	
	$error_update_kelas_kuliah =$update_kelas_kuliah['result']['error_desc'];
}else{
	
	$temp_execution = $proxy->InsertRecord($token,'kelas_kuliah',json_encode($record));
	$insert_kelas_kuliah = $temp_execution;
	
	$error_insert_kelas_kuliah =$insert_kelas_kuliah['result']['error_desc'];
}


if($error_insert_kelas_kuliah==NULL AND $error_update_kelas_kuliah==NULL){
	$LOG=$LOG."[SUCCESS]Data Kelas dengan Kode Matakuliah/Label '".$kode_mk."'/'".$nm_kls."' berhasil ditambahkan <br>------------------------------------<br>";
	$REPORT="[SUCCESS]Data Kelas dengan Kode Matakuliah/Label '".$kode_mk."'/'".$nm_kls."' berhasil ditambahkan <br>------------------------------------<br>";
}else{
	$LOG=$LOG."[ERROR] pada data ".$kode_mk."/".$nm_kls." - <br> DESC INSERT KELAS_KULIAH : ".$error_insert_kelas_kuliah." <br> DESC UPDATE KELAS_KULIAH : ".$error_update_kelas_kuliah." <br>------------------------------------<br> ";
	$REPORT="<br>[ERROR]Error pada data ".$kode_mk."/".$nm_kls." - <br> DESC INSERT KELAS_KULIAH : ".$error_insert_kelas_kuliah." <br> DESC UPDATE KELAS_KULIAH : ".$error_update_kelas_kuliah." <br>------------------------------------<br> ";
}

$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	


echo $REPORT;



?>

