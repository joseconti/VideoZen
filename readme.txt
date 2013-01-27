=== VideoZen ===
Contributors: j.conti
Author URI: http://wpvideozen.com
Tags: video, VideoJS, HTML5, video HTML5, videozen, zencoder, s3, cdn, wordpress, wordpress multisite
Requires at least: 3.3
Tested up to: 3.5
Stable tag: 1.0.1
License: GPLv2

VideoZen is fully compatible with Standard WordPress (non-multisite) and WordPress Multisite. The integrated plugin for play and encode videos.


== Description ==

VideoZen is an integrated plugin using VideoJS player, Zencoder.com and Amazon S3 services.

By VideoZen, put Self hosted video on your WordPress or WordPress multisite, be as simple as activating the plugin, get an account at Amazon S3 and Zencoder, upload a video in Media and put a simple WordPress shortcode in a page or post.

When the page or post is viewed, the plugin will build everything you need with all the videos formats needed to be reproduced in any browser, tablet or mobile from Amazon S3. And with Flash fallback.

The Plugin also supports subtitles type VTT, which are the standard HTML5 . Just have to upload it to WordPress media with the name that indicate you the plugin and will be automatically inserted into the video.

The existing Shortcode are:

Simple shortcode will display the original size of the video

`[VIDEOZEN: id_media]`

Shortcode for the width, the height will be calculated automatically:

`[VIDEOZEN: id_media W:234]`

Shortcode full specified width and height:

`[VIDEOZEN: id_media W:234 H:120]`


The plugin is compatible with WordPress and WordPress Multisite.

On WordPress Multisite, can be activated in different ways.

- For the Network: VideoZen be activated for the entire network and can be configured from the Network menu. All sites have access to the plugin and can upload videos.
- Site by site: Site by site and add different configurations on each site.
- Site by site with global settings: Selectable site by site and use DEFINES sets in the file wp-config.php

The define to add in the wp-config.php file to configure as globally are the following:

`define("CONST_VIDEOZEN_USE_DEFINES" , TRUE);
define("CONST_VIDEOZEN_API_KEY" , "123ag");      //ZenCoder API key
define("CONST_VIDEOZEN_BUCKET" , "bucketname"); //do not include the s3:// prefix
define("CONST_VIDEOZEN_S3WEB" , "s3.amazonaws.com/bucket");            //URL to access via web the Amazon S3 bucket, do not include the http:// prefix
define("CONST_VIDEOZEN_AUTH" , "31415926abc123ABC");        //Secret passphrase between your site and zencoder, use a random letters and number string larger than 16 characters
define("CONST_VIDEOZEN_LANG" , "en|English\nes|Spanish");         // Available languages, each language should be formatted as "code|language name", MUST USE DOUBLE QUOTES, separate multiple languages using \n (carriage return), eg: "en|English\nes|Spanish"`

The global configuration is very interesting for WordPress Multisite sites that want to offer free or paid video service for network sites.
 
 == Screenshots ==

No Screenshots


== Installation ==

1. Upload the "videozen" folder to the "/wp-content/plugins/" directory, or download through the "Plugins" menu in WordPress

2. Activate the plugin through the "Plugins" menu in WordPress. Network activate for Multisite or site by site:

On WordPress Multisite can be activated in different ways.

- For the Network: VideoZen be activated for the entire network and can be configured from the Network menu. All sites have access to the plugin and can upload videos.
- Site by site: Site by site and add different configurations on each site.
- Site by site with global settings: Selectable site by site and use DEFINES sets in the file wp-config.php



