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
$nidn =  $data['nidn'];
$kode_mk  = $data['kode_mk'];
$nm_mk  = $data['nm_mk'];
$sks_subst_tot =  $data['sks_subst_tot'];
$sks_tm_subst = $sks_subst_tot;
$sks_prak_subst = 0;
$sks_prak_lap_subst = 0;
$sks_sim_subst = 0;
$jml_tm_renc =  $data['jml_tm_renc'];
$jml_tm_real = $jml_tm_renc;
$kode_prodi = $data['kode_prodi'];
$nm_kls = $data['nm_kls'];


$path_directory_LOG = '../LOG/'.$id_smt.'/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$id_smt."-".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

//Filter SMS
$filter_sms = "id_sp='".$id_sp."' and kode_prodi ilike '%".$kode_prodi."%'";
$temp_sms = $proxy->GetRecord($token,'sms',$filter_sms);
if($temp_sms['result']){
	$id_sms = $temp_sms['result']['id_sms'];
	$id_jenj_didik = $temp_sms['result']['id_jenj_didik'];
}

//Filter Dosen
if ($nidn!=''){
	$fildosen = "nidn ilike '%".$nidn."%'";
}else{
	$fildosen = "nidn ilike '%wkwkwkwkwk%'";
}

$temp_dosen = $proxy->GetRecord($token,'dosen',$fildosen);
if($temp_dosen['result']){
	$id_sdm = $temp_dosen['result']['id_sdm'];
	$nm_sdm = $temp_dosen['result']['nm_sdm'];
}else $id_sdm = '';


//Filter Dosen PT
$fildosenpt = "p.id_sdm = '".$id_sdm."'";
$temp_dosenpt = $proxy->GetRecord($token,'dosen_pt.raw',$fildosenpt);
if($temp_dosenpt['result']){
	$id_reg_ptk = $temp_dosenpt['result']['id_reg_ptk'];
}else $id_reg_ptk = '';


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


//Filter Kelas KUliah
/*
$filter_kls = "p.id_mk = '".$id_mk."' and nm_kls = '".$nm_kls."' and p.id_smt='".$id_smt."'";
$temp_kls = $proxy->GetRecord($token,'kelas_kuliah',$filter_kls);
if($temp_kls['result']){
	$id_kls = $temp_kls['result']['id_kls'];
	$nm_kls = $temp_kls['result']['nm_kls'];
}else $id_kls;
*/
$temp_filter = "p.id_sms='".$id_sms."' AND p.id_smt='".$id_smt."' AND nm_kls ilike '".$nm_kls."' AND id_mk = '".$id_mk."'";
$temp_query = $proxy->GetRecord($token,'kelas_kuliah.raw',$temp_filter);
if($temp_query['result']){
	$id_kls = $temp_query['result']['id_kls'];	
}
					
$record['id_jns_eval'] = '1';
$record['id_reg_ptk'] = $id_reg_ptk ;
$record['id_kls'] = $id_kls;
$record['sks_subst_tot'] = $sks_subst_tot ;
$record['sks_tm_subst'] = $sks_tm_subst;
$record['sks_prak_subst'] = $sks_prak_subst ;
$record['sks_prak_lap_subst'] = $sks_prak_lap_subst;
$record['sks_sim_subst'] = $sks_sim_subst;
$record['jml_tm_renc'] = $jml_tm_renc ;
$record['jml_tm_real'] = $jml_tm_real;

$data_ajar_dosen = $record;

//CARI ajar_dosen
$id_ajar='';
$temp_filter = "id_jns_eval = '".$id_jns_eval."' and id_reg_ptk = '".$id_reg_ptk."' and id_kls='".$id_kls."'";
$temp_query = $proxy->GetRecord($token,'ajar_dosen.raw',$temp_filter);
if($temp_query['result']){
	$id_ajar = $temp_query['result']['id_ajar'];
}

$update_ajar_dosen ='';
$insert_ajar_dosen = '';
					
$error_update_ajar_dosen ='';
$error_insert_ajar_dosen = '';

if($id_ajar!=''){
	$temp_key = array('id_ajar' => $id_ajar);
	$array_update = array('key'=>$temp_key,'data'=>$data_ajar_dosen);
	$temp_execution = $proxy->UpdateRecord($token,'ajar_dosen',json_encode($array_update));
	$update_ajar_dosen = $temp_execution;
						
	$error_update_ajar_dosen =$update_ajar_dosen['result']['error_desc'];
	
}else{
					
	$temp_execution = $proxy->InsertRecord($token,'ajar_dosen',json_encode($record));
	$insert_ajar_dosen = $temp_execution;	
						
	$error_insert_ajar_dosen =$insert_ajar_dosen['result']['error_desc'];

}
					
if($error_insert_ajar_dosen==NULL AND $error_update_ajar_dosen==NULL){
	$LOG=$LOG."[SUCCESS]Data Ajar dosen dengan NIDN : '".$nidn."'/'".$nm_sdm."'/'".$kode_mk."'/'".$nm_kls."' berhasil di simpan <br>-------------------------------<br>";
	$REPORT="<br>[SUCCESS]Data Ajar dosen dengan NIDN : '".$nidn."'/'".$nm_sdm."'/'".$kode_mk."'/'".$nm_kls."' berhasil di simpan <br>-------------------------------<br>";	
}else{
	$LOG=$LOG."[Error] pada data : NIDN/Kode Matkul/Kelas Label '".$nidn."'/'".$nm_sdm."'/'".$kode_mk."'/'".$nm_kls."' <br> ERROR INSERT AJAR DOSEN : '".$error_insert_ajar_dosen."' <br> ERROR UPDATE AJAR DOSEN : '".$error_update_ajar_dosen."'  <br>--------------------------------------------<br>";	
	$REPORT="[Error] pada data : NIDN/Kode Matkul/Kelas Label '".$nidn."'/'".$nm_sdm."'/'".$kode_mk."'/'".$nm_kls."' <br> ERROR INSERT AJAR DOSEN : '".$error_insert_ajar_dosen."' <br> ERROR UPDATE AJAR DOSEN : '".$error_update_ajar_dosen."'  <br>--------------------------------------------<br>";
					

}

$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	

echo $REPORT;



?>

