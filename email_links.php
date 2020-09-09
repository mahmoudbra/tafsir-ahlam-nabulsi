<?php

/* include header */
include("header.php");

/* set page name */
$page = "email_links";

send_generic($_POST['link_email'], $config['admin_email'], "Upload File Links", $_POST['file_msg']) ;

/* set template vars */
$tpl->sent = 1;

/* include footer */
include("footer.php");

?>