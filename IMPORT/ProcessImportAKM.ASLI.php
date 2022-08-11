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
$nipd = $data['nipd'];
$nm_pd = $data['nm_pd'];
//$sks_smt = $data['sks_smt']; //karena perubahan sistem Feeder 2.1, jumlah sks KRS mahasiswa di MENU MAHASISWA harus sama dengan sks semester import AKM
//Filter SKS semester
$filter_sks = "nipd ilike '".$nipd."' and id_smt ilike '".$id_smt."'";
$temp_sks = $proxy->GetRecordset($token,'nilai',$filter_sks,'','','');
$sks_smt = 0;
foreach($temp_sks['result'] as $row){
	$sks_smt=$sks_smt+$row['sks_mk'];
}
$sks_total = $data['sks_total'];
$ips = $data['ips'];
$ipk = $data['ipk'];
$id_stat_mhs = $data['id_stat_mhs'];

$kode_prodi = $data['kode_prodi'];

$path_directory_LOG = '../LOG/'.$id_smt.'/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$id_smt."-".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG


//Filter Mahasiswa PT 
$filter_mhspt = "nipd ilike '%".$nipd."%' and p.id_sp='".$id_sp."'";
$temp_mhspt = $proxy->GetRecord($token,'mahasiswa_pt',$filter_mhspt);
if($temp_mhspt['result']){
	$id_reg_pd = $temp_mhspt['result']['id_reg_pd'];	
}
else $id_reg_pd ='';					
					
//Filter Kuliah Mahasiswa
$filter_akm = "id_reg_pd = '".$id_reg_pd."' and id_smt = '".$id_smt."'" ;
$temp_kulmhs = $proxy->GetRecord($token,'kuliah_mahasiswa.raw',$filter_akm);
if($temp_kulmhs['result']){
	$akm_id_reg_pd = $temp_kulmhs['result']['id_reg_pd'];
	$akm_id_smt =  $temp_kulmhs['result']['id_smt'];
}
		
//echo "id_reg_pd : ".$id_reg_pd.", id_smt : ".$id_smt."<br/>";
		
$record['id_stat_mhs'] = $id_stat_mhs;
$record['ips'] = $ips;
$record['sks_smt'] = $sks_smt;
$record['ipk'] = $ipk;
$record['sks_total'] = $sks_total;					
					
$data_akm = $record;//data AKM

$update_akm ='';
$insert_akm = '';
					
$error_update_akm ='';
$error_insert_akm = '';

if($akm_id_reg_pd!='' && $akm_id_smt!=''){
	$temp_key = array('id_reg_pd' => $akm_id_reg_pd,'id_smt' => $akm_id_smt);
	$array_update = array('key'=>$temp_key,'data'=>$data_akm);
	$temp_execution = $proxy->UpdateRecord($token,'kuliah_mahasiswa',json_encode($array_update));
	$update_akm = $temp_execution;
	//$LOG=$LOG."UPDATE AKM (".$id_smt."-".$id_reg_pd.") : ".print_r($temp_execution,true)."<br>";
						
	$error_update_akm =$update_akm['result']['error_desc'];
					
}else{
	$record['id_smt'] = $id_smt;
	$record['id_reg_pd'] = $id_reg_pd;
					
	$temp_execution = $proxy->InsertRecord($token,'kuliah_mahasiswa',json_encode($record));
	$insert_akm = $temp_execution;	
	//$LOG=$LOG."INSERT AKM (".$id_smt."-".$id_reg_pd.") : ".print_r($temp_execution,true)."<br>";
						
	$error_insert_akm =$insert_akm['result']['error_desc'];
						
}


if($error_insert_akm==NULL AND $error_update_akm==NULL){
	$LOG=$LOG."[SUCCESS]Data AKM (".$id_smt."-".$nipd.") berhasil tersimpan <br>";	
	$REPORT="[SUCCESS]Data AKM (".$id_smt."-".$nipd.") berhasil tersimpan <br>";	
}
else{
	$LOG=$LOG."<br>[Error] pada data AKM (".$id_smt."-".$nipd.") <br> DESC INSERT AKM : ".$error_insert_akm." <br> DESC UPDATE AKM : ".$error_update_akm." <br>------------------------------------<br> ";	
	$REPORT="<br>[Error] pada data AKM (".$id_smt."-".$nipd.") <br> DESC INSERT AKM : ".$error_insert_akm." <br> DESC UPDATE AKM : ".$error_update_akm." <br>------------------------------------<br> ";
}


$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	


echo $REPORT;



?>

