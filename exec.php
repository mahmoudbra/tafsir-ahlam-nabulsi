<?
/*****************************************************
	Dynamic exec.php file Modified
	EXPANDABLE VERSION!
	1st Modified
******************************************************/

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
    exec ('' . $php_path . ' -c ' . $config_ini . ' ' . $mrr_p .' ' . $file_uid . ' ' . $status . ' >/dev/null &');
    }
  
  }

?>