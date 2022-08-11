<?php

//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp
//GANTI $this->token .... $token
//GANTI print_r(xxx,true);

include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$kode_prodi = $data['kode_prodi'];
$nilai_huruf  = $data['nilai_huruf'];
$bobot_nilai_min = $data['bobot_nilai_min'];
$bobot_nilai_maks = $data['bobot_nilai_maks'];
$nilai_indeks = $data['nilai_indeks'];
$tgl_mulai_efektif  = '1970-01-01';
$tgl_akhir_efektif  = '2099-12-31';		

$path_directory_LOG = '../LOG/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

$table = 'bobot_nilai';	


$record['bobot_nilai_min'] = $bobot_nilai_min;
$record['bobot_nilai_maks'] = $bobot_nilai_maks;
$record['nilai_indeks'] = $nilai_indeks;
$record['tgl_mulai_efektif'] = $tgl_mulai_efektif;
$record['tgl_akhir_efektif'] = $tgl_akhir_efektif;

$data_bobot_nilai = $record;//data BOBOT NILAI

$filter_sms = "id_sp='".$id_sp."' and kode_prodi ilike '%".$kode_prodi."%'";
$temp_sms = $proxy->GetRecord($token,'sms',$filter_sms);
if($temp_sms['result']){
	$id_sms = $temp_sms['result']['id_sms'];
	$id_jenj_didik = $temp_sms['result']['id_jenj_didik'];
}

//CARI bobot_nilai
$kode_bobot_nilai='';
$temp_filter = "id_sms = '".$id_sms."' and nilai_huruf = '".$nilai_huruf."'";
$temp_query = $proxy->GetRecord($token,'bobot_nilai.raw',$temp_filter);
if($temp_query['result']){
	$kode_bobot_nilai = $temp_query['result']['kode_bobot_nilai'];
}

$update_bobot_nilai ='';
$insert_bobot_nilai = '';
					
$error_update_bobot_nilai ='';
$error_insert_bobot_nilai = '';

if($kode_bobot_nilai!=''){
	$temp_key = array('id_sms' => $id_sms,'nilai_huruf' => $nilai_huruf);
	$array_update = array('key'=>$temp_key,'data'=>$data_bobot_nilai);
	$temp_execution = $proxy->UpdateRecord($token,'bobot_nilai',json_encode($array_update));
	$update_bobot_nilai = $temp_execution;
	//$LOG=$LOG."UPDATE BOBOT NILAI (".$kode_prodi."-".$nilai_huruf.") : ".print_r($temp_execution,true)."<br>";
						
	$error_update_bobot_nilai =$update_bobot_nilai['result']['error_desc'];
					
}else{
	$record['id_sms'] = $id_sms;
	$record['nilai_huruf'] = $nilai_huruf;
					
	$temp_execution = $proxy->InsertRecord($token,'bobot_nilai',json_encode($record));
	$insert_bobot_nilai = $temp_execution;	
	//$LOG=$LOG."INSERT BOBOT NILAI (".$kode_prodi."-".$nilai_huruf.") : ".print_r($temp_execution,true)."<br>";
						
	$error_insert_bobot_nilai =$insert_bobot_nilai['result']['error_desc'];
						
}


if($error_insert_bobot_nilai==NULL AND $error_update_bobot_nilai==NULL){
	$LOG=$LOG."[SUCCESS]Data BOBOT NILAI (".$kode_prodi."-".$nilai_huruf.") berhasil tersimpan <br>";	
	$REPORT="<br>[SUCCESS]Data BOBOT NILAI (".$kode_prodi."-".$nilai_huruf.") berhasil tersimpan <br>";	
}
else{
	$LOG=$LOG."<br>[Error] pada data BOBOT NILAI (".$kode_prodi."-".$nilai_huruf.") <br> DESC INSERT BOBOT NILAI : ".$error_insert_bobot_nilai." <br> DESC UPDATE BOBOT NILAI : ".$error_update_bobot_nilai." <br>------------------------------------<br> ";	
	$REPORT="<br>[Error] pada data BOBOT NILAI (".$kode_prodi."-".$nilai_huruf.") <br> DESC INSERT BOBOT NILAI : ".$error_insert_bobot_nilai." <br> DESC UPDATE BOBOT NILAI : ".$error_update_bobot_nilai." <br>------------------------------------<br> ";
}


$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	

echo $REPORT;



?>

