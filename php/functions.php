<?php

function filelist($dir){
	$fl="";
	$str=shell_exec("ls -1 \"{$dir}\"");
	$filelist=explode("\n",$str);
	foreach ($filelist as $file){
		if ($file=="config" or $file=="comments"){
			continue;
		}
		if ($file){
			$sfile=escapeshellarg("{$dir}/{$file}");
			$type=shell_exec("file -b --mime-type $sfile");
			$mime_tooltip=str_replace("\n","",shell_exec("file -b $sfile"));
			$type=str_replace(array("\n","/","+","."),array("","-","x","-"),$type);
			$ss=filesize("{$dir}/{$file}");
			$size=bites2filesize($ss);
			$tpl=new template;
			$arr=array( "size"=>$size,
						"filename"=>$file,
						"mime_css"=>"mime_$type",
						"mime_tooltip"=>$mime_tooltip);				
			$fl.=$tpl->go("./tpl/fileitem.tpl",$arr);
		}
	}
	return $fl;
}

function download($dir,$file,$dl_force=false){
	$type=shell_exec("file -b --mime-type \"{$dir}/{$file}\"");
	//$size=filesize("{$dir}/{$file}");
	header("Content-Type: $type");
	if ($dl_force){
		header('Content-Disposition: attachment; filename="'.$file.'"');	
	}
	readfile("{$dir}/{$file}");
	die;
}

//localconf: die if parse error
function get_globalconf(){
	if(!file_exists("./conf/global")){
		die("Error: File ./conf/global not found");
	}
	$c=parse_inifile("./conf/global");
	if (!file_exists($c["DATADIR"])){
		die("Error: DATADIR needs to be an existing Directory");
	}
	if (!is_writable($c["DATADIR"])){
		$who=`whoami`;
		die("Error: DATADIR is not writeable by $who");
	}
	return $c;
}

//localconf: fuzzy logic
function get_localconf($basedir,$id){
	$f=$basedir."/config";
	if (!file_exists($f)){
		die("Error: No config File Found for this Uploader");
	}
	$def=parse_inifile("./conf/default");	

	$c=parse_inifile($f);
	foreach ($def as $chk=>$def_val){
		if (!$c[$chk]){
			$c[$chk]=$def_val;
		}
	}
	return $c;
}

function get_id(){
	$id=$_REQUEST['id'];
	if (!$id){
		$s=str_replace(dirname($_SERVER['SCRIPT_NAME']),"",$_SERVER['REQUEST_URI']);
		$a=explode("/",$s);
		$id=$a[1];
		if (!$id){
			$id=$s;
		}
	}
	return basename($id);
}



function get_baseurl(){
	$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
	$url=$protocol."://".$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME'])."/";
	return $url;
}


function get_num(){
	if(!file_exists("./conf/num")){
		die("Error: File ./conf/num not found");
	}
	$c=str_replace("\n","",file_get_contents("./conf/num"));
	if(is_numeric($c)){
		return $c;
	} else {
		file_put_contents("0","./conf/num");
		return 0;
	}
}

function set_num($num){
	file_put_contents("./conf/num",$num);
}


function parse_inifile($inifile){
		$result = $matches = array();
		$a = &$result;
		$s = '\s*([[:alnum:]_\- \*]+?)\s*';
		preg_match_all('#^\s*((\['.$s.'\])|(("?)'.$s.'\\5\s*=\s*("?)(.*?)\\7))\s*(;[^\n]*?)?$#ms', @file_get_contents($inifile), $matches, PREG_SET_ORDER);
		if ($matches){
			foreach ($matches as $match){
					if (empty($match[2]))
					$a [$match[6]] = $match[8];
					else  $a = &$result [$match[3]];
			}
			return $result;
		} else return array();
}

function dirsize($directory) { 
    $size = 0; 
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){ 
        $size+=$file->getSize(); 
    } 
    return $size; 
} 

function file_extension($filepath) {
	preg_match('/[^?]*/', $filepath, $matches);
	$string = $matches[0];
	$pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
	if(count($pattern) == 1){
		return "";
	}
	if(count($pattern) > 1){			
		$filenamepart = $pattern[count($pattern)-1][0];
		preg_match('/[^?]*/', $filenamepart, $matches);
		return $matches[0];
	}
} 
    
function filesize2bites( $value ) {
    if ( is_numeric( $value ) ) {
        return $value;
    } else {
        $value_length = strlen( $value );
        $qty = substr( $value, 0, $value_length - 1 );
        $unit = strtolower( substr( $value, $value_length - 1 ) );
        switch ( $unit ) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
        }
        return $qty;
    }
}

function bites2filesize($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}

function dec2any( $num, $base=62, $index=false ) {
    if (! $base ) {
        $base = strlen( $index );
    } else if (! $index ) {
        $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,0 ,$base );
    }
    $out = "";
    for ( $t = floor( log10( $num ) / log10( $base ) ); $t >= 0; $t-- ) {
        $a = floor( $num / pow( $base, $t ) );
        $out = $out . substr( $index, $a, 1 );
        $num = $num - ( $a * pow( $base, $t ) );
    }
    return ($out) ? $out : "0";
}


function any2dec( $num, $base=62, $index=false ) {
    if (! $base ) {
        $base = strlen( $index );
    } else if (! $index ) {
        $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 0, $base );
    }
    $out = 0;
    $len = strlen( $num ) - 1;
    for ( $t = 0; $t <= $len; $t++ ) {
        $out = $out + strpos( $index, substr( $num, $t, 1 ) ) * pow( $base, $len - $t );
    }
    return $out;
}
