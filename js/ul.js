$(document).ready(function(){
	
	update_filelist=function(){
		var obj=new Object();
		obj.m="list";
		obj.id=get_id();
		$.ajax({
			type: 'POST',
			url: "./index.php",
			data: obj,
			success: function(data){
				$("div.flist").html(data);
				$("div.flist > div").mouseover(function(){
					$(this).children("div.actions").show();
					$(this).children("div.filename").addClass("filename_hover");
				});
				$("div.flist > div").mouseout(function(){
					$(this).children("div.actions").hide();
					$(this).children("div.filename").removeClass("filename_hover");
				});
				$("div.flist > div a.dl").click(function(){
					var id=get_id(),
						fname=$(this).parent().parent().parent().children("div.filename").text(),
						url="./index.php?m=download&id="+id+"&f="+fname,
						win=window.open(url, '_blank');
					win.focus();
				});
				$("div.flist > div a.del").click(function(){
					var fname=$(this).parent().parent().parent().children("div.filename").text(),
						chk=confirm("Soll die Datei "+fname+" wirklich gelöscht werden?");
					if (chk){
						var obj=new Object();
						obj.m="delete";
						obj.f=escape(fname);
						obj.id=get_id();
						$.ajax({
							type: 'POST',
							url: "./index.php",
							data: obj,
							success: function(data){
								update_filelist();
								update_size();
							}
						});
					}
				});
			}
			
		});
	}
	
	get_id=function(){
		return $("#uploader_id").html();
	}
	
	update_size=function(){
		var obj=new Object();
		obj.m="size";
		obj.id=get_id();
		$.ajax({
			type: 'POST',
			url: "./index.php",
			dataType: "json",
			data: obj,
			success: function(data){
				$("b.act_dirsize").html(data.cur);
				$("b.max_dirsize").html(data.max);
			}
		});
	}
	
	$('#file_upload').fileUploadUI({
		uploadTable: $('#files'),
		downloadTable: $('#files'),
		buildUploadRow: function (files, index) {
			return $('<tr><td>' + files[index].name + '<\/td>' +
					'<td class="file_upload_progress"><div><\/div><\/td>' +
					'<td class="file_upload_cancel">' +
					'<button class="ui-state-default ui-corner-all" title="Cancel">' +
					'<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
					'<\/button><\/td><\/tr>');
		},
		buildDownloadRow: function (file) {
			return $('<b></b>');
		},
		onComplete: function (event, files, index, xhr, handler) {
			handler.onCompleteAll(files);
		},
		onAbort: function (event, files, index, xhr, handler) {
			handler.removeNode(handler.uploadRow);
			handler.onCompleteAll(files);
		},
		onCompleteAll: function (files) {
			update_filelist();
			update_size();
			if (!files.uploadCounter) {
				files.uploadCounter = 1;  
			} else {
				files.uploadCounter = files.uploadCounter + 1;
			}
			if (files.uploadCounter === files.length) {
				$("#imgerr").hide(); 
				
			}
		},
		onError: function(event, files, index, xhr, handler) {
			alert(xhr.response);
		}
	});
	
	update_comments=function(){
		var obj=new Object();
		obj.m="get_comments";
		obj.id=get_id();
		$.ajax({
			type: 'POST',
			url: "./index.php",
			data: obj,
			success: function(data){
				$("div.comment_area").html(data);
			}
		});
	}
	
	$("#commentform").validate({
		rules: {
			name : { 
				required: true,
				minlength: 3
			},
			text: { 
				required: true,
				minlength: 4
			}
		},
		messages: {
			name:"Bitte Name angeben (mindestens 3 Zeichen)",
			text: "Der Kommentar sollte mindestens 4 Zeichen lang sein",
		}
	});
	post_comment=function(){
		var val=$("#commentform").valid();
		if (val){
			var obj=new Object();
			obj.m="new_comment";
			obj.id=get_id();
			obj.name=$("input.comment_name").val();
			obj.txt=$("textarea.comment_txt").val();
			$.ajax({
				type: 'POST',
				url: "./index.php",
				data: obj,
				success: function(data){
					update_comments();
					$("input.comment_name").val("");
					$("textarea.comment_txt").val("");
				}
			});
		}
	}
	
	$("a.send_comment").click(function(){
		post_comment()
	});
	$("#commentform").submit(function(){
		post_comment();
		return false;
	});
	
	update_filelist();
	update_size();
	update_comments();
	
	window.setInterval(15000,update_filelist);
	window.setInterval(15000,update_size);
	window.setInterval(15000,update_comments);
	
});
