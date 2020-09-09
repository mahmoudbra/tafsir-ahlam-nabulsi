<?php

// include header
include("header.php");

// set page name
$page = "link_checker";

// reset script vars
$is_error = 0;
$result = 0;
$message = "";

// this function gets the contents via curl
function file_get_contents_curl($url) 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

// check links
if(isset($_POST['task']) && $_POST['task'] == "doCheck")
{
    // get urls
	$url_list = isset($_POST['urls']) ? $_POST['urls'] : '';
	
	// if empty
	if($url_list == '')
	{
		$is_error = 1;
		$message = "Please enter your links to check.";
	}
	
	// no error
	if($is_error != 1)
	{
		// clean urls
		$url_list = explode(" ", $url_list);
		$url_list = implode("\n", $url_list);
		$url_list = explode("\n", $url_list);
		$url_list = array_unique($url_list);
		
		// set result var
		$result = 1;
		
		// unset post
		unset($_POST);
	}
}

// assign template vars
$tpl->is_error = $is_error;
$tpl->result = $result;
$tpl->message = $message;
$tpl->url_list = $url_list;

// include footer
include("footer.php");

?>