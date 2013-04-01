<?php
/***********************************************************************
 * 
 * eni's template-class
 * based on a class by Eric Sizemore (Simple Template Engine 1.0.2)
 * 
 * (c) 2009 Cyrill von Wattenwyl, e23.ch
 * License: GNU GPL
 * 
 *  examples:
 * 
 **********************************************************************/

//dirrrrrrrrty
function __($k){return("");}

class template {
	
		var $tpl_vars;
		
		
		/***************************************
 		* Constructor
		* @return 	none
 		***************************************/
		
		function __construct($dir=""){
			$this->dir=$dir;
			$this->tp_file = false;
			$this->tpl_vars = array();
		}
		
		
		/***************************************
 		* set template file for usage style 2
		* @return 	none
 		***************************************/
		
		function set_file($file){
				$this->tp_file=$file;
		}
		
		
		/***************************************
 		* clean all vars for style2
		* @return 	none
 		***************************************/
		
		function clean_tpl_vars(){
				unset($this->tpl_vars);
				$this->tpl_vars=array();
		}
		
			
		/***************************************
 		* auto_assign all vars
		* @param	file	string	the template-file
		* @param	string	bool	$file is a string
		* @return 	none
 		***************************************/
	
		function auto_assign($file=false,$string=false){
				if (!$file){ $file=$this->tp_file; }
				$file=$this->dir.$file;
				if($string){
					$content=$file;
				} else {
					if (!is_file($file))
						return false;
					$content=@file_get_contents($file);
					$file=$this->tp_file;
				}
				preg_match_all("/\{([a-zA-Z0-9_-]*)\}/U",$content,$res_arr,PREG_PATTERN_ORDER);
				$assign = array();
				foreach ($res_arr[1] as $item){
						$text = __($item);
						if (!empty($text))
							$assign[$item] = $text;
				}
				$this->assign($assign);
		}
		
		
		/***************************************
 		* assign {id} with $_REQUEST['id']
		* @return 	none
 		***************************************/

		function assign_id(){
				$this->assign( array( "id"=>mysql_escape_string( $_REQUEST["id"] ) ) );
		}


		/***************************************
 		* Assign our variables and replacements
		* @param  array  Template variables and replacements
		* @return none
 		***************************************/

		function assign($var_array){
				if (!is_array($var_array)){
					return;
				}
				$this->tpl_vars = array_merge($this->tpl_vars, $var_array);
		}


		/**************************************
		* Parse the template file
		* @param  tpl_file  string  Template file or tpl-string
		* @param  no_file   bool 	
		* @param  php       bool 	parse template as php-file
		* @return 			string 	Parsed template data
		**************************************/
					
		function parse($tpl_file=false,$no_file=false,$php=false){
				if (!$tpl_file){ $tpl_file=$this->tp_file; }
				if (!$no_file) {
						if (!is_file($tpl_file)){
							return false;
						}
						if ($php){
							$tpl_content=$this->ob_include($tpl_file);
						} else {
							$tpl_content = file_get_contents($tpl_file);
						}
				} else { 
						$tpl_content=$tpl_file;
				}
				foreach ($this->tpl_vars AS $var => $content){
						$tpl_content = str_replace('{' . $var . '}', $content, $tpl_content);
				}
				return $tpl_content;
		}


		/***************************************
 		* Output the template
		* @param string Template file
 		***************************************/

		function display($tpl_file){
			echo $this->parse($tpl_file);
		}
		
		
		/***************************************
 		* shortcut-function for style1
		* @param   file		string	the template-file
		* @param   array	array	associative array with extra-items
		* @param   nofile	bool 	$file is a string
		* @param   php		parse 	template as php-file
		* @return  			string  Parsed template data
 		***************************************/

		function go($file,$array=false,$nofile=false,$php=false){
				if (is_array($array)){
						$this->assign($array);
				}
				if ($nofile){
						$this->auto_assign($file,true);
						return $this->parse($file,true);
				} else {
						$this->set_file($file);
						$this->auto_assign();
						return $this->parse();
				}
		}
		
		
		/***************************************
 		* parse php-file
		* @param  file  string  the php-string
		* @return string  		the parsed string
 		***************************************/

		function ob_include($file){
				ob_start();
				include($file);
				$res=ob_get_contents();
				ob_end_clean();
				return $res;		
		}
	
}

?>