At this point, you will need an [Zencoder account](http://zencoder.com/) and [Amazon S3 account](http://aws.amazon.com/s3/).

3. Create a S3 Bucket

4. Add this policy to the Bucket:

If you dont need to protect from hotlinking use this policy(Please, remplace YOURBUCKETNAME with your bucket name) :

`{
	"Version": "2008-10-17",
	"Id": "ZencoderBucketPolicy",
	"Statement": [
		{
			"Sid": "Stmt1295042087538",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::395540211253:root"
			},
			"Action": [
				"s3:PutObjectAcl",
				"s3:GetObject",
				"s3:ListMultipartUploadParts",
				"s3:PutObject"
			],
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*"
		},
		{
			"Sid": "Stmt1295042087538",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::395540211253:root"
			},
			"Action": [
				"s3:ListBucketMultipartUploads",
				"s3:GetBucketLocation"
			],
			"Resource": "arn:aws:s3:::YOURBUCKETNAME"
		},
		{
			"Sid": "3",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::cloudfront:user/CloudFront Origin Access Identity E323DPV2F48RSG"
			},
			"Action": "s3:GetObject",
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*"
		}
	]
}`

If you want to protect from hotlinking, add this policy (Please, remplace YOURBUCKETNAME with your bucket name and yourdomain.com for your domain website) :

`{
	"Version": "2008-10-17",
	"Id": "ZencoderBucketPolicy",
	"Statement": [
		{
			"Sid": "Stmt1295042087538",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::395540211253:root"
			},
			"Action": [
				"s3:PutObjectAcl",
				"s3:GetObject",
				"s3:ListMultipartUploadParts",
				"s3:PutObject"
			],
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*"
		},
		{
			"Sid": "Stmt1295042087538",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::395540211253:root"
			},
			"Action": [
				"s3:ListBucketMultipartUploads",
				"s3:GetBucketLocation"
			],
			"Resource": "arn:aws:s3:::YOURBUCKETNAME"
		},
		{
			"Sid": "3",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::cloudfront:user/CloudFront Origin Access Identity E323DPV2F48RSG"
			},
			"Action": "s3:GetObject",
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*"
		},
		{
			"Sid": "2- Allow all referrers to xyz.htm except those listed.",
			"Effect": "Deny",
			"Principal": {
				"AWS": "*"
			},
			"Action": "s3:GetObject",
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*",
			"Condition": {
				"StringNotLike": {
					"aws:Referer": [
						"http://www.yourdomain.com/*",
						"http://yourdomain.com/*",
						"http://www.google.com/reader/*"
					]
				}
			}
		}
	]
}`

If you want to protect from hotlinking and allow direct downloads, add this policy (Please, remplace YOURBUCKETNAME with your bucket name and yourdomain for your domain website) :

`{
	"Version": "2008-10-17",
	"Id": "ZencoderBucketPolicy",
	"Statement": [
		{
			"Sid": "Stmt1295042087538",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::395540211253:root"
			},
			"Action": [
				"s3:PutObjectAcl",
				"s3:GetObject",
				"s3:ListMultipartUploadParts",
				"s3:PutObject"
			],
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*"
		},
		{
			"Sid": "Stmt1295042087538",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::395540211253:root"
			},
			"Action": [
				"s3:ListBucketMultipartUploads",
				"s3:GetBucketLocation"
			],
			"Resource": "arn:aws:s3:::YOURBUCKETNAME"
		},
		{
			"Sid": "3",
			"Effect": "Allow",
			"Principal": {
				"AWS": "arn:aws:iam::cloudfront:user/CloudFront Origin Access Identity E323DPV2F48RSG"
			},
			"Action": "s3:GetObject",
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*"
		},
		{
			"Sid": "2- Allow all referrers to xyz.htm except those listed.",
			"Effect": "Deny",
			"Principal": {
				"AWS": "*"
			},
			"Action": "s3:GetObject",
			"Resource": "arn:aws:s3:::YOURBUCKETNAME/*",
			"Condition": {
				"StringNotLike": {
					"aws:Referer": [
						"http://www.yourdomain.com/*",
						"http://yourdomain.com/*",
						"http://www.google.com/reader/*"
					]
				},
				"Null": {
					"aws:Referer": false
				}
			}
		}
	]
}`

5.- Add all data on VideoZen configuration page or add `define` to `wp-config.php`

6. Updates are automatic. Click on "Upgrade Automatically" if prompted from the admin menu. If you ever have to manually upgrade, simply deactivate, uninstall, and repeat the installation steps with the new version. 


== Upgrade Notice ==

Initial release

== Frequently Asked Questions ==

No Frequently Asked Questions at this time


== Changelog ==

= 1.0.1 - 25 January 2013 =

* Added define("CONST_ZENCODER_BASE_URL", "https://app.zencoder.com/api/v2"); prompted by [Matt McClure ](https://twitter.com/matt_mcclure) Thank's a lot. 
* Some some UI improvements. prompted by [Matt McClure ](https://twitter.com/matt_mcclure) Thank's a lot. 

= 1.0.0 - 21 January 2013 =
* Initial Release
