<?php

//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp
//GANTI $this->token .... $token
//GANTI print_r(xxx,true);

include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$id_smt = $data['id_smt'];
$target_mhs_baru = $data['target_mhs_baru'];
$calon_ikut_seleksi = $data['calon_ikut_seleksi'];
$calon_lulus_seleksi = $data['calon_lulus_seleksi'];
$daftar_sbg_mhs = $data['daftar_sbg_mhs'];
$pst_undur_diri = $data['pst_undur_diri'];
$tgl_awal_kul = $data['tgl_awal_kul'];
$tgl_akhir_kul = $data['tgl_akhir_kul'];
$jml_mgu_kul = $data['jml_mgu_kul']; 
$metode_kul = $data['metode_kul']; 
$metode_kul_eks = $data['metode_kul_eks']; 
$kode_prodi = $data['kode_prodi'];


$path_directory_LOG = '../LOG/'.$id_smt.'/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$id_smt."-".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG
		
//filter id_sms
$filter_sms = "id_sp='".$id_sp."' and kode_prodi ilike '%".$kode_prodi."%'";
$temp_sms = $proxy->GetRecord($token,'sms',$filter_sms);
if($temp_sms['result']){
	$id_sms = $temp_sms['result']['id_sms'];
}
					
//Filter semester 
$filter_semester = "id_smt ilike '%".$id_smt."%'";
$temp_semester = $proxy->GetRecord($token,'semester',$filter_semester);
if($temp_semester['result']){
	$tgl_awal_kul = $temp_semester['result']['tgl_mulai'];
	$tgl_akhir_kul = $temp_semester['result']['tgl_selesai'];	
}	


//Count Jumlah Minggu
$startDate = new DateTime($tgl_awal_kul);
$endDate = new DateTime($tgl_akhir_kul);
$interval = $startDate->diff($endDate);
$jml_mgu_kul =  (int)(($interval->days) / 7);

//echo " id_sms : ".$id_sms." , id_smt : ".$id_smt." , tgl_awal_kul : ".$tgl_awal_kul.", tgl_akhir_kul : ".$tgl_akhir_kul.", jml_mgu_kul ".$jml_mgu_kul."<br/>";

//Filter Daya Tampung 
$filter_daya_tampung = "id_smt ilike '%".$id_smt."%' and id_sms='".$id_sms."'";
$temp_daya_tampung = $proxy->GetRecord($token,'daya_tampung',$filter_daya_tampung);
if($temp_daya_tampung['result']){
	$daya_tampung_id_sms = $temp_daya_tampung['result']['id_sms'];
	$daya_tampung_id_smt = $temp_daya_tampung['result']['id_smt'];	
}	

$record['target_mhs_baru'] = $target_mhs_baru;
$record['calon_ikut_seleksi'] = $calon_ikut_seleksi;
$record['calon_lulus_seleksi'] = $calon_lulus_seleksi;
$record['daftar_sbg_mhs'] = $daftar_sbg_mhs;
$record['pst_undur_diri'] = $pst_undur_diri;			
$record['tgl_awal_kul'] = $tgl_awal_kul;
$record['tgl_akhir_kul'] = $tgl_akhir_kul;	
$record['jml_mgu_kul'] = $jml_mgu_kul;
$record['metode_kul'] = $metode_kul;
$record['metode_kul_eks'] = $metode_kul_eks;				

					
$data_daya_tampung = $record;//data Daya Tampung

$update_daya_tampung ='';
$insert_daya_tampung = '';
					
$error_update_daya_tampung ='';
$error_insert_daya_tampung = '';

if($daya_tampung_id_sms!='' && $daya_tampung_id_smt!=''){
	$temp_key = array('id_sms' => $daya_tampung_id_sms,'id_smt' => $daya_tampung_id_smt);
	$array_update = array('key'=>$temp_key,'data'=>$data_daya_tampung);
	$temp_execution = $proxy->UpdateRecord($token,'daya_tampung',json_encode($array_update));
	$update_daya_tampung = $temp_execution;
						
	$error_update_daya_tampung =$update_daya_tampung['result']['error_desc'];
					
}else{
	$record['id_smt'] = $id_smt;
	$record['id_sms'] = $id_sms;
					
	$temp_execution = $proxy->InsertRecord($token,'daya_tampung',json_encode($record));
	$insert_daya_tampung = $temp_execution;	
						
	$error_insert_daya_tampung =$insert_daya_tampung['result']['error_desc'];
						
}


if($error_insert_daya_tampung==NULL AND $error_update_daya_tampung==NULL){
	$LOG=$LOG."[SUCCESS]Data DAYA TAMPUNG (".$id_smt."-".$kode_prodi.") berhasil tersimpan <br>";	
	$REPORT="[SUCCESS]Data DAYA TAMPUNG (".$id_smt."-".$kode_prodi.") berhasil tersimpan <br>";	
}
else{
	$LOG=$LOG."<br>[Error] pada data DAYA TAMPUNG (".$id_smt."-".$kode_prodi.") <br> DESC INSERT DAYA TAMPUNG : ".$error_insert_daya_tampung." <br> DESC UPDATE DAYA TAMPUNG : ".$error_update_daya_tampung." <br>------------------------------------<br> ";	
	$REPORT="<br>[Error] pada data DAYA TAMPUNG  (".$id_smt."-".$kode_prodi.") <br> DESC INSERT DAYA TAMPUNG : ".$error_insert_daya_tampung." <br> DESC UPDATE AKM : ".$error_update_daya_tampung." <br>------------------------------------<br> ";
}


$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	

echo $REPORT;



?>

