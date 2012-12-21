<?php
/*
*
* Name: compress.php 
*
* Purpose: 
*	Allow Apache server to directly serve pre-gzipped and minified JS and CSS files
*	with no server overhead for deflating or gzipping the file each time it is served.
*	This script combines, minifies and gzips Javascript and Cascading Style Sheet files
*	in batch mode. Each combined group of JS or CSS creates two files: 
*		Minified; Minified and Gzipped; base file names set by user
*
* Reason for using:
*	You want to reduce server overhead by eliminating server calls and decreasing the number
*		of bytes transmitted.
*	You are serving move than one JS or CSS file per page.
*	Your JS and CSS are not minified and/or not GZipped	
*	Your Host server does not support or implement mod_pagespeed
*	Your Host server does not support/implement by default gzip or deflate for js or css files
*	Online minify packages have performance issues on your server and increase server overhead
*
* Features:
*	Source files unchanged, output written to user specified directory reserved
*		for compressed/minified files 
*	Appends time stamp to user file names avoiding cache issues when file data changes. 
*	Creates a PHP file that provides the timestamp for the created files
*		so user written PHP code can easily generate correct html links.
*	Creates output directory and .htaccess file on initial execution 
* 		allowing Apache to correctly serve gzipped JS and CSS files
*		and cache the combined files for one week
*	Deletes outdated versions of combined files
*
* Usage:
*	Install module in www/dir where dir is a directory of your choice
*		PHP file compress_timestamp.php is written to this directory
*		directory can be at any level	      
*	Install CSSMIN into www/dir and optionally change settings	
*	Install JSMIN into www/dir and optionally change settings	
*	Set output directory name, default: ../min
*		should be at the same level as your CSS directory or background-url may need to change
*	code the combining output file names and source file names
*	execute the script (initial)
*	Change your source code to use generated files, sample shown below
*	execute the script when changes are made to the source CSS or JS files
*	(optional) setup Cron job on your host to run weekly catching changes
*		that may not have been compressed 
*	
* Sample PHP implementation code:
* 
* require_once('compress_timestamp.php');		//load timestamp created by compress.php module
*							//sets field $compress_stamp=unix_timestamp					
* if (stripos($_SERVER['HTTP_ACCEPT_ENCODING'],'GZIP')!==false)	
*	$gz='gz';
* else
*	$gz=null;
* echo '<link rel="stylesheet" type="text/css" href="min/css_schedule_'.$compress_stamp.'.css'.$gz.'" />',PHP_EOL;
* 
* //    the following scripts were combined into two groups named css_schedule and css_non_schedule
* //	echo '<link rel="stylesheet" type="text/css" href="CSS/menu.css" />',PHP_EOL;
* //	echo '<link rel="stylesheet" type="text/css" href="CSS/ThreeColumnFixed.css" />',PHP_EOL;
* //	echo '<link rel="stylesheet" type="text/css" href="CSS/sprite.css" />',PHP_EOL;
* //	echo '<link rel="stylesheet" type="text/css" href="CSS/iCal.css" />',PHP_EOL;
* 	
*/

$unix_timestamp = time();		//get unix time stamp added to all file names
$adir = $_SERVER['DOCUMENT_ROOT'].'/templates/zt_xenia25/min';			//directory to store output, must not be one of the source directories
require_once('jsmin.php');		//load javascript minifier	
require_once('cssmin.php');		//load css minifier	

//		Create target directory and write Apache htaccess file when no directory found 
if (!file_exists($adir))
	{
	mkdir($adir) && chmod($adir, 0777);
	$htaccess='Options All -Indexes'.PHP_EOL;
	$htaccess.='AddType text/css cssgz'.PHP_EOL;
	$htaccess.='AddType text/javascript jsgz'.PHP_EOL;
	$htaccess.='AddEncoding x-gzip .cssgz .jsgz'.PHP_EOL;
	$htaccess.='# for all files in min directory'.PHP_EOL;
	$htaccess.='FileETag None'.PHP_EOL;
	$htaccess.='# Cache for a week, attempt to always use local copy'.PHP_EOL; 
	$htaccess.='<IfModule mod_expires.c>'.PHP_EOL;
	$htaccess.='  ExpiresActive On'.PHP_EOL;
	$htaccess.='  ExpiresDefault A604800'.PHP_EOL;
	$htaccess.='</IfModule>'.PHP_EOL;
	$htaccess.='<IfModule mod_headers.c>'.PHP_EOL;
	$htaccess.='  Header unset ETag'.PHP_EOL;
	$htaccess.='  Header set Cache-Control "max-age=604800, public"'.PHP_EOL;
	$htaccess.='</IfModule>'.PHP_EOL;
	file_put_contents($adir.'/.htaccess',$htaccess);			//write initial htaccess file
	}

