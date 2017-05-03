<?php
//if (!isset ($_SESSION)) session_start();

function clean_var($value){
	global $lnk;
    //$newVal = trim($value);
    if(!is_array($value)){$newVal = mysqli_real_escape_string($lnk, $value);}
	else{ return $value;}
    return $newVal;
}
function generate_user_unique_id(){
	global $lnk;
	while(true){
		$possible = '1234567890qwertyuiopasdfghjklzxcvbnm';
		$user_id = '';
		$c = 0;
		while ($c < 8) {
			$user_id .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$c++;
		}
		$q = mysqli_query($lnk, "SELECT * FROM users WHERE user_unique_id = '$user_id' ")
			 or die("Invalid query: " . mysqli_error($lnk));
		$rows  = mysqli_num_rows($q);
		if($rows == 0) break;
	}
	return $user_id;
}
function user_unique_id_exist($user_unique_id){
	global $lnk;
	$q = mysqli_query($lnk, "SELECT * FROM users WHERE user_unique_id = '$user_unique_id' ")
		 or die("Invalid query: " . mysqli_error($lnk));
	$rows  = mysqli_num_rows($q);
	if($rows == 0){
		return false;
	}else{
		return true;
	}
	
}
?>