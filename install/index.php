<!DOCTYPE html>
<html>
	<head>
		<title>Twttr-to-db: setup</title>
		<?php include_once("../include/header.php"); ?>
	</head>
	<body>
		<?php include_once("../include/navbar.php"); ?>
		<h1>Fill the information <small>of your account</small></h1>
		<ul class="breadcrumb">
			<li><a href="#">Installation</a> <span class="divider">/</span></li>
			<li class="active">Fill information</li>
		</ul>
		<div class="content">
			<div class="row">
				<div class="span12">
					<div class="alert">
						<strong>Tip!</strong> If you have a /lot/ of photos uploaded on pic.twitter.com, your archive may be really big, so you might want to take them out of the archive before uploading it.
					</div>
					<form class="form-horizontal" action="process.php" method="POST" enctype="multipart/form-data">
						<div class="control-group">
							<label class="control-label" for="username">Username</label>
							<div class="controls">
								<div class="input-prepend">
							   		<span class="add-on">@</span>
							   		<input class="span3" type="text" name="username" placeholder="outadoc">
							   </div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="realname">Real Name</label>
							<div class="controls">
								<input class="span3" type="text" name="realname" placeholder="Chuck Norris">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="archive">Archive file, sent to you by Twitter</label>
							<div class="controls">
								<input class="span3" type="file" accept="application/zip" name="archive">
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<input class="btn btn-primary" type="submit" value="Next step" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>