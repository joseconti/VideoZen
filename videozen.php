<?php
/*
Plugin Name: VideoZen
Plugin URI: http://www.videozen.com
Description: VideoZen.
Version: 1.0.1
Author: José Conti
Author URI: http://www.joseconti.com
License: GPL2
*/

/*  Copyright 2012  http://www.wangguard.com (email : wangguard.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


//# VideoZen configuration starts here #
/*
 * To set VideoZen parameters via defines, set CONST_VIDEOZEN_USE_DEFINES to true and move these settings to the wp-config.php file
 */
/*
define("CONST_VIDEOZEN_USE_DEFINES" , false);
define("CONST_VIDEOZEN_API_KEY" , "123ag");	//ZenCoder API key
define("CONST_VIDEOZEN_BUCKET" , "amazons3");	//do not include the s3:// prefix
define("CONST_VIDEOZEN_S3WEB" , "www.yours3bucketurl.com");	//URL to access via web the Amazon S3 bucket, do not include the http:// prefix
define("CONST_VIDEOZEN_AUTH" , "31415926abc123ABC"");	//Secret passphrase between your site and zencoder, use a random letters and number string larger than 16 characters
define("CONST_VIDEOZEN_LANG" , "en|English\nes|Spanish");	// Available languages, each language should be formatted as "code|language name", MUST USE DOUBLE QUOTES, separate multiple languages using \n (carriage return), eg: "en|English\nes|Spanish"
*/

define("CONST_VIDEOZEN_DEF_WIDTH" , 640);	// Default video player width
define("CONST_VIDEOZEN_DEF_HEIGHT" , 390);	// Default video player height
//# VideoZen configuration ends here stop editing! #



define("CONST_ZENCODER_BASE_URL", "https://app.zencoder.com/api/v2");



define('VIDEOZEN_VERSION', '1.0.1');
define('VIDEOZEN_STAT_ENCODING', 'E');
define('VIDEOZEN_STAT_OK', 'K');
define('VIDEOZEN_STAT_PENDING', 'P');
define('VIDEOZEN_STAT_FAIL', 'F');



if (!defined('CONST_VIDEOZEN_USE_DEFINES'))
	define("CONST_VIDEOZEN_USE_DEFINES" , false);
	
if (!defined('CONST_VIDEOZEN_DEF_WIDTH'))
	define("CONST_VIDEOZEN_DEF_WIDTH" , 640);
	
if (!defined('CONST_VIDEOZEN_DEF_HEIGHT'))
	define("CONST_VIDEOZEN_DEF_HEIGHT" , 390);
	


$videozen_is_network_admin = is_network_admin();

if ( ! function_exists( 'is_plugin_active_for_network' ) ) 
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

$videozen_is_network_activated = is_plugin_active_for_network( 'videozen/videozen.php' );

$videozen_getOption =  'get_option';
$videozen_updateOption =  'update_option';
if ($videozen_is_network_activated) {
	$videozen_getOption =  'get_site_option';
	$videozen_updateOption =  'update_site_option';
}



$videozen_statustable = "videozen_cdnstatus";
$videozen_extensionesVideo = array(
	'avi',
	'wmv',
	'wma',
	'webm',
	'mpg',
	'mp4',
	'mpeg',
	'3gp',
	'3gp2', 
	'3g2', 
	'3gpp', 
	'3gpp2',
	'mov',
	'mkv',
	'flv',
	'ogg',
	'oga', 
	'ogv',
	'ogx',
	'divx',
	'asf',
	'aac'
);


// array for the output formats
$videozen_outputFormats = array();

//mp4 HD (1280x720) Q 4
$videozen_outputFormats['mp4HD'] = 
array(
	"label"=> "mp4HD",
	"format"=> "mp4",
	"quality"=> 4,
	"width"=> "1280",
	"height"=> "720",	
	"public"=> 1,
	"video-direct-link"=> false
);


//mp4 SD (640x480) Q 3
$videozen_outputFormats['mp4SD'] = 
array(
	"label"=> "mp4SD",
	"format"=> "mp4",
	"quality"=> 3,
	"width"=> "640",
	"height"=> "480",		
	"public"=> 1,
	"video-direct-link"=> false
);



