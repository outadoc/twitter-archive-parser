<?php

$path = $_FILES['archive']['tmp_name'];
$zip = new ZipArchive;

if($zip->open($path) === true) {
    for($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        $fileinfo = pathinfo($filename);
        
        if(preg_match("#.txt$#", $fileinfo['basename'])) {
        	copy("zip://" . $path . "#" . $filename, "tmp/" . $fileinfo['basename']);
        }
    }
    $zip->close();
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Twttr-to-db: setup</title>
		<?php include_once("../include/header.php"); ?>
	</head>
	<body>
		<?php include_once("../include/navbar.php"); ?>
		<h1>Let us process <small>your data</small></h1>
		<ul class="breadcrumb">
			<li><a href="#">Installation</a> <span class="divider">/</span></li>
			<li><a href="#">Fill information</a> <span class="divider">/</span></li>
			<li class="active">Processing...</li>
		</ul>
		<div class="content">
			<div class="progress-win">
				<h1>Processing your data...</h1>
				<h2>Please wait, this operation will likely take a few minutes.</h2>
				<div class="progress progress-striped active">
					<div class="bar" id="parse-progress" style="width: 0%;"></div>
				</div>
				<p id="state-desc"></p>
				<a id="btn-next-step" class="btn btn-primary hidden" href="finish.php?username=<?php echo $_POST['username']; ?>&realname=<?php echo $_POST['realname']; ?>">Finish installation</a>
			</div>
		</div>
		<script type="text/javascript">
			
			var xhr = new XMLHttpRequest();
			
			xhr.onreadystatechange = function() {
				if(xhr.readyState == 4) {
					if(xhr.responseText.indexOf('error:') != -1) {
						var errorArray =  xhr.responseText.split(":");
						document.getElementById("state-desc").textContent = "Error: " + errorArray[1];
						document.getElementById("parse-progress").style.width = "0%";
					} else {
						document.getElementById("state-desc").textContent = "Done!";
						document.getElementById("parse-progress").style.width = "100%";
						document.getElementById("btn-next-step").className = "btn btn-primary";
					}
					
					clearInterval(interval);
				}
			}
			
			xhr.open("GET", "scripts/parse-to-db.php?username=<?php echo $_POST['username']; ?>");
			xhr.send(null);
			
			document.getElementById("state-desc").textContent = "Parsing to database...";
			document.getElementById("parse-progress").style.width = "10%";
			
			var interval = setInterval(function() {
				var percentage = document.getElementById("parse-progress").style.width.replace('%', '');
				document.getElementById("parse-progress").style.width = (parseInt(percentage)+1) + "%";
			}, 4500);
			
		</script>
	</body>
</html>