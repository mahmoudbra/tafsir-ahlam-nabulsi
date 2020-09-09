<?php

// include header
include("header.php");

// set page name
$page = "myfiles";

// set $task var and get all tasks over $_POST, $_GET
$task = isset($_POST['task']) ? $_POST['task'] : $_GET['task'];

// switch task
switch($task)
{
    // rename file
	case "rename":
	
	    // make sure new file name is not empty
	    if($_POST['update_value'] != "")
	    {
			// set new file name
			$newName = $_POST['update_value'];
			
			// remove any spaces new file name
			$newName = str_replace(" ", "", $newName);
			
			// update database
			mysql_query("UPDATE uploads SET file_name = '".$newName."' WHERE file_id = '".$_POST['element_id']."'");
			
			// return new value
			die($newName);
		
		}else{
			
			// return old value
			die($_POST['original_html']);
		}
	
	break;
	
	// default task (show files)
	default:
	
	    // check for valid login
		$user->login_check();
		
		// get list of all members files
		$q = "SELECT file_name, file_size, upload_date, downloads, file_id, delete_id FROM uploads 
		      WHERE upload_owner='".$user->user_info['user_id']."' ORDER BY id DESC";
		$num = mysql_num_rows(mysql_query($q));
		$r = mysql_query($q);
		
		// get total space used by member
		$q_space = "SELECT SUM(file_size) AS sum_size FROM uploads WHERE upload_owner = '".$user->user_info['user_id']."'";
		$r_space = mysql_query($q_space);
		$s = mysql_fetch_array($r_space);
		
		// set total allowed space
		if($user->user_info['premium'] == 1 && $user->user_info['premium_active'] == 1){ $m = $config['max_space_prem']; }else{ $m = $config['max_space_reg']; }
		
		// assign template vars
		$tpl->result = $r;
		$tpl->total = $num;
		$tpl->space_used = $s['sum_size'];
		$tpl->total_space = $m;
}

// include footer
include("footer.php");