//webm HD orig size Q 4
$videozen_outputFormats['webmHD'] = 
array(
	"label"=> "webmHD",
	"format"=> "webm",
	"quality"=> 4,
	"width"=> "1280",
	"height"=> "720",	
	"public"=> 1,
	"video-direct-link"=> false
);



//webm SD orig size Q 3
$videozen_outputFormats['webmSD'] = 
array(
	"label"=> "webmSD",
	"format"=> "webm",
	"quality"=> 3,
	"width"=> "640",
	"height"=> "480",		
	"public"=> 1,
	"video-direct-link"=> false
);



//ogg HD size Q 4
$videozen_outputFormats['oggHD'] = 
array(
	"label"=> "oggHD",
	"format"=> "ogg",
	"quality"=> 4,
	"width"=> "1280",
	"height"=> "720",	
	"public"=> 1,
	"video-direct-link"=> false
);



//ogg SD size Q 3
$videozen_outputFormats['oggSD'] = 
array(
	"label"=> "oggSD",
	"format"=> "ogg",
	"quality"=> 3,
	"width"=> "640",
	"height"=> "480",		
	"public"=> 1,
	"video-direct-link"=> false
);



//mp4 480x360 iphone Q 4
$videozen_outputFormats['iPhone'] = 
array(
	"label"=> "iPhone", 
	"format"=> "mp4",
	"quality"=> 4,
	"width"=> "480",
	"height"=> "360",		
	"public"=> 1,
	"video-direct-link"=> true
);


//3gp 320x240 old mobile Q 3
$videozen_outputFormats['Mobile'] = 
array(
	"label"=> "Mobile", 
	"format"=> "3gp",
	"quality"=> 3,
	"width"=> "320",
	"height"=> "240",
	"public"=> 1,
	"video-direct-link"=> true
);


//langs
$videozen_subs_langs = array();




/********************************************************************/
/*** INSTALL BEGINS ***/
/********************************************************************/
function videozen_admin_init() {
	global $videozen_updateOption , $videozen_getOption;
	
	$version = (float)$videozen_getOption("videozen_db_version");
	
	if (CONST_VIDEOZEN_USE_DEFINES) {
		$langs = CONST_VIDEOZEN_LANG;
	}
	else {
		$langs = $videozen_getOption('videozen_lang');
		
		$auth = $videozen_getOption("videozen_auth");

		if (empty($auth) || !$auth) {
			$videozen_updateOption("videozen_auth", wp_generate_password(20,false,false));
		}
	}

	
	
	wp_enqueue_style( 'videozenCSS', "/" . PLUGINDIR . '/videozen/videozen.css' );
	
	
	
	//Upgrade DB
	if ($version < 1)
		videozen_install (1);
	
	
	global $videozen_subs_langs;
	

	$langs = str_replace("\r", "", $langs);
	$langsArr = explode("\n", $langs);
	foreach ($langsArr as $key => $value) {
		$l = explode("|", $value);
		if (count($l) == 2)
			$videozen_subs_langs[$l[0]] = $l[1];
	}
	
	
	
	if (@$_REQUEST['videozen_retry']) {
		videozen_resent((int)$_REQUEST['videozen_retry'] , @$_REQUEST['videozen_retry_format']);
	}
}
add_action('admin_init', 'videozen_admin_init');


function videozen_install($current_version) {
	global $wpdb , $videozen_statustable;

	update_option("videozen_db_version" , $current_version);
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$table_name = $wpdb->prefix . $videozen_statustable;
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

		$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL,
			Format VARCHAR(10) NOT NULL,
			Status VARCHAR(255) NOT NULL,
			URL TEXT NOT NULL,
			RealHeight int null,
			ErrorMessage TEXT NULL,
			UNIQUE KEY id (id , Format)
		);";

		dbDelta($sql);
	}

}




