<?php
/*-------------------------------------------------------+
| SEO-Fusion based on PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: setup/step_2.php
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
 */
echo render_header($locale['title']);
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

    $db_host = (isset($_POST['db_host']) ? stripinput(trim($_POST['db_host'])) : "");
	$db_user = (isset($_POST['db_user']) ? stripinput(trim($_POST['db_user'])) : "");
	$db_pass = (isset($_POST['db_pass']) ? stripinput(trim($_POST['db_pass'])) : "");
	$db_name = (isset($_POST['db_name']) ? stripinput(trim($_POST['db_name'])) : "");
	$db_prefix = (isset($_POST['db_prefix']) ? stripinput(trim($_POST['db_prefix'])) : "");
	$db_driver= (isset($_POST['db_driver']) ? stripinput(trim($_POST['db_driver'])) : "");
	$cookie_prefix = (isset($_POST['cookie_prefix']) ? stripinput(trim($_POST['cookie_prefix'])) : "fusion_");
	if ($db_prefix != "") {
		$db_prefix_last = $db_prefix[strlen($db_prefix)-1];
		if ($db_prefix_last != "_") { $db_prefix = $db_prefix."_"; }
	}
	if ($cookie_prefix != "") {
		$cookie_prefix_last = $cookie_prefix[strlen($cookie_prefix)-1];
		if ($cookie_prefix_last != "_") { $cookie_prefix = $cookie_prefix."_"; }
	}

	if ($db_host != "" && $db_user != "" && $db_name != "" && $db_prefix != "" && $db_driver != "") {
		// Establish mySQL database connection
        require_once("../includes/db_handlers/pdo_functions_include.php");
		$db_connect = dbconnect($db_host, $db_user, $db_pass, $db_name);	
		if ($db_connect) {
			//Set strict mode for compatibility with MySQL Versions 5.7 and higher
			$set_strict_mode = @dbquery("SET SESSION sql_mode='';");
			
				if (dbrows(dbquery("SHOW TABLES LIKE '".str_replace("_", "\_", $db_prefix)."%'")) == "0") {
					$table_name = uniqid($db_prefix, false); $can_write = true;
					$result = dbquery("CREATE TABLE ".$table_name." (test_field VARCHAR(10) NOT NULL)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");
					if (!$result) { $can_write = false; }
					$result = dbquery("DROP TABLE ".$table_name);
					if (!$result) { $can_write = false; }
					if ($can_write) {
						$config = "<?php\n";
						$config .= "// database settings\n";
						$config .= "\$db_host = '".$db_host."';\n";
						$config .= "\$db_user = '".$db_user."';\n";
						$config .= "\$db_pass = '".$db_pass."';\n";
						$config .= "\$db_name = '".$db_name."';\n";
						$config .= "\$db_driver = '".$db_driver."';\n";
						$config .= "\$db_prefix = '".$db_prefix."';\n";
						$config .= "define('DB_PREFIX', '".$db_prefix."');\n";
						$config .= "define('COOKIE_PREFIX', '".$cookie_prefix."');\n";
						$config .= "?>";
						$temp = fopen("config.php","w");
						if (fwrite($temp, $config)) {
							fclose($temp);
							$fail = false;
							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."admin");
							$result = dbquery("CREATE TABLE ".$db_prefix."admin (
							admin_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							admin_rights CHAR(4) NOT NULL DEFAULT '',
							admin_image VARCHAR(50) NOT NULL DEFAULT '',
							admin_title VARCHAR(50) NOT NULL DEFAULT '',
							admin_link VARCHAR(100) NOT NULL DEFAULT 'reserved',
							admin_page TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							PRIMARY KEY (admin_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."admin_resetlog");
							$result = dbquery("CREATE TABLE ".$db_prefix."admin_resetlog (
							reset_id mediumint(8) unsigned NOT NULL auto_increment,
							reset_admin_id mediumint(8) unsigned NOT NULL default '1',
							reset_timestamp int(10) unsigned NOT NULL default '0',
							reset_sucess text,
							reset_failed text,
							reset_admins varchar(8) NOT NULL default '0',
							reset_reason varchar(255) NOT NULL,
							PRIMARY KEY (reset_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."articles");
							$result = dbquery("CREATE TABLE ".$db_prefix."articles (
							article_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							article_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							article_subject VARCHAR(200) NOT NULL DEFAULT '',
							article_snippet text,
							article_article text,
							article_draft TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							article_breaks CHAR(1) NOT NULL DEFAULT '',
							article_name MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
							article_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							article_reads MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							article_allow_comments TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							article_allow_ratings TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							PRIMARY KEY (article_id),
							KEY article_cat (article_cat),
							KEY article_datestamp (article_datestamp),
							KEY article_reads (article_reads)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."article_cats");
							$result = dbquery("CREATE TABLE ".$db_prefix."article_cats (
							article_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							article_cat_name VARCHAR(100) NOT NULL DEFAULT '',
							article_cat_description VARCHAR(200) NOT NULL DEFAULT '',
							article_cat_sorting VARCHAR(50) NOT NULL DEFAULT 'article_subject ASC',
							article_cat_access TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (article_cat_id),
							KEY article_cat_access (article_cat_access)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."bbcodes");
							$result = dbquery("CREATE TABLE ".$db_prefix."bbcodes (
							bbcode_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							bbcode_name VARCHAR(20) NOT NULL DEFAULT '',
							bbcode_order SMALLINT(5) UNSIGNED NOT NULL,
							PRIMARY KEY (bbcode_id),
							KEY bbcode_order (bbcode_order)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."blacklist");
							$result = dbquery("CREATE TABLE ".$db_prefix."blacklist (
							blacklist_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							blacklist_user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							blacklist_ip VARCHAR(45) NOT NULL DEFAULT '',
							blacklist_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							blacklist_email VARCHAR(100) NOT NULL DEFAULT '',
							blacklist_reason text,
							blacklist_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (blacklist_id),
							KEY blacklist_ip_type (blacklist_ip_type)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."captcha");
							$result = dbquery("CREATE TABLE ".$db_prefix."captcha (
							captcha_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							captcha_ip VARCHAR(45) NOT NULL DEFAULT '',
							captcha_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							captcha_encode VARCHAR(32) NOT NULL DEFAULT '',
							captcha_string VARCHAR(15) NOT NULL DEFAULT '',
							KEY captcha_datestamp (captcha_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."comments");
							$result = dbquery("CREATE TABLE ".$db_prefix."comments (
							comment_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							comment_item_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							comment_type CHAR(2) NOT NULL DEFAULT '',
							comment_name VARCHAR(50) NOT NULL DEFAULT '',
							comment_message text,
							comment_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							comment_ip VARCHAR(45) NOT NULL DEFAULT '',
							comment_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							comment_hidden TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (comment_id),
							KEY comment_datestamp (comment_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."custom_pages");
							$result = dbquery("CREATE TABLE ".$db_prefix."custom_pages (
							page_id MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
							page_title VARCHAR(200) NOT NULL DEFAULT '',
							page_access TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							page_content text,
							page_allow_comments TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							page_allow_ratings TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (page_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."download_cats");
							$result = dbquery("CREATE TABLE ".$db_prefix."download_cats (
							download_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							download_cat_name VARCHAR(100) NOT NULL DEFAULT '',
							download_cat_description text,
							download_cat_sorting VARCHAR(50) NOT NULL DEFAULT 'download_title ASC',
							download_cat_access TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (download_cat_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."downloads");
							$result = dbquery("CREATE TABLE ".$db_prefix."downloads (
							download_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							download_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							download_homepage VARCHAR(100) NOT NULL DEFAULT '',
							download_title VARCHAR(100) NOT NULL DEFAULT '',
							download_description_short VARCHAR(255) NOT NULL,
							download_description text,
							download_image VARCHAR(100) NOT NULL DEFAULT '',
							download_image_thumb VARCHAR(100) NOT NULL DEFAULT '',
							download_url VARCHAR(200) NOT NULL DEFAULT '',
							download_file VARCHAR(100) NOT NULL DEFAULT '',
							download_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							download_license VARCHAR(50) NOT NULL DEFAULT '',
							download_copyright VARCHAR(250) NOT NULL DEFAULT '',
							download_os VARCHAR(50) NOT NULL DEFAULT '',
							download_version VARCHAR(20) NOT NULL DEFAULT '',
							download_filesize VARCHAR(20) NOT NULL DEFAULT '',
							download_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							download_count INT(10) UNSIGNED NOT NULL DEFAULT '0',
							download_allow_comments TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							download_allow_ratings TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (download_id),
							KEY download_datestamp (download_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."errors");
							$result = dbquery("CREATE TABLE ".$db_prefix."errors (
							error_id mediumint(8) unsigned NOT NULL auto_increment,
							error_level smallint(5) unsigned NOT NULL,
							error_message text,
							error_file varchar(255) NOT NULL,
							error_line smallint(5) NOT NULL,
							error_page varchar(200) NOT NULL,
							error_user_level smallint(3) NOT NULL,
							error_user_ip varchar(45) NOT NULL default '',
							error_user_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							error_status tinyint(1) NOT NULL default '0',
							error_timestamp int(10) NOT NULL,
							PRIMARY KEY (error_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."faq_cats");
							$result = dbquery("CREATE TABLE ".$db_prefix."faq_cats (
							faq_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							faq_cat_name VARCHAR(200) NOT NULL DEFAULT '',
							faq_cat_description VARCHAR(250) NOT NULL DEFAULT '',
							PRIMARY KEY(faq_cat_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."faqs");
							$result = dbquery("CREATE TABLE ".$db_prefix."faqs (
							faq_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							faq_cat_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							faq_question VARCHAR(200) NOT NULL DEFAULT '',
							faq_answer text,
							PRIMARY KEY(faq_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."flood_control");
							$result = dbquery("CREATE TABLE ".$db_prefix."flood_control (
							flood_ip VARCHAR(45) NOT NULL DEFAULT '',
							flood_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							flood_timestamp INT(5) UNSIGNED NOT NULL DEFAULT '0',
							KEY flood_timestamp (flood_timestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."forum_attachments");
							$result = dbquery("CREATE TABLE ".$db_prefix."forum_attachments (
							attach_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							thread_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							post_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							attach_name VARCHAR(100) NOT NULL DEFAULT '',
							attach_ext VARCHAR(5) NOT NULL DEFAULT '',
							attach_size INT(20) UNSIGNED NOT NULL DEFAULT '0',
							attach_count INT(10) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (attach_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."forum_ranks");
							$result = dbquery("CREATE TABLE ".$db_prefix."forum_ranks (
							rank_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							rank_title VARCHAR(100) NOT NULL DEFAULT '',
							rank_image VARCHAR(100) NOT NULL DEFAULT '',
							rank_posts iNT(10) UNSIGNED NOT NULL DEFAULT '0',
							rank_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							rank_apply SMALLINT(5) UNSIGNED NOT NULL DEFAULT '101',
							PRIMARY KEY (rank_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."forum_poll_options");
							$result = dbquery("CREATE TABLE ".$db_prefix."forum_poll_options (
							thread_id MEDIUMINT(8) unsigned NOT NULL,
							forum_poll_option_id SMALLINT(5) UNSIGNED NOT NULL,
							forum_poll_option_text VARCHAR(150) NOT NULL,
							forum_poll_option_votes SMALLINT(5) UNSIGNED NOT NULL,
							KEY thread_id (thread_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."forum_poll_voters");
							$result = dbquery("CREATE TABLE ".$db_prefix."forum_poll_voters (
							thread_id MEDIUMINT(8) UNSIGNED NOT NULL,
							forum_vote_user_id MEDIUMINT(8) UNSIGNED NOT NULL,
							forum_vote_user_ip VARCHAR(45) NOT NULL,
							forum_vote_user_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							KEY thread_id (thread_id,forum_vote_user_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."forum_polls");
							$result = dbquery("CREATE TABLE ".$db_prefix."forum_polls (
							thread_id MEDIUMINT(8) UNSIGNED NOT NULL,
							forum_poll_title VARCHAR(250) NOT NULL,
							forum_poll_start INT(10) UNSIGNED DEFAULT NULL,
							forum_poll_length iNT(10) UNSIGNED NOT NULL,
							forum_poll_votes SMALLINT(5) unsigned NOT NULL,
							KEY thread_id (thread_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."forums");
							$result = dbquery("CREATE TABLE ".$db_prefix."forums (
							forum_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							forum_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							forum_name VARCHAR(50) NOT NULL DEFAULT '',
							forum_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							forum_description text,
							forum_moderators text,
							forum_access TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							forum_post SMALLINT(3) UNSIGNED DEFAULT '101',
							forum_reply SMALLINT(3) UNSIGNED DEFAULT '101',
							forum_poll SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
							forum_vote SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
							forum_attach SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
							forum_attach_download SMALLINT(3) UNSIGNED NOT NULL DEFAULT'0',
							forum_lastpost INT(10) UNSIGNED NOT NULL DEFAULT '0',
							forum_postcount MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							forum_threadcount MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							forum_lastuser MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							forum_merge TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (forum_id),
							KEY forum_order (forum_order),
							KEY forum_lastpost (forum_lastpost),
							KEY forum_postcount (forum_postcount),
							KEY forum_threadcount (forum_threadcount)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."infusions");
							$result = dbquery("CREATE TABLE ".$db_prefix."infusions (
							inf_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							inf_title VARCHAR(100) NOT NULL DEFAULT '',
							inf_folder VARCHAR(100) NOT NULL DEFAULT '',
							inf_version VARCHAR(10) NOT NULL DEFAULT '0',
							PRIMARY KEY (inf_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."messages");
							$result = dbquery("CREATE TABLE ".$db_prefix."messages (
							message_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							message_to MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							message_from MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							message_subject VARCHAR(100) NOT NULL DEFAULT '',
							message_message text,
							message_smileys CHAR(1) NOT NULL DEFAULT '',
							message_read TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							message_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							message_folder TINYINT(1) UNSIGNED NOT NULL DEFAULT  '0',
							PRIMARY KEY (message_id),
							KEY message_datestamp (message_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."messages_options");
							$result = dbquery("CREATE TABLE ".$db_prefix."messages_options (
							user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							pm_email_notify tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
							pm_save_sent tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
							pm_inbox SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL,
							pm_savebox SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL,
							pm_sentbox SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL,
							PRIMARY KEY (user_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."news");
							$result = dbquery("CREATE TABLE ".$db_prefix."news (
							news_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							news_subject VARCHAR(200) NOT NULL DEFAULT '',
							news_image VARCHAR(100) NOT NULL DEFAULT '',
							news_image_t1 VARCHAR(100) NOT NULL DEFAULT '',
							news_image_t2 VARCHAR(100) NOT NULL DEFAULT '',
							news_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							news_news text,
							news_extended text,
							news_breaks CHAR(1) NOT NULL DEFAULT '',
							news_name MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
							news_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							news_start INT(10) UNSIGNED NOT NULL DEFAULT '0',
							news_end INT(10) UNSIGNED NOT NULL DEFAULT '0',
							news_visibility TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							news_reads INT(10) UNSIGNED NOT NULL DEFAULT '0',
							news_draft TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							news_sticky TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							news_allow_comments TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							news_allow_ratings TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							PRIMARY KEY (news_id),
							KEY news_datestamp (news_datestamp),
							KEY news_reads (news_reads)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."news_cats");
							$result = dbquery("CREATE TABLE ".$db_prefix."news_cats (
							news_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							news_cat_name VARCHAR(100) NOT NULL DEFAULT '',
							news_cat_image VARCHAR(100) NOT NULL DEFAULT '',
							PRIMARY KEY (news_cat_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."new_users");
							$result = dbquery("CREATE TABLE ".$db_prefix."new_users (
							user_code VARCHAR(40) NOT NULL,
							user_name VARCHAR(30) NOT NULL,
							user_email VARCHAR(100) NOT NULL,
							user_datestamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
							user_info text,
							KEY user_datestamp (user_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."email_verify");
							$result = dbquery("CREATE TABLE ".$db_prefix."email_verify (
							user_id MEDIUMINT(8) NOT NULL,
							user_code VARCHAR(32) NOT NULL,
							user_email VARCHAR(100) NOT NULL,
							user_datestamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
							KEY user_datestamp (user_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."ratings");
							$result = dbquery("CREATE TABLE ".$db_prefix."ratings (
							rating_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							rating_item_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							rating_type CHAR(1) NOT NULL DEFAULT '',
							rating_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							rating_vote TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							rating_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							rating_ip VARCHAR(45) NOT NULL DEFAULT '',
							rating_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							PRIMARY KEY (rating_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."online");
							$result = dbquery("CREATE TABLE ".$db_prefix."online (
							online_user VARCHAR(50) NOT NULL DEFAULT '',
							online_ip VARCHAR(45) NOT NULL DEFAULT '',
							online_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							online_lastactive INT(10) UNSIGNED NOT NULL DEFAULT '0'
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."panels");
							$result = dbquery("CREATE TABLE ".$db_prefix."panels (
							panel_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							panel_name VARCHAR(100) NOT NULL DEFAULT '',
							panel_filename VARCHAR(100) NOT NULL DEFAULT '',
							panel_content text,
							panel_side TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							panel_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							panel_type VARCHAR(20) NOT NULL DEFAULT '',
							panel_access TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							panel_display TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							panel_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							panel_url_list text,
							panel_restriction TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (panel_id),
							KEY panel_order (panel_order)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."photo_albums");
							$result = dbquery("CREATE TABLE ".$db_prefix."photo_albums (
							album_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							album_title VARCHAR(100) NOT NULL DEFAULT '',
							album_description text,
							album_thumb VARCHAR(100) NOT NULL DEFAULT '',
							album_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							album_access SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							album_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							album_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (album_id),
							KEY album_order (album_order),
							KEY album_datestamp (album_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."photos");
							$result = dbquery("CREATE TABLE ".$db_prefix."photos (
							photo_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							album_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							photo_title VARCHAR(100) NOT NULL DEFAULT '',
							photo_description text,
							photo_filename VARCHAR(100) NOT NULL DEFAULT '',
							photo_thumb1 VARCHAR(100) NOT NULL DEFAULT '',
							photo_thumb2 VARCHAR(100) NOT NULL DEFAULT '',
							photo_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							photo_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							photo_views INT(10) UNSIGNED NOT NULL DEFAULT '0',
							photo_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							photo_allow_comments tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
							photo_allow_ratings tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
							PRIMARY KEY (photo_id),
							KEY photo_order (photo_order),
							KEY photo_datestamp (photo_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."votes");
							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."poll_votes");
							$result = dbquery("CREATE TABLE ".$db_prefix."poll_votes (
							vote_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							vote_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							vote_opt SMALLINT(2) UNSIGNED NOT NULL DEFAULT '0',
							poll_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (vote_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."polls");
							$result = dbquery("CREATE TABLE ".$db_prefix."polls (
							poll_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							poll_title VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_0 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_1 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_2 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_3 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_4 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_5 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_6 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_7 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_8 VARCHAR(200) NOT NULL DEFAULT '',
							poll_opt_9 VARCHAR(200) NOT NULL DEFAULT '',
							poll_started INT(10) UNSIGNED NOT NULL DEFAULT '0',
							poll_ended INT(10) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (poll_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."posts");
							$result = dbquery("CREATE TABLE ".$db_prefix."posts (
							forum_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							thread_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							post_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							post_message text,
							post_showsig TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							post_smileys TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							post_author MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							post_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							post_ip VARCHAR(45) NOT NULL DEFAULT '',
							post_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							post_edituser MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							post_edittime INT(10) UNSIGNED NOT NULL DEFAULT '0',
							post_editreason text,
							post_hidden TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							post_locked TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (post_id),
							KEY thread_id (thread_id),
							KEY post_datestamp (post_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."settings");
							$result = dbquery("CREATE TABLE ".$db_prefix."settings (
							settings_name VARCHAR(200) NOT NULL DEFAULT '',
							settings_value text,
							PRIMARY KEY (settings_name)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."settings_inf");
							$result = dbquery("CREATE TABLE ".$db_prefix."settings_inf (
							settings_name VARCHAR(200) NOT NULL DEFAULT '',
							settings_value text,
							settings_inf VARCHAR(200) NOT NULL DEFAULT '',
							PRIMARY KEY (settings_name)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."site_links");
							$result = dbquery("CREATE TABLE ".$db_prefix."site_links (
							link_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							link_name VARCHAR(100) NOT NULL DEFAULT '',
							link_url VARCHAR(200) NOT NULL DEFAULT '',
							link_visibility TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							link_position TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							link_window TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							link_order SMALLINT(2) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (link_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."smileys");
							$result = dbquery("CREATE TABLE ".$db_prefix."smileys (
							smiley_id MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
							smiley_code VARCHAR(50) NOT NULL,
							smiley_image VARCHAR(100) NOT NULL,
							smiley_text VARCHAR(100) NOT NULL,
							PRIMARY KEY (smiley_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."submissions");
							$result = dbquery("CREATE TABLE ".$db_prefix."submissions (
							submit_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							submit_type CHAR(1) NOT NULL,
							submit_user MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
							submit_datestamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
							submit_criteria text,
							PRIMARY KEY (submit_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."suspends");
							$result = dbquery("CREATE TABLE ".$db_prefix."suspends (
							suspend_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							suspended_user MEDIUMINT(8) UNSIGNED NOT NULL,
							suspending_admin MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
							suspend_ip VARCHAR(45) NOT NULL DEFAULT '',
							suspend_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							suspend_date INT(10) NOT NULL DEFAULT '0',
							suspend_reason text,
							suspend_type TINYINT(1) NOT NULL DEFAULT '0',
							reinstating_admin MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
							reinstate_reason text,
							reinstate_date INT(10) NOT NULL DEFAULT '0',
							reinstate_ip VARCHAR(45) NOT NULL DEFAULT '',
							reinstate_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							PRIMARY KEY (suspend_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."threads");
							$result = dbquery("CREATE TABLE ".$db_prefix."threads (
							forum_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							thread_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							thread_subject VARCHAR(100) NOT NULL DEFAULT '',
							thread_author MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							thread_views MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							thread_lastpost INT(10) UNSIGNED NOT NULL DEFAULT '0',
							thread_lastpostid MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							thread_lastuser MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							thread_postcount SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							thread_poll TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							thread_sticky TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							thread_locked TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							thread_hidden TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (thread_id),
							KEY thread_postcount (thread_postcount),
							KEY thread_lastpost (thread_lastpost),
							KEY thread_views (thread_views)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."thread_notify");
							$result = dbquery("CREATE TABLE ".$db_prefix."thread_notify (
							thread_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							notify_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							notify_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							notify_status tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
							KEY notify_datestamp (notify_datestamp)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."user_field_cats");
							$result = dbquery("CREATE TABLE ".$db_prefix."user_field_cats (
							field_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
							field_cat_name VARCHAR(200) NOT NULL ,
							field_cat_order SMALLINT(5) UNSIGNED NOT NULL ,
							PRIMARY KEY (field_cat_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."user_fields");
							$result = dbquery("CREATE TABLE ".$db_prefix."user_fields (
							field_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							field_name VARCHAR(50) NOT NULL,
							field_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
 							field_required TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							field_log TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							field_registration TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
 							field_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (field_id),
							KEY field_order (field_order)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."user_groups");
							$result = dbquery("CREATE TABLE ".$db_prefix."user_groups (
							group_id TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
							group_name VARCHAR(100) NOT NULL,
							group_description VARCHAR(200) NOT NULL,
							PRIMARY KEY (group_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."user_log");
							$result = dbquery("CREATE TABLE ".$db_prefix."user_log (
							userlog_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							userlog_user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							userlog_field VARCHAR(50) NOT NULL DEFAULT '',
							userlog_value_new text,
							userlog_value_old text,
							userlog_timestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY (userlog_id),
							KEY userlog_user_id (userlog_user_id),
							KEY userlog_field (userlog_field)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."users");
							$result = dbquery("CREATE TABLE ".$db_prefix."users (
							user_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							user_name VARCHAR(30) NOT NULL DEFAULT '',
							user_algo VARCHAR(10) NOT NULL DEFAULT 'sha256',
							user_salt VARCHAR(40) NOT NULL DEFAULT '',
							user_password VARCHAR(64) NOT NULL DEFAULT '',
							user_admin_algo VARCHAR(10) NOT NULL DEFAULT 'sha256',
							user_admin_salt VARCHAR(40) NOT NULL DEFAULT '',
							user_admin_password VARCHAR(64) NOT NULL DEFAULT '',
							user_email VARCHAR(100) NOT NULL DEFAULT '',
							user_hide_email TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
							user_timezone VARCHAR(75) NOT NULL DEFAULT '".$locale['default_timezone']."',
							user_avatar VARCHAR(100) NOT NULL DEFAULT '',
							user_posts SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							user_threads text,
							user_joined INT(10) UNSIGNED NOT NULL DEFAULT '0',
							user_lastvisit INT(10) UNSIGNED NOT NULL DEFAULT '0',
							user_ip VARCHAR(45) NOT NULL DEFAULT '0.0.0.0',
							user_ip_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
							user_rights text,
							user_groups text,
							user_level TINYINT(3) UNSIGNED NOT NULL DEFAULT '101',
							user_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
							user_actiontime INT(10) UNSIGNED NOT NULL DEFAULT '0',
							user_theme VARCHAR(100) NOT NULL DEFAULT 'Default',
							user_location VARCHAR(50) NOT NULL DEFAULT '',
							user_birthdate VARCHAR(10) NOT NULL DEFAULT '0000-00-00',
							user_skype VARCHAR(100) NOT NULL DEFAULT '',
							user_aim VARCHAR(16) NOT NULL DEFAULT '',
							user_icq VARCHAR(15) NOT NULL DEFAULT '',
							user_msn VARCHAR(100) NOT NULL DEFAULT '',
							user_yahoo VARCHAR(100) NOT NULL DEFAULT '',
							user_web VARCHAR(200) NOT NULL DEFAULT '',
							user_sig text,
							PRIMARY KEY (user_id),
							KEY user_name (user_name),
							KEY user_joined (user_joined),
							KEY user_lastvisit (user_lastvisit)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."weblink_cats");
							$result = dbquery("CREATE TABLE ".$db_prefix."weblink_cats (
							weblink_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							weblink_cat_name VARCHAR(100) NOT NULL DEFAULT '',
							weblink_cat_description text,
							weblink_cat_sorting VARCHAR(50) NOT NULL DEFAULT 'weblink_name ASC',
							weblink_cat_access TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY(weblink_cat_id)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

							if (!$result) { $fail = true; }

							$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."weblinks");
							$result = dbquery("CREATE TABLE ".$db_prefix."weblinks (
							weblink_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
							weblink_name VARCHAR(100) NOT NULL DEFAULT '',
							weblink_description text,
							weblink_url VARCHAR(200) NOT NULL DEFAULT '',
							weblink_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
							weblink_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
							weblink_count SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
							PRIMARY KEY(weblink_id),
							KEY weblink_datestamp (weblink_datestamp),
							KEY weblink_count (weblink_count)
							)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

                            require_once("../includes/seo-functions_include.php");
                            ensure_seo_table();

							if (!$result) { $fail = true; }

							if (!$fail) {
								echo "<br />\n".$locale['040']."<br /><br />\n";
								echo $locale['041']."<br /><br />\n";
								echo $locale['042']."<br /><br />\n";
								$success = true;
								$db_error = 6;
							} else {
								echo "<br />\n".$locale['040']."<br /><br />\n";
								echo $locale['041']."<br /><br />\n";
								echo "<strong>".$locale['043']."</strong> ".$locale['048']."<br /><br />\n";
								$success = false;
								$db_error = 0;
							}
						} else {
							echo "<br />\n".$locale['040']."<br /><br />\n";
							echo "<strong>".$locale['043']."</strong> ".$locale['046']."<br />\n";
							echo "<span class='small'>".$locale['047']."</span><br /><br />\n";
							$success = false;
							$db_error = 5;
						}
					} else {
						echo "<br />\n".$locale['040']."<br /><br />\n";
						echo "<strong>".$locale['043']."</strong> ".$locale['054']."<br />\n";
						echo "<span class='small'>".$locale['055']."</span><br /><br />\n";
						$success = false;
						$db_error = 4;
					}
				} else {
					echo "<br />\n<strong>".$locale['043']."<strong> ".$locale['052']."<br />\n";
					echo "<span class='small'>".$locale['053']."</span><br /><br />\n";
					$success = false;
					$db_error = 3;
				}
			
		} else {
			echo "<br />\n<strong>".$locale['043']."<strong> ".$locale['044']."<br />\n";
			echo "<span class='small'>".$locale['045']."</span><br /><br />\n";
			$success = false;
			$db_error = 1;
		}
	} else {
		echo "<br />\n<strong>".$locale['043']."<strong> ".$locale['056']."<br />\n";
		echo "<span class='small'>".$locale['057']."</span><br /><br />\n";
		$success = false;
		$db_error = 7;
	}
	echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
	if ($success) {
		echo "<input type='hidden' name='step' value='5' />\n";
		echo "<input type='submit' name='next' value='".$locale['007']."' class='btn btn-purple' />\n";
	} else {
		echo "<input type='hidden' name='step' value='3' />\n";
		echo "<input type='hidden' name='db_host' value='".$db_host."' />\n";
		echo "<input type='hidden' name='db_user' value='".$db_user."' />\n";
		echo "<input type='hidden' name='db_name' value='".$db_name."' />\n";
		echo "<input type='hidden' name='db_prefix' value='".$db_prefix."' />\n";
		echo "<input type='hidden' name='db_error' value='".$db_error."' />\n";
		echo "<input type='submit' name='next' value='".$locale['008']."' class='btn btn-purple' />\n";
	}

    echo '</div>
        </form>
                </div>
            </div>
        </main>
    </div>
</div>';
echo render_footer();