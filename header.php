<?php

// include common file
include("include/common.php");

// clean all $_POST, $_GET & $_COOKIE
if(!defined("_ADMIN"))
{
    $_POST = security($_POST);
    $_GET = security($_GET);
    $_COOKIE = security($_COOKIE);
}

// get total space used by member
$q_space = "SELECT SUM(file_size) AS sum_size FROM uploads WHERE upload_owner = '".$user->user_info['user_id']."'";
$r_space = mysql_query($q_space);
$s = mysql_fetch_array($r_space);
$tpl->member_space_used = $s['sum_size'];
if($user->user_info['premium'] == 1 && $user->user_info['premium_active'] == 1){ $m = $config['max_space_prem']; }else{ $m = $config['max_space_reg']; }
$tpl->max_space = $m;

?>