//Plugin init
function videozen_init() {
	global $videozen_subs_langs , $videozen_getOption , $wpdb, $videozen_statustable;
	
	wp_enqueue_script("videojs" , 'http://vjs.zencdn.net/c/video.js');
	wp_enqueue_style ("videojs" , 'http://vjs.zencdn.net/c/video-js.css');
	
	if (CONST_VIDEOZEN_USE_DEFINES) 
		$langs = CONST_VIDEOZEN_LANG;
	else  {
		$langs = $videozen_getOption('videozen_lang');
	}


	$langs = str_replace("\r", "", $langs);
	$langsArr = explode("\n", $langs);
	foreach ($langsArr as $key => $value) {
		$l = explode("|", $value);
		if (count($l) == 2)
			$videozen_subs_langs[$l[0]] = $l[1];
	}

	
	/*NOTIFICATIONS HANDLER*/
	if ($_GET['videozenznot'] == '1') {
		if (CONST_VIDEOZEN_USE_DEFINES) {
			$videozen_apikey = CONST_VIDEOZEN_API_KEY;
			$videozen_authkey = CONST_VIDEOZEN_AUTH;
		}
		else {
			$videozen_apikey = $videozen_getOption('videozen_api_key');	//ZendCoder API Key
			$videozen_authkey = $videozen_getOption('videozen_auth');	//VideoZen internal authentication key, used to verify the notification received
		}
		$notificiation_data = json_decode(trim(file_get_contents('php://input')), true);
		$blogid = (int)@$_GET['wpbid'];
		$postid = (int)@$_GET['id'];
		$format = @$_GET['f'];
		$key = @$_GET['k'];


		if ($key == $videozen_authkey) {
			if ($postid) {
				if ($blogid && function_exists('switch_to_blog'))
					switch_to_blog($blogid);

				if ($notificiation_data['output']['state'] == 'finished')
					$notificiation_data['job']['state'] = 'finished';

				switch ($notificiation_data['job']['state']) {

					case 'finished':
						$label = $postid . "-" . $format;
						$realheight = videozen_zencoder_get_job_real_height($notificiation_data['job']['id'] , $label);

						$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}$videozen_statustable set Status = '%s', RealHeight = %d where ID = %d and Format = '%s' " , VIDEOZEN_STAT_OK , $realheight, $postid , $format  ) );
						break;

					case '':
					case 'cancelled':
					case 'failed':
						$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}$videozen_statustable set Status = '%s' , URL='%s' where ID = %d and Format = '%s'" , VIDEOZEN_STAT_FAIL , $notificiation_data['output']['error_message'], $postid , $format  ) );
						break;

				}
			}
		}
	}
	/*NOTIFICATIONS HANDLER*/
}
add_action('init', 'videozen_init');
/********************************************************************/
/*** INSTALL ENDS ***/
/********************************************************************/




/********************************************************************/
/*** NEW MIME TYPES ***/
/********************************************************************/
add_filter('upload_mimes', 'videozen_upload_mimes');
function videozen_upload_mimes ( $existing_mimes = array() ) {
    $existing_mimes['vtt'] = 'text/plain';
    $existing_mimes['wmv'] = 'video/x-ms-wmv';
    $existing_mimes['webm'] = 'video/webm'; 
    $existing_mimes['mpeg'] = 'video/mpeg';
    $existing_mimes['mpg'] = 'video/mpeg';
    $existing_mimes['avi'] = 'video/avi'; 
    $existing_mimes['3gp'] = 'video/3gpp'; 
    $existing_mimes['3gp2'] = 'video/3gpp2'; 
    $existing_mimes['3g2'] = 'video/3g2';  
    $existing_mimes['3gpp'] = 'video/3gpp'; 
    $existing_mimes['3gpp2'] = 'video/3gpp2'; 
    $existing_mimes['mov'] = 'video/quicktime'; 
    $existing_mimes['mkv'] = 'video/x-matroska'; 
    $existing_mimes['flv'] = 'video/x-flv'; 
    $existing_mimes['ogg'] = 'video/ogg';
    $existing_mimes['oga'] = 'video/ogg'; 
    $existing_mimes['ogv'] = 'video/ogg';
    $existing_mimes['ogx'] = 'application/ogg'; 
    $existing_mimes['divx'] = 'video/divx'; 
    $existing_mimes['asf'] = 'video/x-ms-asf'; 
    $existing_mimes['mp4'] = 'video/mp4';
    $existing_mimes['mpv'] = 'video/mpv';
    $existing_mimes['xvid'] = 'video/xvid';
    return $existing_mimes;
}
/********************************************************************/
/*** NEW MIME TYPES ***/
/********************************************************************/


