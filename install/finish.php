<?php

$dir = 'tmp/';
if($dh = opendir($dir)){
	while(($file = readdir($dh)) !== false){
		if(file_exists($dir . $file)) {
			@unlink($dir . $file);
		}
	}
	
	closedir($dh);
}

$db = new PDO('sqlite:../db/twitter.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = $db->prepare('UPDATE user SET real_name=? WHERE screen_name=?');
$query->execute(array(
	$_GET['realname'],
	$_GET['username']
));

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Twttr-to-db: setup</title>
		<?php include_once("../include/header.php"); ?>
	</head>
	<body>
		<?php include_once("../include/navbar.php"); ?>
		<h1>Completed the Twitter <small>database backup</small></h1>
		<ul class="breadcrumb">
			<li><a href="#">Installation</a> <span class="divider">/</span></li>
			<li><a href="#">Fill information</a> <span class="divider">/</span></li>
			<li><a href="#">Processing</a> <span class="divider">/</span></li>
			<li class="active">Finalization</li>
		</ul>
		<div class="content">
			<div class="hero-unit">
				<h1>Well, we're done here!</h1>
				<p>Enjoy your freshly installed Twitter backup.</p>
			</div>
		</div>
	</body>
</html>