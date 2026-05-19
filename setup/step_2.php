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


if (!file_exists("config.php")) {
		if (file_exists("_config.php") && function_exists("rename")) {
			@rename("_config.php", "config.php");
		} else {
			$handle = fopen("config.php", "w");
			fclose($handle);
		}
	}

	$check_arr = [
		"administration/db_backups" => false,
		"forum/attachments" => false,
		"downloads" => false,
		"downloads/images" => false,
		"downloads/submissions/" => false,
		"downloads/submissions/images" => false,
		"ftp_upload" => false,
		"images" => false,
		"images/imagelist.js" => false,
		"images/articles" => false,
		"images/avatars" => false,
		"images/news" => false,
		"images/news/thumbs" => false,
		"images/news_cats" => false,
		"images/photoalbum" => false,
		"images/photoalbum/submissions" => false,
		"config.php" => false,
		"robots.txt" => false
	];

	$write_check = true; $check_display = "";

	foreach ($check_arr as $key => $value) {
		if (file_exists($key) && is_writable($key)) {
			$check_arr[$key] = true;
		} else {
			if (file_exists($key) && function_exists("chmod") && @chmod($key, 0777) && is_writable($key)) {
				$check_arr[$key] = true;
			} else {
				$write_check = false;
			}
		}
        if ($check_arr[$key] == true){
            $check_display .= "<div class='alert alert-access'>".$key." ".$locale['023']."</div>\n";
        }else{
            $check_display .= "<div class='alert alert-danger'>".$key." ".$locale['024']."</div>\n";
        }
	}

    if(function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
        $write_check = true;
        $mod = '<div class="alert alert-access">Mod-rewrite steht zur Verfügung (Wird gebraucht, um Suchmaschinenfreundliche Links zu händeln).</div>';
    } else {
        $write_check = false;
        $mod = '<div class="alert alert-danger">Mod-Rewrite ist nicht aktiviert, Bitte aktiviere es oder wende dich an deinen Hoster! (Wird gebraucht, um Suchmaschinenfreundliche Links zu händeln).<br />Ohne Mod-Rewrite Bitte nicht installieren! Dieses System finktioniert nicht ohne!</div>';
    }

	echo $locale['020']."<br /><br />\n";
	echo $check_display."\n<br /><br />\n";
    echo $mod."<br /><br />";
	if ($write_check) {
		echo $locale['021']."<br />\n";
		echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
		echo "<input type='hidden' name='step' value='3' />\n";
		echo "<input type='submit' name='next' value='".$locale['007']."' class='btn btn-purple' />\n";
	} else {
		echo $locale['022']."<br />\n";
		echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
		echo "<input type='hidden' name='step' value='2' />\n";
		echo "<input type='submit' name='next' value='".$locale['025']."' class='btn btn-purple' />\n";
	}

    echo '</div>
        </form>
                </div>
            </div>
        </main>
    </div>
</div>';
render_footer();