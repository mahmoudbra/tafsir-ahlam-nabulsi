<?php

// define admin constant
define("_ADMIN", true);

// include header
include("header.php");

// set page name
$page = "admin";

/* check for valid admin login */
$user->check_admin_login();

// get management section
$man = isset($_GET['m']) ? $_GET['m'] : "main";

// get task
$task = isset($_GET['task']) ? $_GET['task'] : "main";

// reset error vars
$is_error = 0;
$message = "";

/* switch management */
switch($man)
{
	// manage members
	case "users":
	
	    // switch task
	    switch($task)
		{
		    case "delete":
			    
				// build query
			    $q = "DELETE FROM members WHERE user_id = '".$_GET['u']."' AND user_id != '1' LIMIT 1";
			
			    // execure query
			    mysql_query($q);
			
			    // redirect
			    header("Location: ./adm.php?m=users&success=1");
				
			    // exit
			    exit();
				
			break;
			
			case "edit":
			
			    // build query
			    $q = "SELECT * FROM members WHERE user_id = '".$_GET['u']."' LIMIT 1";
			
			    // execure query
			    $r = mysql_query($q);
			
			    // get user info
			    $u = mysql_fetch_assoc($r);
				
				// edit user
				if(isset($_POST['task']) && $_POST['task'] == "doedit")
				{
					// check for empty fields
					$required_array = array("user_name", "user_email");
					foreach($_POST as $key => $val)
					{
					    if(in_array($key, $required_array) && empty($val))
						{
							header("Location: ./adm.php?m=users&task=edit&u=".$_POST['u']."&error=1");
							exit();
						}
					}
					
					// collect values
					$username = $_POST['user_name'];
					$user_email = $_POST['user_email'];
					$user_password = isset($_POST['user_password']) ? sha1($_POST['user_password']) : NULL;
					
					// begin building query
					$query = "UPDATE members SET user_name = '{$username}',
					                             user_email = '{$user_email}',
												 user_password = '{$user_password}'";

												 
					if ($user_password == "da39a3ee5e6b4b0d3255bfef95601890afd80709")
					{
                                           // begin building query
					   $query = "UPDATE members SET user_name = '{$username}',
					                             user_email = '{$user_email}'";

                                        } else {
                                          // begin building query
					  $query = "UPDATE members SET user_name = '{$username}',
					                             user_email = '{$user_email}',
												 user_password = '{$user_password}'";

                                        }
												 

					
					
					// query where
					$query .= " WHERE user_id = '".$_POST['u']."'";
					
					// run querry
					mysql_query($query) or die(mysql_error());
					
					// redirect success
					header("Location: ./adm.php?m=users&task=edit&u=".$_POST['u']."&success=1");
					
					// exit
					exit();
				}
			
			    // set template vars
			    $tpl->task = "edit_member";
			    $tpl->u = $u;
				
			break;
			
			default:
			
			    // query for all members
				$q = "SELECT user_id, user_email, user_name, user_signup FROM members ORDER BY user_id DESC";
				$r = mysql_query($q);
		        $num = mysql_num_rows($r);
		
		
		        // set template vars
		        $tpl->task = "users";
		        $tpl->result = $r;
		        $tpl->total = $num;
		}
		
	break;
	
	// manage files
	case "files":
	    
		// query for all files
		$q = "SELECT file_name, file_size, upload_date, downloads, file_id, delete_id, upload_owner FROM uploads ORDER BY id DESC";
		$r = mysql_query($q);
		$num = mysql_num_rows($r);
		
		// set template vars
		$tpl->task = "files";
		$tpl->result = $r;
		$tpl->total = $num;
		
		// delete files
		if(isset($_GET['task']) && $_GET['task'] == "deletemultiple")
		{
			// get file id
		    //$files_id = $_POST['multicheckbox'];

                    foreach ($_POST['multicheckbox'] as $files_chkbox=>$val)
                    {

		    // get file details
		    $file = sql_row("SELECT file_name, file_id, upload_id FROM uploads WHERE file_id = '".$val."' LIMIT 1");
		
		    // delete from database
	            mysql_query("DELETE FROM uploads WHERE file_id = '".$file['file_id']."'");
	            mysql_query("DELETE FROM file WHERE uid = '".$file['file_id']."'");
	            mysql_query("DELETE FROM mirror WHERE uid = '".$file['file_id']."'");
                    mysql_query("DELETE FROM mirror WHERE url = ''NULL");

		
		    // delete from uploads folder
		    @unlink($config['upload_path'] . $file['upload_id']);
			
			// log action
		    admin_log("Files Delete", "Admin deleted files: " . $file['file_name']);
		    
                    }
		    // redirect
			header("Location: ./adm.php?m=files&success=1");
			
			// exit
			exit();
		}


		// delete files
		if(isset($_GET['task']) && $_GET['task'] == "delete")
		{
			// get file id
		    $file_id = $_GET['f'];

		    // get file details
		    $file = sql_row("SELECT file_name, file_id, upload_id FROM uploads WHERE file_id = '".$file_id."' LIMIT 1");
		
		    // delete from database
	            mysql_query("DELETE FROM uploads WHERE file_id = '".$file['file_id']."'");
	            mysql_query("DELETE FROM file WHERE uid = '".$file['file_id']."'");
	            mysql_query("DELETE FROM mirror WHERE uid = '".$file['file_id']."'");
                    mysql_query("DELETE FROM mirror WHERE url = ''NULL");

		
		    // delete from uploads folder
		    @unlink($config['upload_path'] . $file['upload_id']);
			
			// log action
			admin_log("File Delete", "Admin deleted file: " . $file['file_name']);
		
		    // redirect
			header("Location: ./adm.php?m=files&success=1");
			
			// exit
			exit();
		}
		
	break;
	
	// manage siute logs
	case "site_logs":
	
	    // query for all logs
		$q = "SELECT id, action, action_date, action_text FROM site_logs ORDER BY id DESC";
		$r = mysql_query($q);
		$num = mysql_num_rows($r);
		
		// clear admin logs
		if(isset($_POST['task']) && $_POST['task'] == "clearLogs")
		{
		    // delete logs from database
			mysql_query("TRUNCATE site_logs");
			
			// log action
			admin_log("Logs Clear", "Admin cleared site logs");
			
			// redirect
			header("Location: ./adm.php?m=site_logs");
			
			// exit
			exit();
		}
		
		// set template vars
		$tpl->task = "site_logs";
		$tpl->result = $r;
		$tpl->total = $num;
	
	break;
	
	// manage site docs
	case "site_docs":
	    
		// get edit page
		$p = isset($_GET['p']) ? $_GET['p'] : "about";
		
	    // query for all docs
		$q = "SELECT id, page_name, page_content FROM site_docs WHERE page_name = '".$p."' LIMIT 1";
		$r = mysql_fetch_assoc(mysql_query($q));
		
		// update site doc
		if(isset($_POST['task']) && $_POST['task'] == "save")
		{
			// save to database
		    mysql_query("UPDATE site_docs SET page_content = '".$_POST['wysiwyg']."' WHERE page_name = '".$_POST['page']."'");
			
			// log action
			admin_log("Doc Update", "Admin updated site document: " . $_POST['page']);
			
			// redirect
			header("Location: ./adm.php?m=site_docs&p=".$_POST['page']);
			
			// exit
			exit();
		}
		
		// set template vars
		$tpl->task = "site_docs";
		$tpl->p = $p;
		$tpl->result = $r;
	
	break;
	
	// manage site settings
	case "site_settings":
		
	    // query for all settings
		$q = "SELECT * FROM site_settings LIMIT 1";
		$r = mysql_fetch_assoc(mysql_query($q));
		
		// update site settings
		if(isset($_GET['task']) && $_GET['task'] == "doEdit")
		{
			// check for empty fields
			foreach($_POST as $key => $val)
			{
			    if(empty($_POST[$key]))
				{
					$tpl->is_error = 1;
					$tpl->message = "Please fill out all the required fields";
					$is_error = 1;
				}
			}
			
			if($is_error != 1)
			{
				// build query
				foreach($_POST as $key => $val)
				{
					$query = "UPDATE site_settings SET ";
				    $query .= "$key = '".mysql_real_escape_string($val)."'";
					mysql_query($query) or die(mysql_error());
				}
				
				// log action
			    admin_log("Settings Update", "Admin updated site settings");
				
				// redirect
				header("Location: ./adm.php?m=site_settings&result=success");
				exit();
			}
			
		}
		
		// set template vars
		$tpl->task = "site_settings";
		$tpl->result = $r;
	
	break;
	
	// premium packages
	case "premium_packages":
	
	    // query for all files
		$q = "SELECT package_id, package_name, package_price, package_length FROM packages";
		$r = mysql_query($q);
		$num = mysql_num_rows($r);
		
		// set template vars
		$tpl->task = "premium_packages";
		$tpl->result = $r;
		$tpl->total = $num;
		
		// delete plan
		if(isset($_GET['task']) && $_GET['task'] == "delete")
		{
			// remove from database
			mysql_query("DELETE FROM packages WHERE package_id = '".$_GET['p']."'");
			
			// log action
			admin_log("Plan Delete", "Admin deleted premium plan: " . $_GET['p']);
			
			// redirect
			header("Location: ./adm.php?m=premium_packages&success=1");
			
			// exit
			exit();
		}
		
		// edit
		if(isset($_GET['task']) && $_GET['task'] == "edit")
		{
			// get package info
			$p = sql_row("SELECT * FROM packages WHERE package_id = '".$_GET['p']."' LIMIT 1");
			
			// edit package
			if(isset($_POST['task']) && $_POST['task'] == "doedit")
			{
				// check for empty fields
				if(empty($_POST['package_name'])||empty($_POST['package_price'])||empty($_POST['package_length']))
				{
				    // set error
					$tpl->is_error = 1;
					$tpl->message = "Please fill out all the required fields";
					$is_error = 1;
				}
				
				// no error
				if($is_error != 1)
				{
					// insert in to database
				    mysql_query("UPDATE packages SET package_name = '".$_POST['package_name']."',
												     package_price = '".$_POST['package_price']."',
												     package_length = '".$_POST['package_length']."'
													 WHERE package_id = '".$_POST['p']."'");
					
					// log action
					admin_log("Plan Edit", "Admin edited premium plan: " . $_POST['p']);
				
				    // success, redirect back to packages
				    header("Location: ./adm.php?m=premium_packages");
				}
			}
			
		    // set template vars
			$tpl->task = "edit_premium_package";
			$tpl->p = $p;
		}
		
		// add
		if(isset($_GET['task']) && $_GET['task'] == "add")
		{
			// add package
			if(isset($_POST['task']) && $_POST['task'] == "doadd")
			{
				// check for empty fields
				if(empty($_POST['package_name'])||empty($_POST['package_price'])||empty($_POST['package_length']))
				{
				    // set error
					$tpl->is_error = 1;
					$tpl->message = "Please fill out all the required fields";
					$is_error = 1;
				}
				
				// no error
				if($is_error != 1)
				{
					// insert in to database
				    mysql_query("INSERT INTO packages (package_name,
												       package_price,
												       package_length
												       ) VALUES (
												       '".$_POST['package_name']."',
												       '".$_POST['package_price']."',
												       '".$_POST['package_length']."')");
					
					// log action
					admin_log("Plan Add", "Admin added premium plan: " . $_POST['package_name']);
				
				    // success, redirect back to packages
				    header("Location: ./adm.php?m=premium_packages");
				}
			}
			
		    // set template vars
			$tpl->task = "add_premium_package";
		}
	
	break;
	
	// download file
	case "download":
	    
		/* get file info based on vars passed */
		$file = sql_row("SELECT * FROM uploads WHERE file_id = '".$_GET['file_id']."' LIMIT 1");
		
		/* file that should be send to the client */
		$local_file = $config['upload_path'] . $file['upload_id'];
		
		/* set download rate */
		$download_rate = 800.0;
		
		/* download file */
		if(file_exists($config['upload_path'] . $file['upload_id']))
		{
			$dl = new httpdownload;
			$dl->filename = "[".$config['site_name']."]" . $file['file_name'];
			$dl->speed = $download_rate;
			$dl->use_resume = $resume;
			$dl->set_byfile($local_file);
			$dlsize = $dl->download();
		}
		
	break;
	
    // default view
	default:
	
	    // get total members
		$total_members = sql_row("SELECT count(user_id) AS total FROM members");
		
		// get total files
		$total_files = sql_row("SELECT count(id) AS total FROM uploads");
		
		// get total uploads size
		$total_space = sql_row("SELECT SUM(file_size) AS total_space FROM uploads");
	
	    // set template vars
		$tpl->total_members = $total_members['total'];
		$tpl->total_files = $total_files['total'];
		$tpl->total_space = $total_space['total_space'];
	    $tpl->task = "main";
}

// include footer
include("footer.php");

?>