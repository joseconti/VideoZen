<?php
//Configuration page
function videozen_conf() {
	
	if (CONST_VIDEOZEN_USE_DEFINES)
		return;
	
	global $wpdb , $videozen_updateOption , $videozen_getOption;
	
	$auth_error_msg = false;
	
	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'videozen'));

	if ( isset($_POST['submit']) ) {
		check_admin_referer( 'videozen_conf' );
		
			
		
		$videozen_updateOption('videozen_api_key' , $_POST['key']);
		$videozen_updateOption('videozen_bucket' , $_POST['s3']);
		$videozen_updateOption('videozen_s3web' , $_POST['s3web']);
		$videozen_updateOption('videozen_auth' , $_POST['auth']);
		$videozen_updateOption('videozen_lang' , $_POST['lang']);
		
		if ( empty($_POST['auth']) ) {
			$auth_error_msg = true;
			$videozen_updateOption("videozen_auth", wp_generate_password(20,false,false));
		}

	}
?>


<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'videozen') ?></strong></p></div>
<?php endif; ?>
<?php if ( $auth_error_msg ) : ?>
<div id="message" class="error fade"><p><strong><?php _e("The notification passphrase can't be empty, a new passphrase has been created.", 'videozen') ?></strong></p></div>
<?php endif; ?>


<div class="wrap">
	<div class="icon32" id="icon-videozen"><br></div>
	<h2><?php _e('VideoZen Configuration', 'videozen'); ?></h2>
	<div class="">

		<div id="videozen-conf-apikeys">
			<form action="" method="post" id="videozen-conf" style="margin: auto;">
				<p><?php __('VideoZen config page explanation....', 'videozen'); ?></p>

				<h3><label for="key"><?php _e('Zendcoder API Key', 'videozen'); ?></label></h3>
				<p><input id="key" name="key" type="text" size="35" maxlength="50" value="<?php echo $videozen_getOption('videozen_api_key'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /></p>

				<h3 style="margin-top: 30px;"><label for="s3"><?php _e('Amazon S3 bucket name', 'videozen'); ?></label></h3>
				<p><?php _e('Enter here the Amazon S3 bucket name where Zendcoder will upload the encoded videos once encoded (do not include the s3:// prefix)', 'videozen'); ?></p>
				<p>s3://<input id="s3" name="s3" type="s3" size="35" maxlength="100" value="<?php echo $videozen_getOption('videozen_bucket'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /></p>

				<h3 style="margin-top: 30px;"><label for="s3web"><?php _e('URL to access the files saved on the Amazon S3 bucket', 'videozen'); ?></label></h3>
				<p><?php _e('Enter here the URL to access the Amazon S3 bucket where the encoded videos are stored by Zencoder (do not include the http:// prefix)', 'videozen'); ?></p>
				<p>http://<input id="s3web" name="s3web" type="s3web" size="35" maxlength="100" value="<?php echo $videozen_getOption('videozen_s3web'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /></p>

				<h3 style="margin-top: 30px;"><label for="wv_auth"><?php _e('VideoZen notification passphrase', 'videozen'); ?></label></h3>
				<p><?php _e("This passphrase is sent along with the job to Zencoder, and verified when receiving the Zendcoder notification when the encoding jobs are ready, you will rarely need to change this, but, if you need to, use a long combination of leeters and numbers (at least 10) or leave the field blank to get a new one.", 'videozen'); ?></p>
				<p><input id="wv_auth" name="auth" type="auth" size="35" maxlength="100" value="<?php echo $videozen_getOption('videozen_auth'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /></p>

				<h3 style="margin-top: 30px;"><label for="wv_lang"><?php _e('VideoZen available subtitles languages', 'videozen'); ?></label></h3>
				<p><?php _e("Enter one language per line, first the two letter code for the language, a pipe (|) and the language name, for example: en|English. You can read here <a href='http://videojs.com/docs/tracks/' target='_blank'>http://videojs.com/docs/tracks/</a> about the subtitles format and the two letter codes for each language", 'videozen'); ?></p>
				<p><textarea id="wv_lang" name="lang" type="lang" cols="30" rows="15" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"><?php echo $videozen_getOption('videozen_lang'); ?></textarea>


				<?php wp_nonce_field('videozen_conf') ?>

				<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Update options &raquo;', 'videozen'); ?>" /></p>
			</form>
		</div>


	</div>
</div>
<?php
}
?>
