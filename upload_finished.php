<?php

/* include commons file */
include("include/common.php");

/* initialize upload id */
$UPLOAD_ID = '';

/* require upload finished lib */
require_once("./include/uploader_finished_lib.php");

/* set headers */
header('Content-type: text/html; charset=UTF-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

/* set upload ID or throw error */
if(isset($_GET['upload_id']) && preg_match("/^[a-zA-Z0-9]{32}$/", $_GET['upload_id'])){ $UPLOAD_ID = $_GET['upload_id']; }else{ kak("<span class='error'>Invalid parameters passed</span>", 1, __LINE__); }

/* set arrays */
$_XML_DATA = array();
$_CONFIG_DATA = array();
$_POST_DATA = array();
$_FILE_DATA = array();
$_FILE_DATA_TABLE = '';
$_FILE_DATA_EMAIL = '';

/* new XML parser */
$xml_parser = new XML_Parser;

/* set upload_id.redirect file */
$xml_parser->setXMLFile($TEMP_DIR, $_GET['upload_id']);

/* delete upload_id.redirect file when finished parsing */
$xml_parser->setXMLFileDelete($_INI['delete_redirect_file']);

/* parse upload_id.redirect file */
$xml_parser->parseFeed();

/* display message if the XML parser encountered an error */
if($xml_parser->getError()){ kak($xml_parser->getErrorMsg(), 1, __LINE__); }

/* get xml data from the xml parser */
$_XML_DATA = $xml_parser->getXMLData();

/* get data from xml */
$_CONFIG_DATA = getConfigData($_XML_DATA);
$_POST_DATA  = getPostData($_XML_DATA);
$_FILE_DATA = getFileData($_XML_DATA);

/* get upload owner */
$upload_owner = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0';

$emailMsg = "Here are your file links: \n\n";

/* handle files */
for($i = 0; $i < count($_FILE_DATA); $i++)
{
	// set rand file id
    $file_id = randomStr(4, 8) . time();

    // set delete id
    $del_id = randomStr(6, 8) . time();
	
    // insert file in to database


$file_name = $_FILE_DATA[$i]->getFileInfo('name');
$file_size = $_FILE_DATA[0]->size;
$file_ip = $_SERVER['REMOTE_ADDR'];

    mysql_query("INSERT INTO uploads (file_id, 
								      delete_id,
								      file_name,
								      file_type,
								      file_size,
								      uploader_ip,
								      upload_date,
								      downloads,
								      upload_owner,
									  upload_id
								      ) VALUES (
								      '".$file_id."',
								      '".$del_id."',
								      '".$file_name."',
								      '".$_FILE_DATA[$i]->getFileInfo('type')."',
								      '".$_FILE_DATA[$i]->getFileInfo('size')."',
								      '".$file_ip."',
								      '".time()."',
								      '0',
								      '".$upload_owner."',
									  '".$UPLOAD_ID."')");


mysql_query("INSERT INTO file (uid, name, size, ip) VALUES ('$file_id', '$file_name', '$file_size', '$file_ip')") or die(mysql_error());

    // display upload results on page
    print getFormattedUploadResults($_FILE_DATA[$i]->getFileInfo('name'), $file_id, $del_id, $tiny_url);
}

  require 'includes/configs.inc.php';
  require 'includes/mconfig.php';
  $status = 0;
  
  for($i=0;$i<count($mrr_1);$i++)
  { 
  $mrr=strtolower($mcon[$mrr_1[$i]][1]);
  $mrr_r=$mrr.'remote';
  $mrr_p=$mcon[$mrr_1[$i]][2];
  
  	if ((isset ($_POST_DATA[$mrr]) OR isset ($_POST[$mrr_r])))
    {
    exec ('' . $php_path . ' -c ' . $config_ini . ' ' . $mrr_p .' ' . $file_id . ' ' . $status . ' >/dev/null &');
    }
  
  }

?>