/********************************************************************/
/*** HOOKS BEGINS ***/
/********************************************************************/
function videozen_attachment_fields_to_save($post, $attachment , $format = "") {
	global $wpdb , $videozen_statustable , $videozen_extensionesVideo , $videozen_outputFormats , $videozen_getOption;

	$path = pathinfo($attachment['url']);
	$ext = strtolower($path['extension']);
	$filename = strtolower($path['filename']);
	
	$S3Folder = $path['dirname'];
	$S3Folder = str_replace("http://", "", $S3Folder);
	$S3Folder = substr($S3Folder, strpos($S3Folder, "/")+1 ) . "/";
	
	global $switched;
	$main_override = is_multisite() && defined( 'MULTISITE' ) && is_main_site();
	
	if ( is_multisite() && !$main_override && ( !isset( $switched ) || $switched === false ) ) {
		if ( defined( 'BLOGUPLOADDIR' ) ) {
			$S3Folder = str_replace("files/", "", BLOGUPLOADDIR) . $S3Folder;
			$S3Folder = str_replace(WP_CONTENT_DIR . "/", "", $S3Folder);
		}
		else
			$S3Folder = str_replace(WP_CONTENT_DIR . "/uploads/", "", $S3Folder);
	}
	else
		$S3Folder = str_replace(WP_CONTENT_DIR . "/uploads/", "", $S3Folder);

	
	
	if (CONST_VIDEOZEN_USE_DEFINES) {
		$videozen_authkey = CONST_VIDEOZEN_AUTH;
		$videozen_output_path = 's3://'.CONST_VIDEOZEN_BUCKET.'/';
		$videozen_output_web_path = 'http://'.CONST_VIDEOZEN_S3WEB.'/';	
	}
	else {
		$videozen_authkey = $videozen_getOption('videozen_auth');	//VideoZen internal authentication key, used to verify the notification received
		$videozen_output_path = 's3://'.$videozen_getOption('videozen_bucket').'/';
		$videozen_output_web_path = 'http://'.$videozen_getOption('videozen_s3web').'/';	
	}
	$notificationURL = get_option("siteurl");

	
	
	if (in_array($ext, $videozen_extensionesVideo)) {
		
		$urlOutputsArr = array();

		foreach ($videozen_outputFormats as $out) {
			$continue = true;
			if (!empty ($format)) {
				//just one format
				$continue = ($format == $out['label']);
			}
			if ($continue) {
				//delete status in case of resend
				$wpdb->query( $wpdb->prepare("delete from {$wpdb->prefix}$videozen_statustable where ID = %d and Format = '%s'" , $post['ID'] , $out['label']) );

				//Save status on DB
				$wpdb->query( $wpdb->prepare("insert into {$wpdb->prefix}$videozen_statustable(ID , Format , Status , URL) values (%d , '%s' , '%s' , '%s')" , $post['ID'] , $out['label'] , VIDEOZEN_STAT_PENDING , $videozen_output_web_path . $S3Folder . $filename . "_" . $out['label'] . "." . $out['format'] ) );

				$urlOutputsArr[] = 
'{
	"base_url": "' . $videozen_output_path . $S3Folder . '",
	"filename": "'.$filename . "_" . $out['label'] . "." . $out['format'].'",
	"label": "'.$post['ID'] . "-" . $out['label'].'",
	"quality": "'.$out['quality'].'",
	"width": "'.$out['width'].'",
	"height": "'.$out['height'].'",
	"notifications": [
		"' . $notificationURL . '?videozenznot=1&wpbid='.$wpdb->blogid.'&id='.$post['ID'].'&f='.$out['label'].'&k='.$videozen_authkey.'"
	],
	"public": 1
}';
			}
		}
		
		//Send encoding request to Zencoder
		$videoURL = $attachment['url'];
		
		$urlOutputs = join(" , \n", $urlOutputsArr);
		
$jsonreq = <<<VIDEOZEN
{   
	"input": "$videoURL",   
	"outputs": [     
		$urlOutputs
	] 
}
VIDEOZEN;

		if (count($urlOutputsArr)==0) return $post;
		
		$res = videozen_zencoder_create_job($jsonreq);
		if ($res) {
			//Update status on DB
			if (empty ($format)) {
				$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}$videozen_statustable set Status = '%s' where ID = %d" , VIDEOZEN_STAT_ENCODING , $post['ID'] ) );
			}
			else {
				$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}$videozen_statustable set Status = '%s' where ID = %d and Format = '%s'" , VIDEOZEN_STAT_ENCODING , $post['ID'] , $format ) );
			}
		}
	}
	
	return $post;
}

