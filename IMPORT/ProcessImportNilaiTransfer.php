<?php

//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp
//GANTI $this->token .... $token
//GANTI print_r(xxx,true);

include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$nipd = $data['nipd'];
$kode_mk_diakui = $data['kode_mk_diakui'];
$nm_mk_diakui = $data['nm_mk_diakui'];	
$sks_diakui = $data['sks_diakui'];
$nilai_angka_diakui = $data['nilai_angka_diakui'];
$nilai_huruf_diakui = $data['nilai_huruf_diakui'];
$kode_mk_asal = $data['kode_mk_asal'];
$nm_mk_asal = $data['nm_mk_asal'];
$sks_asal = $data['sks_asal'];
$nilai_huruf_asal = $data['nilai_huruf_asal'];

$path_directory_LOG = '../LOG/NilaiTransfer/';

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$nipd."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

//Filter Mahasiswa PT 
$filter_mhspt = "nipd ilike '%".$nipd."%' and p.id_sp='".$id_sp."'";
$temp_mhspt = $proxy->GetRecord($token,'mahasiswa_pt',$filter_mhspt);
if($temp_mhspt['result']){
	$id_reg_pd = $temp_mhspt['result']['id_reg_pd'];	
}
else $id_reg_pd ='';	


//Filter MATA_KULIAH
$id_mk='';
$temp_filter = "kode_mk ilike '".$kode_mk_diakui."' and nm_mk ilike '%".substr($nm_mk_diakui,0,6)."%'";	
$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
if($temp_query['result']){
	$id_mk = $temp_query['result']['id_mk'];	
}else{
	//jaga-jaga tidak masuk
	$temp_filter = "kode_mk ilike '".$kode_mk_diakui."'";	
	$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
	if($temp_query['result']){
		$id_mk = $temp_query['result']['id_mk'];	
	}
}
	
//echo $id_reg_pd."-".$id_mk."-".$kode_mk_diakui."-".$nm_mk_diakui;

//$record['id_reg_pd'] = $id_reg_pd;
//$record['id_mk'] = $id_mk;
$record['kode_mk_asal'] = $kode_mk_asal;
$record['nm_mk_asal'] = $nm_mk_asal;
$record['sks_asal'] = $sks_asal;
$record['sks_diakui'] = $sks_diakui;
$record['nilai_huruf_asal'] = $nilai_huruf_asal;
$record['nilai_huruf_diakui'] = $nilai_huruf_diakui;
$record['nilai_angka_diakui'] = $nilai_angka_diakui;

$data_nilai_transfer = $record;//data NILAI transfer


//CARI nilai transfer
$id_ekuivalensi='';
$temp_filter = "id_reg_pd = '".$id_reg_pd."' and id_mk = '".$id_mk."'";
$temp_query = $proxy->GetRecord($token,'nilai_transfer',$temp_filter);
if($temp_query['result']){
	$id_ekuivalensi = $temp_query['result']['id_ekuivalensi'];
}


$update_nilai_transfer = '';
$insert_nilai_transfer = '';
					
$error_update_nilai_transfer ='';
$error_insert_nilai_transfer = '';

if($id_ekuivalensi!=''){
	$temp_key = array('id_ekuivalensi' => $id_ekuivalensi, 'id_reg_pd' => $id_reg_pd, 'id_mk' => $id_mk);
	$array_update = array('key'=>$temp_key,'data'=>$data_nilai_transfer);
	$temp_execution = $proxy->UpdateRecord($token,'nilai_transfer',json_encode($array_update));
	$update_nilai_transfer = $temp_execution;
						
	$error_update_nilai_transfer =$update_nilai_transfer['result']['error_desc'];
					
}else{
	$record['id_reg_pd'] = $id_reg_pd;
	$record['id_mk'] = $id_mk;
					
	$temp_execution = $proxy->InsertRecord($token,'nilai_transfer',json_encode($record));
	$insert_nilai_transfer = $temp_execution;	
						
	$error_insert_nilai_transfer =$insert_nilai_transfer['result']['error_desc'];
						
}


if($error_insert_nilai_transfer==NULL AND $error_update_nilai_transfer==NULL){
	$LOG=$LOG."[SUCCESS] Data NILAI TRANSFER (".$nipd."-".$kode_mk_diakui." dari kode asal : ".$kode_mk_asal.") berhasil tersimpan <br>";	
	$REPORT="<br>[SUCCESS] Data NILAI TRANSFER (".$nipd."-".$kode_mk_diakui." dari kode asal : ".$kode_mk_asal.") berhasil tersimpan <br>";	
}
else{
	$LOG=$LOG."<br>[Error] pada data NILAI TRANSFER (".$nipd."-".$kode_mk_diakui.") <br> DESC INSERT BOBOT NILAI : ".$error_insert_nilai_transfer." <br> DESC UPDATE BOBOT NILAI : ".$error_update_nilai_transfer." <br>------------------------------------<br> ";	
	$REPORT="<br>[Error] pada data NILAI TRANSFER (".$nipd."-".$kode_mk_diakui.") <br> DESC INSERT BOBOT NILAI : ".$error_insert_nilai_transfer." <br> DESC UPDATE BOBOT NILAI : ".$error_update_nilai_transfer." <br>------------------------------------<br> ";
}

$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	

echo $REPORT;




?>