//		prepare combined, minfied and combined, minified and gzipped css and js files
file_compress('css_schedule.css',array($_SERVER['DOCUMENT_ROOT'].'/templates/zt_xenia25/css/template.css',$_SERVER['DOCUMENT_ROOT'].'/templates/zt_xenia25/css/default.css',$_SERVER['DOCUMENT_ROOT'].'/templates/zt_xenia25/zt_menus/zt_megamenu/zt.megamenu.css',$_SERVER['DOCUMENT_ROOT'].'/components/com_virtuemart/assets/css/vmsite-ltr.css'));
//file_compress('css_non_schedule.css',array($_SERVER['DOCUMENT_ROOT'].'/CSS/menu.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/ThreeColumnFixed.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/sprite.css'));
//file_compress('css_schedule_ie7.css',array($_SERVER['DOCUMENT_ROOT'].'/CSS/menuIE.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/ThreeColumnFixed.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/sprite.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/iCal.css'));
//file_compress('css_non_schedule_ie7.css',array($_SERVER['DOCUMENT_ROOT'].'/CSS/menuIE.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/ThreeColumnFixed.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/sprite.css'));
//file_compress('css_ie6.css',array($_SERVER['DOCUMENT_ROOT'].'/CSS/menuIE6.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/ThreeColumnFixedIE6.css',$_SERVER['DOCUMENT_ROOT'].'/CSS/sprite.css'));
file_compress('js_schedule.js',array($_SERVER['DOCUMENT_ROOT'].'/media/system/js/mootools-more.js',$_SERVER['DOCUMENT_ROOT'].'/media/system/js/mootools-core.js',$_SERVER['DOCUMENT_ROOT'].'/components/com_virtuemart/assets/js/facebox.js',$_SERVER['DOCUMENT_ROOT'].'/media/system/js/modal.js',$_SERVER['DOCUMENT_ROOT'].'/templates/zt_xenia25/js/zt.script.js',$_SERVER['DOCUMENT_ROOT'].'/components/com_virtuemart/assets/js/vmprices.js',$_SERVER['DOCUMENT_ROOT'].'/media/system/js/core.js',$_SERVER['DOCUMENT_ROOT'].'/templates/zt_xenia25/js/ajax_search.js',$_SERVER['DOCUMENT_ROOT'].'/templates/zt_xenia25/zt_menus/zt_megamenu/zt.megamenu.js',$_SERVER['DOCUMENT_ROOT'].'/components/com_virtuemart/assets/js/vmsite.js',$_SERVER['DOCUMENT_ROOT'].'/modules/mod_AutsonSlideShow/js/jquery-1.5.2.min.js',$_SERVER['DOCUMENT_ROOT'].'/modules/mod_AutsonSlideShow/js/jquery.skitter.min.js',$_SERVER['DOCUMENT_ROOT'].'/modules/mod_AutsonSlideShow/js/jquery.easing.1.3.js',$_SERVER['DOCUMENT_ROOT'].'/modules/mod_AutsonSlideShow/css/skitter.css'));
//file_compress('js_non_schedule.js',array($_SERVER['DOCUMENT_ROOT'].'/JS/Findit.js',$_SERVER['DOCUMENT_ROOT'].'/JS/UlScroll.js',$_SERVER['DOCUMENT_ROOT'].'/ExternalConnect/External.js'));

//		write new timestamp file compress_timestamp.php for php execution code
$infofile='<?php'.PHP_EOL;
$infofile.='$compress_stamp='.$unix_timestamp.';'.PHP_EOL;
$infofile.='?>'.PHP_EOL;
file_put_contents ('compress_timestamp.php',$infofile,LOCK_EX);		//file loaded by ThreeColTemplate1.php to get unique stamp id

//		5 second delay should take care of 99.9% of inflight requests
//			except on super overloaded servers or slow connections
sleep(5);	

//		delete older files in library
$unix_timestamp.='';		//make a string
if (file_exists($adir) && $dh = opendir($adir)) 
	{
	while($fn = readdir($dh)) if ($fn[0] != ".")
		{
		if (strpos($fn,$unix_timestamp)===false)
			{
			unlink($adir.'/'.$fn);
			echo $fn,' deleted<br>';
			}
		}	
	closedir($dh);
	}
else
	echo 'failed to open ',$adir,' ',date("r"),PHP_EOL;

function file_compress($file_name,$file_input) {
	global $unix_timestamp,$adir;
	$pos=strrpos($file_name,'.');				//get last . in file name
	if ($pos==false)
		die ('illogical response from strrpos');
	$fn=substr($file_name,0,$pos).'_'.$unix_timestamp.substr($file_name,$pos);	//put timestamp into file name
	$fl=null;						//clear file data variable
	foreach($file_input as $value)				//merge files in the group
		$fl.= file_get_contents($value).' ';
	$len_orig=strlen($fl);		
	if (strtolower(substr($file_name,$pos+1,2)) == 'js')	
//		$fl = preg_replace('/\t| {2,}/', '', $fl);
		$fl = JSMin::minify($fl);			//minify js	
	else
//		$fl = preg_replace('/\n\r|\r\n|\n|\r|\t| {2,}/', '', $fl);
		$fl = CssMin::minify($fl);			//minify css

	$len_minify=strlen($fl);
    	$gzdata=gzencode ($fl,9);				//gzip 

//		write files no need to lock, filename is unique and not yet in use
 	file_put_contents ($adir.'/'.$fn,$fl);			 	//put out minified, non gzipped version
   	file_put_contents ($adir.'/'.$fn.'gz',gzencode ($fl,9));	//put out gzipped version
	echo $adir.'/'.$fn, ' created length: ', $len_minify, ' ', $len_orig,'<br />'; 
	echo $adir.'/'.$fn.'gz', ' created length: ', strlen($gzdata), ' ', $len_minify, ' ', $len_orig,'<br />'; 
}
?>