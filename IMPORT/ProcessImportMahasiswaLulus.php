<?php

//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp
//GANTI $this->token .... $token
//GANTI print_r(xxx,true);


include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$jumlah_lulusan =  $_REQUEST['jumlah_lulusan'];


$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$nipd = $data['nipd']; 
$nm_pd = $data['nm_pd'];	
if (!empty($data['tgl_keluar'])){
	$tgl_keluar = date('Y-m-d',strtotime($data['tgl_keluar']));	
}							 
$sk_yudisium = $data['sk_yudisium'];
if (!empty($data['tgl_sk_yudisium'])){
	$tgl_sk_yudisium = date('Y-m-d',strtotime($data['tgl_sk_yudisium']));
}
$ipk = $data['ipk'];		
$jalur_skripsi = $data['jalur_skripsi'];		
$judul_skripsi = $data['judul_skripsi'];		
$no_seri_ijazah = $data['no_seri_ijazah'];	
$wisuda_ke = $data['wisuda_ke'];

$id_jns_keluar = $data['id_jns_keluar'];	

$filter_ket = "id_jns_keluar = '".$id_jns_keluar."' ";
	$temp_filter_ket = $proxy->GetRecord($token,'jenis_keluar',$filter_ket);
	if($temp_filter_ket['result']){
		$ket = $temp_filter_ket['result']['ket_keluar'];	
	}

$bln_awal_bimbingan = date('Y-m-d',strtotime($data['bln_awal_bimbingan']));
$bln_akhir_bimbingan = 	date('Y-m-d',strtotime($data['bln_akhir_bimbingan']));



$path_directory_LOG = '../LOG/LULUSAN';

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$wisuda_ke."-".$jumlah_lulusan."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

$table = 'mahasiswa';
$table1 = 'mahasiswa_pt';



//filter Mahasiswa PT
$filter_mhspt = "nipd ilike '%".$nipd."%' and p.id_sp='".$id_sp."'";
$temp_mhspt = $proxy->GetRecord($token,'mahasiswa_pt',$filter_mhspt);
if($temp_mhspt['result']){
	$id_reg_pd = $temp_mhspt['result']['id_reg_pd'];	
}
else $id_reg_pd ='';
					
$key = array('id_reg_pd' => $id_reg_pd);
 //TIDAK BOLEH ADA UPDATE NIM
$record['tgl_keluar'] = $tgl_keluar;
$record['ket'] = $ket;
$record['bln_awal_bimbingan'] = $bln_awal_bimbingan;
$record['bln_akhir_bimbingan'] = $bln_akhir_bimbingan;
$record['id_jns_keluar'] = $id_jns_keluar;
$record['sk_yudisium'] = $sk_yudisium;					
$record['tgl_sk_yudisium'] = $tgl_sk_yudisium;
$record['jalur_skripsi'] = $jalur_skripsi;
$record['judul_skripsi'] = $judul_skripsi;
$record['ipk'] = $ipk;
$record['no_seri_ijazah'] = $no_seri_ijazah;

				
$data = $record;

$array_key = array('key'=>$key,'data'=>$data);
					
$update_data= $proxy->UpdateRecord($token,'mahasiswa_pt',json_encode($array_key));		
					
if($update_data['result']['error_desc']==NULL){
	$LOG=$LOG."[SUCCESS] Data NIM / Nama '".$nipd." / ".$nm_pd."' berhasil di Update dengan status '".$ket."' <br>";	
	$REPORT="[SUCCESS] Data NIM / Nama '".$nipd." / ".$nm_pd."' berhasil di Update dengan status '".$ket."' <br>";							
}
else{
	$LOG=$LOG."[Error] pada data ( '".$nipd." / ".$nm_pd.") : '".$update_data['result']['error_desc']."' <br>";
	$REPORT="[Error] pada data ( '".$nipd." / ".$nm_pd.") : '".$update_data['result']['error_desc']."' <br>";							
}					

$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	

echo $REPORT;



?>