function videozen_add_attachment($postid) {
	$post = get_post($postid, ARRAY_A);
	$attach = array( "url" => $post['guid'] );
	videozen_attachment_fields_to_save($post, $attach , "");
}
add_action('add_attachment', 'videozen_add_attachment', 10, 1);
/********************************************************************/
/*** HOOKS ENDS ***/
/********************************************************************/


/********************************************************************/
/*** RESEND BEGINS ***/
/********************************************************************/
function videozen_resent($id , $format) {
	$post = get_post($id, ARRAY_A);
	$attach = array( "url" => $post['guid'] );
	videozen_attachment_fields_to_save($post, $attach , $format);
	header("Location: upload.php");
	die();
}
/********************************************************************/
/*** RESEND ENDS ***/
/********************************************************************/


/********************************************************************/
/*** MEDIA COLUMN BEGINS ***/
/********************************************************************/
function videozen_manage_media_columns($posts_columns, $detached = null) {
	$posts_columns['videozen'] =  __( 'URL CDN' ,'videozen');
	
	return $posts_columns;
}
add_filter( 'manage_media_columns', 'videozen_manage_media_columns', 10, 2);


function videozen_manage_media_custom_column($column_name , $id ) {
	global $wpdb , $videozen_statustable , $videozen_extensionesVideo , $videozen_subs_langs;
	
	if ($column_name == 'videozen' ) {

		$post = get_post($id, ARRAY_A);
		$path = pathinfo($post['guid']);
		$ext = strtolower($path['extension']);
		$filename = strtolower($path['filename']);

		if (!in_array($ext, $videozen_extensionesVideo)) {
			return;
		}

		_e("Shortcode" , 'videozen');
		echo ": <strong>[VIDEOZEN:{$id}]</strong><br/><br/>";
		_e("Subtitles filename:" , 'videozen');
		echo " <br/>";
		foreach ($videozen_subs_langs as $k=>$s) {
			echo "{$s}: <strong>subs_{$id}_{$k}.vtt</strong><br/>";
		}
		
		$formatsRs = $wpdb->get_results	( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$videozen_statustable WHERE ID = %s order by Format", $id ) );
		if (count($formatsRs)==0) {
			echo "<span style='color:#fea100; font-weight:bold'>" . __( 'Upload to Zencoder pending...' , 'videozen') . "</span><br/>";
			echo "<a href='?videozen_retry=".$id."'>" . __( 'Upload again to Zencoder' , 'videozen') . "</a>";
		}
		else {
			echo "<ul class='vz-format-list'>";
			foreach ($formatsRs as $row) {
				echo "<li>";
				echo "<strong>".$row->Format."</strong> - ";

				switch ($row->Status) {
					case VIDEOZEN_STAT_ENCODING:
						echo "<span style='color:#0000a0; font-weight:bold'>" . __( 'Encoding on Zencoder...' , 'videozen') . "</span>";
						break;

					case VIDEOZEN_STAT_PENDING:
						echo "<span style='color:#fea100; font-weight:bold'>" . __( 'Uploading to Zencoder...' , 'videozen') . "</span><br/>";
						echo "<a href='?videozen_retry=".$id."&videozen_retry_format=".$row->Format."'>" . __( 'Upload again to Zencoder' , 'videozen') . "</a>";
						break;

					case VIDEOZEN_STAT_FAIL:
						echo "<span style='color:#BC0B0B; font-weight:bold'>" . __( 'Zencoder reported an enconding error' , 'videozen') . "</span><br/>";
						echo $row->ErrorMessage . "<br/>";
						echo "<a href='?videozen_retry=".$id."&videozen_retry_format=".$row->Format."'>" . __( 'Upload again to Zencoder' , 'videozen') . "</a>";
						break;

					case VIDEOZEN_STAT_OK:
						echo "<span style='color:#00a000; font-weight:bold'>" . __( 'Encoded' , 'videozen') . "</span>";
						echo "<a href='{$row->URL}' target='_blank' title='{$row->URL}'><img src='" .plugins_url( 'download.png' , __FILE__ ). "' alt='Download' class='download-icon'/></a><br/>";
						break;

					default:
						echo __( '- not encoded -' , 'videozen');
				}
				echo "</li>";
			}
			echo "</ul>";
			echo "<a href='?videozen_retry=".$id."'>" . __( 'Upload all to Zencoder' , 'videozen') . "</a>";
		}
	}
}
add_action('manage_media_custom_column', 'videozen_manage_media_custom_column', 10, 2);
/********************************************************************/
/*** MEDIA COLUMN ENDS ***/
/********************************************************************/




/********************************************************************/
/*** DELETE MEDIA BEGINS ***/
/********************************************************************/
function videozen_delete_attachment($id) {
	global $wpdb , $videozen_statustable;
	$wpdb->query( $wpdb->prepare("delete from {$wpdb->prefix}$videozen_statustable where ID = %d" , $id ) );
}
add_action('delete_attachment', 'videozen_delete_attachment', 10, 1);
/********************************************************************/
/*** DELETE MEDIA ENDS ***/
/********************************************************************/




/********************************************************************/
/*** HTTP POST BEGINS ***/
/********************************************************************/
// creates a job on zencoder
function videozen_zencoder_create_job($request) {
	global $videozen_getOption;

	if (CONST_VIDEOZEN_USE_DEFINES)
		$videozen_apikey = CONST_VIDEOZEN_API_KEY;
	else
		$videozen_apikey = $videozen_getOption('videozen_api_key');	//ZendCoder API Key
	
	
	//Init response buffer
	$response = '';

	
	$options = array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_HEADER => 1, 
		CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json",
			"User-Agent: VideoZen v" . VIDEOZEN_VERSION,
			"Accept: application/json",
			"Zencoder-Api-Key: " . $videozen_apikey
		),
		CURLOPT_CONNECTTIMEOUT => 0,
		CURLOPT_SSL_VERIFYPEER => 0, // Turn off verification, curl -k or --insecure
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $request
	);
	
    $ch = curl_init(CONST_ZENCODER_BASE_URL . "/jobs");
    curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	

	$response = str_replace("\r", "", $response);
	$response = split("\n" , $response);

	//echo "<xmp>".$request . "\n\n\n";
	//print_r($response);
	//die();
	
    if (curl_errno($ch)) {
		return false;
    } else {
		//Cuando son m�ltiples trabajos, en 0 devuelve un 100 Continue, y en 2 el 201 Created :s
		return ($response[0] == "HTTP/1.1 201 Created") || ($response[2] == "HTTP/1.1 201 Created");
    }
}
/********************************************************************/
/*** HTTP POST ENDS ***/
/********************************************************************/



