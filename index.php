<?php

include("./php/functions.php");
include("./php/template.class.php");


session_start();

//load configs and check for id
$globalconf=get_globalconf();
$id=get_id();
$gc=get_globalconf();
$dir=$globalconf["DATADIR"]."/".$id;
$url=get_baseurl();
if (file_exists($dir)){
	$item=get_localconf($dir,$id);
} else {
	die("Error: Uploader-ID not found");
}

//if not logged in try to login
if ($_SESSION[$id]['logged_id'] != true){
	$login=false;
	$ec="";
	if ($pw=$_POST["password"]){
		if ($pw==$item['PASSWORD']){
			$_SESSION[$id]['logged_id']=true;
		} else {
			$ec=" error";
		}
	} 
	if ($_SESSION[$id]['logged_id']!=true){
		$tpl=new template;
		$arr=array( "url"=>$url,
					"error_class"=>$ec,
					"uploader_id"=>$id);
		echo $tpl->go("./tpl/login.tpl",$arr);
		die;
	}
}


$name=$item["NAME"];
$desc=$item["DESC"];
$max_size=$item["MAXSIZE"];
$max_size=filesize2bites($max_size);

header("Content-Type: text/html; charset=utf-8");
switch ($_REQUEST['m']){
	
	case "list":
		echo filelist($dir);
		break;
		
	case "download":
		$fname=basename($_REQUEST['f']);
		download($dir,$fname,true);
		break;
		
	case "upload":
		$file=$_FILES['file'];
		if ($file){
			$error=false;
			$cur_size=dirsize($dir);
			if (($cur_size+$file['size'])>$max_size){
				$error="Not enough Space on Uploader";
			}
			$ext=file_extension($file['name']);
			if ($item['TYPES']!="*"){
				$ok=false;
				$allowed=explode(",",$item['TYPES']);
				foreach ($allowed as $al){
					if ($ext==$al){
						$ok=true;	
					}
				}
				if (!$ok){
					$error="File Extension not allowed";
				}
			}
			if (!$error){
				$newfile="{$dir}/".urldecode($file['name']);
				$i=0;
				while(file_exists($newfile)){
					$newfile="{$dir}/".urldecode($file['name']);
					$no_ext=substr($newfile,0,strlen($newfile)-(strlen($ext)+1));
					$newfile=$no_ext."_{$i}.{$ext}";
					$i++;
				}
				move_uploaded_file($file['tmp_name'],$newfile);	
				$arr=array("name"=>$file['name'],"type"=>$file['type'],"size"=>$file['size']);
				echo json_encode($arr);
			} else {
				echo $error;
			}
		} else {
			echo "No File";
		}
		break;
		
	case "delete":
		$fname=basename(urldecode($_REQUEST['f']));
		unlink("{$dir}/{$fname}");
		echo "true";
		break;
		
	case "size":
		header("Content-Type: application/json", true);
		$ret['max']=bites2filesize($max_size);
		$c=dirsize($dir)-filesize($dir."/config")-filesize($dir."/comments");
		$ret['cur']=bites2filesize($c);
		echo json_encode($ret);
		break;
			
	case "logout":
		unset($_SESSION[$id]);
		break;
		
	case "get_comments":
		$cfile=$dir."/comments";
		if (!file_exists($cfile)){
			$content=serialize(array());
			file_put_contents($cfile,$content);
		}
		$comments=unserialize(file_get_contents($cfile));
		foreach ($comments as $comment){
			$date=date("d.m.Y H:i:s",$comment['ts']);
			$tpl=new template;
			$arr=array( "name"=>$comment['name'],
						"txt"=>$comment['txt'],
						"timestamp"=>$date);				
			echo $tpl->go("./tpl/comment.tpl",$arr);
		}
		break;
		
	case "new_comment":
		$cfile=$dir."/comments";
		$comments=unserialize(file_get_contents($cfile));
		$ts=time();
		$comment=array(	"name"=>$_REQUEST['name'],
						"txt"=>$_REQUEST['txt'],
						"ts"=>$ts);
		array_unshift($comments,$comment);
		$content=serialize($comments);
		file_put_contents($cfile,$content);
		echo "ok";
		break;
		
	
	default:
		$tpl=new template;
		$arr=array( "url"=>$url,
					"uploader_id"=>$id,
					"uploader_title"=>$name,
					"allowed_types"=>$item['TYPES'],
					"uploader_description"=>$desc);				
		echo $tpl->go("./tpl/main.tpl",$arr);
		break;
	
}
