<?php
require_once("engine/header.php");
$page_config = array ();
	$page_config['page_title'] = "Новостройки Петербурга на карте - установка newcity";
	$page_config['map_element'] = "mapsMain";
	$page_config['content_width'] = 880;
/*
mysql_query("CREATE TABLE IF NOT EXISTS `static_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coord` point NOT NULL,
  `name` varchar(31) NOT NULL,
  `desc` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
mysql_query("CREATE TABLE IF NOT EXISTS `dinamic_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coord` point NOT NULL,
  `name` varchar(31) NOT NULL,
  `desc` tinytext NOT NULL,
  `ptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `obj` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<head> 
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="style.css">
<title><?=$page_config['page_title'];?></title>
</head> 
<body id="top" class="page- ">
<div id="page"> 
<?php require_once ("engine/menu.php"); ?>
<section id="content-container">
	<div class="home-infobox">Отличные новости: 
		<a href="index.php" target="_self">Проект <b>Новостройки на карте Петербурга</b> возведен!
			<p><img src="images/install_logo.jpg"></img></p>Попробовать сейчас!</a></div>
	
</section> 
<?php require_once ("engine/footer.php"); ?>
</div> 
<div class="soc-fixed"> </div> 
</body> 
</html>