/********************************************************************/
/*** POST FILTER BEGINS ***/
/********************************************************************/
function videozen_content_filter($content) {
	global $wpdb , $videozen_statustable , $videozen_extensionesVideo , $videozen_outputFormats , $videozen_subs_langs;
	
	
	$videozen_Media = array();
	
	$matches = array();
	preg_match_all("/\[VIDEOZEN:(\d+)([\s])?(W:(\d+))?([\s])?(H:(\d+))?\]/", $content, $matches);

	$codecsTypes = array(
		"mp4"=>"type='video/mp4; codecs=\"avc1.42E01E, mp4a.40.2\"'",
		"webm"=>"type='video/webm; codecs=\"vp8, vorbis\"' ",
		"ogg"=>"type='video/ogg; codecs=\"theora, vorbis\"'",
		"3gp"=>""
	);

	
	if (isset ($matches[1])) {
		if (is_array ($matches[1])) {
			
			
			$mediaSources = array();
			
			foreach ($matches[1] as $matchIX => $id) {
				
				$shortCodeToReplace = $matches[0][$matchIX];
				$playerW = is_numeric($matches[4][$matchIX]) ? $matches[4][$matchIX] : CONST_VIDEOZEN_DEF_WIDTH;
				$playerH = is_numeric($matches[7][$matchIX]) ? $matches[7][$matchIX] : CONST_VIDEOZEN_DEF_HEIGHT;
				
				$autoHeight = is_numeric($matches[4][$matchIX]) && !is_numeric($matches[7][$matchIX]);

				$formatsRs = $wpdb->get_results	( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$videozen_statustable WHERE ID = %s and Status = '%s' order by Format", $id , VIDEOZEN_STAT_OK ) );
				foreach ($formatsRs as $row) {
					$f = $videozen_outputFormats[$row->Format];
					if ($f) {
						$mediaSources[$row->Format]['label'] = $f['label'];
						$mediaSources[$row->Format]['w'] = $f['width'];
						$mediaSources[$row->Format]['h'] = $f['height'];
						$mediaSources[$row->Format]['rh'] = $row->RealHeight;
						$mediaSources[$row->Format]['URL'] = $row->URL;
						$mediaSources[$row->Format]['format'] = $f['format'];
						
						if ($autoHeight && $row->RealHeight && $f['width']) {
							$playerH = ceil($playerW / ($f['width'] / $row->RealHeight));
							$autoHeight = false;
						}
					}
				}

				$subs = array();
				foreach ($videozen_subs_langs as $k => $s) {
					$sub = $wpdb->get_row( "SELECT guid FROM {$wpdb->posts} WHERE post_type='attachment' and post_name='subs_{$id}_{$k}' LIMIT 1" );
					if (!is_null($sub))
						$subs[] = array(
							'code' => $k , 
							'lang' => $s , 
							'URL' => $sub->guid
						);
				}
				
				ob_start();
				if (strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ||
					strstr($_SERVER['HTTP_USER_AGENT'],'iPod')) {
					//iphone/ipad
					?>  
					<video id="videozen_<?php echo $id ?>" class="video-js vjs-default-skin videozen" controls
					  preload="auto" width="480" height="360"
					  data-setup="{}">
						<?php foreach ($mediaSources as $source) {
						if ($source['label'] != 'iPhone') continue;?>
						<source src="<?php echo $source['URL'] ?>" type='video/<?php echo $source['format'] ?>' >
						<?php } ?>
						<?php foreach ($subs as $sub) {?>
						<track kind="subtitles" src="<?php echo $sub['URL'] ?>" srclang="<?php echo $sub['code'] ?>" label="<?php echo $sub['lang'] ?>">
						<?php } ?>
					</video>
					<?php
				}
				else { 
					//everything else
					?>  
					<video id="videozen_<?php echo $id ?>" class="video-js vjs-default-skin videozen" controls
					  preload="auto" width="<?php echo $playerW ?>" height="<?php echo $playerH ?>"
					  data-setup="{}">
						<?php foreach ($mediaSources as $source) {
						if ($source['label'] == 'iPhone') continue;?>
						<source src="<?php echo $source['URL'] ?>" type='video/<?php echo $source['format'] ?>' >
						<?php } ?>
						<?php foreach ($subs as $sub) {?>
						<track kind="subtitles" src="<?php echo $sub['URL'] ?>" srclang="<?php echo $sub['code'] ?>" label="<?php echo $sub['lang'] ?>">
						<?php } ?>
					</video>
					<?php
				}
				$video = ob_get_clean();

				$content = str_replace($shortCodeToReplace,$video, $content);
				
			}
		}
	}
	
	
	return $content;
}
add_filter( 'the_content', 'videozen_content_filter', 20 );
/********************************************************************/
/*** POST FILTER ENDS ***/
/********************************************************************/




/********************************************************************/
/*** CONFIG BEGINS ***/
/********************************************************************/
include_once 'videozen-conf.php';
/********************************************************************/
/*** CONFIG ENDS ***/
/********************************************************************/




/********************************************************************/
/*** ADMIN GROUP MENU BEGINS ***/
/********************************************************************/
/**
 * Add VideoZen to WP menu
 * 
 * @global type $menu
 * @global array $admin_page_hooks
 * @global array $_registered_pages
 * @global type $wpdb
 * @return boolean 
 */
function videozen_add_admin_menu() {
	if ( !is_super_admin() )
		return false;

	global $menu, $admin_page_hooks, $_registered_pages , $wpdb;

	$params = array(
		'page_title' => __( 'VideoZen', 'videozen' ),
		'menu_title' => __( 'VideoZen', 'videozen' ),
		'access_level' => 10,
		'file' => 'videozen_conf',
		'function' => 'videozen_conf',
		'position' => 20
	);

	extract( $params, EXTR_SKIP );

	$file = plugin_basename( $file );

	$admin_page_hooks[$file] = sanitize_title( $menu_title );

	$hookname = get_plugin_page_hookname( $file, '' );
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	$position = 25;
	do {
		$position++;
	} while ( !empty( $menu[$position] ) );

	if ( empty( $icon_url ) )
		$icon_url = '';
	
	$menu[$position] = array ( $menu_title, "level_10", "videozen_conf", $page_title, 'menu-top ' . $hookname, $hookname, $icon_url );

	$_registered_pages[$hookname] = true;

	$confHook = add_submenu_page( 'videozen_conf', __( 'Configuration', 'videozen'), __( 'Configuration', 'videozen' ), 'manage_options', 'videozen_conf', 'videozen_conf' );
}


if (!CONST_VIDEOZEN_USE_DEFINES) {
	$videozen_menu_processed = false;
	if ($videozen_is_network_activated) {
		add_action( 'network_admin_menu', 'videozen_add_admin_menu' );
		$videozen_menu_processed = true;
	}

	if (!$videozen_menu_processed) {
		if (!$videozen_is_network_admin)
			add_action( 'admin_menu', 'videozen_add_admin_menu' );
		else
			add_action( 'network_admin_menu', 'videozen_add_admin_menu' );
	}
}



// creates a job on zencoder
function videozen_zencoder_get_job_real_height($jobid , $label) {
	global $videozen_getOption;
	
	if (CONST_VIDEOZEN_USE_DEFINES) {
		$videozen_apikey = CONST_VIDEOZEN_API_KEY;
		$videozen_authkey = CONST_VIDEOZEN_AUTH;
	}
	else {
		$videozen_apikey = $videozen_getOption('videozen_api_key');	//ZendCoder API Key
		$videozen_authkey = $videozen_getOption('videozen_auth');	//VideoZen internal authentication key, used to verify the notification received
	}
	
	//Init response buffer
	$response = '';

	$url = CONST_ZENCODER_BASE_URL . "/jobs/{$jobid}.json?api_key=" . $videozen_apikey;
	
	$options = array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_HEADER => 1, 
		CURLOPT_CONNECTTIMEOUT => 0,
		CURLOPT_SSL_VERIFYPEER => 0, // Turn off verification, curl -k or --insecure
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_POST => 0
	);

	$ch = curl_init($url);
	curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	
	if ($status_code != 200) return 0;
	
	$response = str_replace("\r", "", $response);
	if (strpos($response , "\n\n") !== false) {
		$response = substr($response, strpos($response , "\n\n")+1);
		$ret = json_decode($response);
		
		$files = $ret->job->output_media_files;

		foreach ($files as $f)
			if ($label == $f->label)
				return $f->height;
		
	}
	
	return 0;
}
?>