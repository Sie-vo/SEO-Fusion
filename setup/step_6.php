<?php
/*-------------------------------------------------------+
| SEO-Fusion based on PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: setup/step_6.php
| Author: Dennis Vorpahl
| https://sievo.de
| Version: 0.0.1
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if(!defined('IN_FUSION')) die('No direct access allowed');

/**
 * @var array $locale
 * @var string $db_host
 * @var string $db_user
 * @var string $db_pass
 * @var string $db_name
 * @var string $db_prefix
 */
render_header($locale['title']);
echo '<div class="container">
    <div class="row justify-content-center">
        <!-- Volle Breite (col-12) oder zentriert (col-lg-10) -->
        <main class="col-lg-10">
            <div class="main-content shadow-sm">';
echo '<h1>'.$locale['title'].'</h1>
<p class="lead">'.$locale['001'].'</p>
                <div class="card mt-4">
                    <div class="card-body">';
echo "<form name='setupform' method='post' action='index.php'>\n";

require_once "../config.php";
	$dbconnect = dbconnect($db_host, $db_user, $db_pass, $db_name);

	$error = ""; $error_pass = "0"; $error_name = "0"; $error_mail = "0"; $settings['password_algorithm'] = "sha256";

	$username = (isset($_POST['username']) ? stripinput(trim($_POST['username'])) : "");
	if ($username == "") {
		$error .= $locale['070b']."<br /><br />\n";
		$error_name = "1";
	} elseif (!preg_match("/^[-0-9A-Z_@\s]+$/i", $username)) {
		$error .= $locale['070']."<br /><br />\n";
		$error_name = "1";
	}

	require_once "../includes/classes/PasswordAuth.class.php";

	$userPassword = ""; $adminPassword = "";

	$userPass = new PasswordAuth();
	$userPass->inputNewPassword = (isset($_POST['password1']) ? stripinput(trim($_POST['password1'])) : "");
	$userPass->inputNewPassword2 = (isset($_POST['password2']) ? stripinput(trim($_POST['password2'])) : "");
	$returnValue = $userPass->isValidNewPassword();
	if ($returnValue == 0) {
		$userPassword = $userPass->getNewHash();
		$userSalt = $userPass->getNewSalt();
	} elseif ($returnValue == 2) {
		$error .= $locale['071']."<br /><br />\n";
		$error_pass = "1";
	} elseif ($returnValue == 3) {
		$error .= $locale['072']."<br /><br />\n";
	}

	$adminPass = new PasswordAuth();
	$adminPass->inputNewPassword = (isset($_POST['admin_password1']) ? stripinput(trim($_POST['admin_password1'])) : "");
	$adminPass->inputNewPassword2 = (isset($_POST['admin_password2']) ? stripinput(trim($_POST['admin_password2'])) : "");
	$returnValue = $adminPass->isValidNewPassword();
	if ($returnValue == 0) {
		$adminPassword = $adminPass->getNewHash();
		$adminSalt = $adminPass->getNewSalt();
	} elseif ($returnValue == 2) {
		$error .= $locale['073']."<br /><br />\n";
		$error_pass = "1";
	} elseif ($returnValue == 3) {
		$error .= $locale['075']."<br /><br />\n";
	}

	if ($userPass->inputNewPassword == $adminPass->inputNewPassword) {
			$error .= $locale['074']."<br /><br />\n";
			$error_pass = "1";
	}

	$email = (isset($_POST['email']) ? stripinput(trim($_POST['email'])) : "");

 	if ($email == "") {
		$error .= $locale['076b']."<br /><br />\n";
		$error_mail = "1";
	} elseif (!preg_match("/^[-0-9A-Z_\.]{1,50}@([-0-9A-Z_\.]+\.){1,50}([0-9A-Z]){2,4}$/i", $email)) {
		$error .= $locale['076']."<br /><br />\n";
		$error_mail = "1";
	}

	$rows = dbrows(dbquery("SELECT user_id FROM ".$db_prefix."users"));

	if ($error == "") {
		if ($rows == 0) {
			$siteurl = getCurrentURL();
			$url = parse_url($siteurl);

			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('sitename', 'PHP-Fusion Powered Website')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('siteurl', '".$siteurl."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('site_protocol', '".$url['scheme']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('site_host', '".$url['host']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('site_port', '".(isset($url['port']) ? $url['port'] : "")."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('site_path', '".(isset($url['path']) ? $url['path'] : "")."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('sitebanner', 'images/php-fusion-logo.png')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('sitebanner1', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('sitebanner2', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('siteemail', '".$email."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('siteusername', '".$username."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('siteintro', '<div style=\'text-align:center\'>".$locale['230']."</div>')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('description', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('keywords', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('footer', '<div style=\'text-align:center\'>Copyright &copy; ".@date("Y")."</div>')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('opening_page', 'news.php')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_thumb_ratio', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_image_link', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_thumb_w', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_thumb_h', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_photo_max_w', '1800')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_photo_max_h', '1600')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_photo_max_b', '150000')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('locale', '".stripinput($_POST['localeset'])."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('theme', 'Gillette')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('default_search', 'all')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('exclude_left', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('exclude_upper', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('exclude_lower', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('exclude_right', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('shortdate', '".$locale['shortdate']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('longdate', '".$locale['longdate']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('forumdate', '".$locale['forumdate']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('newsdate', '".$locale['newsdate']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('subheaderdate', '".$locale['subheaderdate']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('timeoffset', '0.0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('serveroffset', '0.0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('numofthreads', '15')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('forum_ips', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('attachmax', '150000')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('attachmax_count', '5')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('attachtypes', '.gif,.jpg,.png,.zip,.rar,.tar,.7z')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('thread_notify', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('forum_ranks', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('forum_edit_lock', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('forum_edit_timelimit', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('forum_editpost_to_lastpost', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('forum_last_posts_reply', '10')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('enable_registration', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('email_verification', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('admin_activation', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('display_validation', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('enable_deactivation', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('deactivation_period', '365')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('deactivation_response', '14')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('enable_terms', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('license_agreement', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('license_lastupdate', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('thumb_w', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('thumb_h', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_w', '400')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_h', '300')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_max_w', '1800')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_max_h', '1600')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_max_b', '512000')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('thumb_compression', 'gd2')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('thumbs_per_row', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('thumbs_per_page', '12')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_watermark', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_watermark_image', 'images/watermark.png')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_watermark_text', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_watermark_text_color1', 'FF6600')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_watermark_text_color2', 'FFFF00')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_watermark_text_color3', 'FFFFFF')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('photo_watermark_save', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('tinymce_enabled', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('smtp_host', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('smtp_port', '25')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('smtp_username', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('smtp_password', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('bad_words_enabled', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('bad_words', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('bad_word_replace', '****')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('guestposts', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('comments_enabled', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('ratings_enabled', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('hide_userprofiles', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('userthemes', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('newsperpage', '11')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('flood_interval', '15')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('counter', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('version', '1.00.00')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('maintenance', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('maintenance_message', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_max_b', '512000')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_types', '.pdf,.gif,.jpg,.png,.zip,.rar,.tar,.bz2,.7z')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('articles_per_page', '15')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('downloads_per_page', '15')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('links_per_page', '15')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('comments_per_page', '10')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('comments_sorting', 'ASC')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('comments_avatar', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('avatar_width', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('avatar_height', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('avatar_filesize', '15000')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('avatar_ratio', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('cronjob_day', '".time()."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('cronjob_hour', '".time()."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('flood_autoban', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('visitorcounter_enabled', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('rendertime_enabled', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('popular_threads_timeframe', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('maintenance_level', '102')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_photo_w', '400')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_photo_h', '300')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_image_frontpage', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('news_image_readmore', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('deactivation_action', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('captcha', 'securimage2')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('password_algorithm', 'sha256')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('default_timezone', '".$locale['default_timezone']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('userNameChange', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_screen_max_b', '150000')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_screen_max_w', '1024')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_screen_max_h', '768')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('recaptcha_public', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('recaptcha_private', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('recaptcha_theme', 'red')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_screenshot', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_thumb_max_w', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('download_thumb_max_h', '100')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('multiple_logins', '0')");
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('smtp_auth', '0')"); //new in v7.02.05
			$result = dbquery("INSERT INTO ".$db_prefix."settings (settings_name, settings_value) VALUES ('mime_check', '0')"); //new in v7.02.05
			
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('AD', 'admins.gif', '".$locale['080']."', 'administrators.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('APWR', 'admin_pass.gif', '".$locale['128']."', 'admin_reset.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('AC', 'article_cats.gif', '".$locale['081']."', 'article_cats.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('A', 'articles.gif', '".$locale['082']."', 'articles.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('SB', 'banners.gif', '".$locale['083']."', 'banners.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('BB', 'bbcodes.gif', '".$locale['084']."', 'bbcodes.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('B', 'blacklist.gif', '".$locale['085']."', 'blacklist.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('C', '', '".$locale['086']."', 'reserved', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('CP', 'c-pages.gif', '".$locale['087']."', 'custom_pages.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('DB', 'db_backup.gif', '".$locale['088']."', 'db_backup.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('DC', 'dl_cats.gif', '".$locale['089']."', 'download_cats.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('D', 'dl.gif', '".$locale['090']."', 'downloads.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('ERRO', 'errors.gif', '".$locale['129']."', 'errors.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('FQ', 'faq.gif', '".$locale['091']."', 'faq.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('F', 'forums.gif', '".$locale['092']."', 'forums.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('IM', 'images.gif', '".$locale['093']."', 'images.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('I', 'infusions.gif', '".$locale['094']."', 'infusions.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('IP', '', '".$locale['095']."', 'reserved', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('M', 'members.gif', '".$locale['096']."', 'members.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('NC', 'news_cats.gif', '".$locale['097']."', 'news_cats.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('N', 'news.gif', '".$locale['098']."', 'news.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('P', 'panels.gif', '".$locale['099']."', 'panels.php', '3')");
            $result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('SEO', 'panels.gif', '".$locale['129c']."', 'seo_save.php', '5')");

			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('PH', 'photoalbums.gif', '".$locale['100']."', 'photoalbums.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('PI', 'phpinfo.gif', '".$locale['101']."', 'phpinfo.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('PO', 'polls.gif', '".$locale['102']."', 'polls.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('SL', 'site_links.gif', '".$locale['104']."', 'site_links.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('SM', 'smileys.gif', '".$locale['105']."', 'smileys.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('SU', 'submissions.gif', '".$locale['106']."', 'submissions.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('U', 'upgrade.gif', '".$locale['107']."', 'upgrade.php', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('UG', 'user_groups.gif', '".$locale['108']."', 'user_groups.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('WC', 'wl_cats.gif', '".$locale['109']."', 'weblink_cats.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('W', 'wl.gif', '".$locale['110']."', 'weblinks.php', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S1', 'settings.gif', '".$locale['111']."', 'settings_main.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S2', 'settings_time.gif', '".$locale['112']."', 'settings_time.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S3', 'settings_forum.gif', '".$locale['113']."', 'settings_forum.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S4', 'registration.gif', '".$locale['114']."', 'settings_registration.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S5', 'photoalbums.gif', '".$locale['115']."', 'settings_photo.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S6', 'settings_misc.gif', '".$locale['116']."', 'settings_misc.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S7', 'settings_pm.gif', '".$locale['117']."', 'settings_messages.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S8', 'settings_news.gif', '".$locale['121']."', 'settings_news.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S9', 'settings_users.gif', '".$locale['122']."', 'settings_users.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S10', 'settings_ipp.gif', '".$locale['124']."', 'settings_ipp.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S11', 'settings_dl.gif', '".$locale['123']."', 'settings_dl.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('S12', 'security.gif', '".$locale['125']."', 'settings_security.php', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('UF', 'user_fields.gif', '".$locale['118']."', 'user_fields.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('FR', 'forum_ranks.gif', '".$locale['119']."', 'forum_ranks.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('UFC', 'user_fields_cats.gif', '".$locale['120']."', 'user_field_cats.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('UL', 'user_fields.gif', '".$locale['129a']."', 'user_log.php', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."admin (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('ROB', 'robots.gif', '".$locale['129b']."', 'robots.php', '3')");
			$result = dbquery(
				"INSERT INTO ".$db_prefix."users (
					user_name, user_algo, user_salt, user_password, user_admin_algo, user_admin_salt, user_admin_password, user_email, user_hide_email, user_timezone,
					user_avatar, user_posts, user_threads, user_joined, user_lastvisit, user_ip, user_rights,
					user_groups, user_level, user_status, user_theme, user_location, user_birthdate, user_aim,
					user_icq, user_msn, user_yahoo, user_web, user_sig
				) VALUES (
					'".$username."', 'sha256', '".$userSalt."', '".$userPassword."', 'sha256', '".$adminSalt."', '".$adminPassword."',
					'".$email."', '1', 'Europe/Berlin', '',  '0', '', '".time()."', '0', '0.0.0.0',
					'A.AC.AD.APWR.B.BB.C.CP.DB.DC.D.ERRO.FQ.F.FR.IM.I.IP.M.N.NC.P.PH.PI.PO.ROB.SL.S1.S2.S3.S4.S5.S6.S7.S8.S9.S10.S11.S12.SB.SEO.SM.SU.UF.UFC.UG.UL.U.W.WC',
					'', '103', '0', 'Default', '', '0000-00-00', '', '', '', '', '', ''
				)"
			);

			$result = dbquery("INSERT INTO ".$db_prefix."messages_options (user_id, pm_email_notify, pm_save_sent, pm_inbox, pm_savebox, pm_sentbox) VALUES ('0', '0', '1', '20', '20', '20')");

			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('smiley', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('b', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('i', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('u', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('url', '5')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('mail', '6')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('img', '7')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('center', '8')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('small', '9')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('code', '10')");
			$result = dbquery("INSERT INTO ".$db_prefix."bbcodes (bbcode_name, bbcode_order) VALUES ('quote', '11')");

			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (':)', 'smile.gif', '".$locale['210']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (';)', 'wink.gif', '".$locale['211']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (':(', 'sad.gif', '".$locale['212']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (':|', 'frown.gif', '".$locale['213']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (':o', 'shock.gif', '".$locale['214']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (':P', 'pfft.gif', '".$locale['215']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES ('B)', 'cool.gif', '".$locale['216']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (':D', 'grin.gif', '".$locale['217']."')");
			$result = dbquery("INSERT INTO ".$db_prefix."smileys (smiley_code, smiley_image, smiley_text) VALUES (':@', 'angry.gif', '".$locale['218']."')");

			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['180']."', 'bugs.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['181']."', 'downloads.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['182']."', 'games.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['183']."', 'graphics.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['184']."', 'hardware.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['185']."', 'journal.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['186']."', 'members.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['187']."', 'mods.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['188']."', 'movies.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['189']."', 'network.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['190']."', 'news.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['191']."', 'php-fusion.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['192']."', 'security.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['193']."', 'software.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['194']."', 'themes.gif')");
			$result = dbquery("INSERT INTO ".$db_prefix."news_cats (news_cat_name, news_cat_image) VALUES ('".$locale['195']."', 'windows.gif')");

			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['160']."', 'css_navigation_panel', '', '1', '1', 'file', '0', '0', '1', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['161']."', 'online_users_panel', '', '1', '2', 'file', '0', '0', '1', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['162']."', 'forum_threads_panel', '', '1', '3', 'file', '0', '0', '0', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['163']."', 'latest_articles_panel', '', '1', '4', 'file', '0', '0', '0', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['164']."', 'welcome_message_panel', '', '2', '1', 'file', '0', '0', '1', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['165']."', 'forum_threads_list_panel', '', '2', '2', 'file', '0', '0', '0', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['166']."', 'user_info_panel', '', '4', 1, 'file', '0', '0', '1', '')");
			$result = dbquery("INSERT INTO ".$db_prefix."panels (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list) VALUES ('".$locale['167']."', 'member_poll_panel', '', '4', '2', 'file', '0', '0', '0', '')");

			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['130']."', 'index.php', '0', '2', '0', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['131']."', 'articles.php', '0', '2', '0', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['132']."', 'downloads.php', '0', '2', '0', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['133']."', 'faq.php', '0', '1', '0', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['134']."', 'forum/index.php', '0', '2', '0', '5')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['136']."', 'news_cats.php', '0', '2', '0', '7')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['137']."', 'weblinks.php', '0', '2', '0', '6')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['135']."', 'contact.php', '0', '1', '0', '8')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['138']."', 'photogallery.php', '0', '1', '0', '9')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['139']."', 'search.php', '0', '1', '0', '10')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('---', '---', '101', '1', '0', '11')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['140']."', 'submit.php?stype=l', '101', '1', '0', '12')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['141']."', 'submit.php?stype=n', '101', '1', '0', '13')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['142']."', 'submit.php?stype=a', '101', '1', '0', '14')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['143']."', 'submit.php?stype=p', '101', '1', '0', '15')");
			$result = dbquery("INSERT INTO ".$db_prefix."site_links (link_name, link_url, link_visibility, link_position, link_window, link_order) VALUES ('".$locale['144']."', 'submit.php?stype=d', '101', '1', '0', '16')");

			$result = dbquery("INSERT INTO ".$db_prefix."user_field_cats (field_cat_id, field_cat_name, field_cat_order) VALUES (1, '".$locale['220']."', 1)");
			$result = dbquery("INSERT INTO ".$db_prefix."user_field_cats (field_cat_id, field_cat_name, field_cat_order) VALUES (2, '".$locale['221']."', 2)");
			$result = dbquery("INSERT INTO ".$db_prefix."user_field_cats (field_cat_id, field_cat_name, field_cat_order) VALUES (3, '".$locale['222']."', 3)");
			$result = dbquery("INSERT INTO ".$db_prefix."user_field_cats (field_cat_id, field_cat_name, field_cat_order) VALUES (4, '".$locale['223']."', 4)");

			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_location', '2', '0', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_birthdate', '2', '0', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_skype', '1', '0', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_aim', '1', '0', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_icq', '1', '0', '3')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_msn', '1', '0', '4')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_yahoo', '1', '0', '5')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_web', '1', '0', '6')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_timezone', '3', '0', '1')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_theme', '3', '0', '2')");
			$result = dbquery("INSERT INTO ".$db_prefix."user_fields (field_name, field_cat, field_required, field_order) VALUES ('user_sig', '3', '0', '3')");

			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (1, '".$locale['200']."', 'rank_super_admin.png', 0, '1', 103)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (2, '".$locale['201']."', 'rank_admin.png', 0, '1', 102)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (3, '".$locale['202']."', 'rank_mod.png', 0, '1', 104)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (4, '".$locale['203']."', 'rank0.png', 0, '0', 101)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (5, '".$locale['204']."', 'rank1.png', 10, '0', 101)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (6, '".$locale['205']."', 'rank2.png', 50, '0', 101)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (7, '".$locale['206']."', 'rank3.png', 200, '0', 101)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (8, '".$locale['207']."', 'rank4.png', 500, '0', 101)");
			$result = dbquery("INSERT INTO ".$db_prefix."forum_ranks VALUES (9, '".$locale['208']."', 'rank5.png', 1000, '0', 101)");
		}

		if (function_exists("chmod")) { @chmod("config.php", 0644); }

		echo "<br />\n".$locale['240']."<br /><br />\n";
		echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
		echo "<input type='hidden' name='step' value='7' />\n";
		echo "<input type='submit' name='next' value='".$locale['009']."' class='btn btn-purple' />\n";
	} elseif ($rows == 0) {
		echo "<br />\n".$locale['077']."<br /><br />\n".$error;
		echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
		echo "<input type='hidden' name='error_pass' value='".$error_pass."' />\n";
		echo "<input type='hidden' name='error_name' value='".$error_name."' />\n";
		echo "<input type='hidden' name='error_mail' value='".$error_mail."' />\n";
		echo "<input type='hidden' name='username' value='".$username."' />\n";
		echo "<input type='hidden' name='email' value='".$email."' />\n";
		echo "<input type='hidden' name='step' value='5' />\n";
		echo "<input type='submit' name='back' value='".$locale['008']."' class='btn btn-purple' />\n";
	} else {
		echo "<br />\n".$locale['240']."<br /><br />\n";
		echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
		echo "<input type='hidden' name='step' value='7' />\n";
		echo "<input type='submit' name='next' value='".$locale['009']."' class='btn btn-purple' />\n";
	}

    echo '</div>
        </form>
                </div>
            </div>
        </main>
    </div>
</div>';
render_footer();