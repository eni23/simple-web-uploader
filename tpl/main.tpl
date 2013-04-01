<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/2000/REC-xhtml1-200000126/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"> 
	<head>
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic' rel='stylesheet' type='text/css'>
		<style>
		</style>
		<link rel="stylesheet" href="{url}css/style.css" />		
		<link rel="stylesheet" href="{url}css/mime.css" />
		<script src="{url}js/jquery-fileupload.js"></script>
		<script src="{url}js/jquery-fileupload.ui.js"></script>
		<script src="{url}js/jquery.validate.min.js"></script>
		<script src="{url}js/ul.js"></script>
	</head>
	<body>
	
		<div class="head">				
			<div>
				<span class="title">{uploader_title}</span><br />
				{uploader_description}<br /><br />
				<small>
					Aktuelle Gr&ouml;sse: <b class="act_dirsize">0</b><br />
					Maximale Gr&ouml;sse: <b class="max_dirsize">0</b><br />						
					Erlaubte Dateitypen: <b>{allowed_types}</b><br />
				</small>
			</div>
		</div>
		
		<div class="uploadarea">
			<form id="file_upload" action="" method="POST" enctype="multipart/form-data">
				<input type="file" name="file" multiple />
				<input type="hidden" name="m" value="upload" />
				<input type="hidden" name="id" value="{uploader_id}" id="frm_id" />
				<button>Upload</button>
				<div>
					<b>Upload:</b> Dateien hier hineinziehen oder hier klicken
				</div>
			</form>
		</div>							

		<div class="site">
			<table id="files"></table>
			<div id="img_order"></div>
			<div class="flist"></div>
			<!--
			<br />
			<a href="#">Alle Dateien als Zip-Archiv herunterladen</a><br /><br />
			!-->
			<br /><br />
			<span class="tt">Kommentare</span><br />
			<br />
			<b>Neuer Kommentar</b>
			<form id="commentform">
				<table>
					<tr>
						<td>Name</td>
						<td><input type="text" class="comment_name" name="name" /></td>
					</tr>
					<tr>
						<td>Kommentar</td>
						<td><textarea class="comment_txt" name="text"></textarea></td>
					</tr>
				</table>
			</form>
			<a href="javascript:void(0);" class="send_comment">Kommentar senden</a>
			<div class="comment_area"></div>
		</div>
		<div class="hidden" id="uploader_id">{uploader_id}</div>
	</body>
</html>
