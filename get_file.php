<?php

/* include header */
include("header.php");

/* set page name */
$page = "get_file";

/* generate a list of all available premium packages */
$premium = mysql_query("SELECT package_id, package_name, package_price FROM packages");

/* get file info based on vars passed */
$file = sql_row("SELECT * FROM uploads WHERE file_id = '".$_GET['file_id']."' LIMIT 1");

/* file that should be send to the client */
$local_file = $config['upload_path'] . $file['file_name'];

/* set the download rate dependant on user */
if($user->user_exists == 1 && $user->user_info['premium'] == 1 && $user->user_info['premium_active'] == 1)
{
	$download_rate = $config['premium_down_speed'];
	$resume = true;
	$wait = 0;
}else{ 
    $download_rate = $config['download_speed'];
	$resume = false;
	$wait = $config['wait_for_link'];
}

/* delete all old download sessions */
$q = mysql_query("SELECT * FROM download_sessions LIMIT 50");
while($row = mysql_fetch_assoc($q))
{
    if((time() - $row['download_start']) > 3600)
	{
		mysql_query("DELETE FROM download_sessions WHERE id = '".$row['id']."'") or die();
	}
}

/* if file exists send it */
if(isset($_POST['task']) && $_POST['task'] == "download")
{
	// check if link has expired
	if((time() - $_GET['time']) > 240)
	{
		header("Location: " . $config['site_url'] . "/files/" . $file['file_id'] . "/" . $file['file_name'] . ".html");
		exit();
	}
	
	// check wait time
	if((time() - $_GET['time']) < $wait)
	{
		header("Location: " . $config['site_url'] . "/files/" . $file['file_id'] . "/" . $file['file_name'] . ".html");
		exit();
	}
	
	// check free user download limit
	if($user->user_info['premium'] == 0 && $user->user_info['premium_active'] == 0)
	{
		// check download sessions
		if($file['file_size'] > 1048576)
		{
		    $num = sql_num_rows("SELECT * FROM download_sessions WHERE download_ip = '".$_SERVER['REMOTE_ADDR']."'");
		    if($num > $config['free_down_limit'])
		    {
				$is_error = 1;
				$error_message = "Max " . $config['free_down_limit'] . " files per hour for free users.";
		    }
		}
	}
	
	// check if file exists
	if(!file_exists($config['upload_path'] . $file['file_name'])){ header("Location: ./"); exit(); }

	// serve file
	if($is_error != 1)
	{
        $dl = new httpdownload;
	    $dl->filename = "[".$config['site_name']."]" . $file['file_name'];
	    $dl->speed = $download_rate;
	    $dl->use_resume = $resume;
	    $dl->set_byfile($local_file);
	    $dlsize = $dl->download();
	
	    // update file last access, downloads etc
	    mysql_query("UPDATE uploads SET downloads = downloads+1,
				                        last_access = '".time()."' WHERE file_id = '".$file['file_id']."'");
	
	    // insert download session in to database
	    mysql_query("INSERT INTO download_sessions (download_ip, 
												    download_start
												    ) VALUES (
												    '".$_SERVER['REMOTE_ADDR']."',
												    '".time()."')");
	}
}

/* assign template vars */
$tpl->premium = $premium;
$tpl->is_error = $is_error;
$tpl->error_message = $error_message;

/* include footer */
include("footer.php");

?>