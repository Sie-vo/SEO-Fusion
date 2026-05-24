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

function createRandomPrefix ($length = 5) {
		$chars = ["abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ", "123456789"];
		$count = [(strlen($chars[0]) - 1), (strlen($chars[1]) - 1)];
		$prefix = "";
		for ($i = 0; $i < $length; $i++) {
			$type = mt_rand(0, 1);
			$prefix .= substr($chars[$type], mt_rand(0, $count[$type]), 1);
		}
		return $prefix;
	}
	$db_prefix = "fusion".createRandomPrefix()."_";
	$cookie_prefix  = "fusion".createRandomPrefix()."_";
	$db_host = (isset($_POST['db_host']) ? stripinput(trim($_POST['db_host'])) : "localhost");
	$db_user = (isset($_POST['db_user']) ? stripinput(trim($_POST['db_user'])) : "");
	$db_name = (isset($_POST['db_name']) ? stripinput(trim($_POST['db_name'])) : "");
    /**
     * @todo Bei vollständiger pdo Umstellung raus
     */
	$db_driver = (isset($_POST['db_driver']) ? stripinput(trim($_POST['db_driver'])) : "pdo");
	$db_prefix = (isset($_POST['db_prefix']) ? stripinput(trim($_POST['db_prefix'])) : $db_prefix);
	$cookie_prefix = (isset($_POST['cookie_prefix']) ? stripinput(trim($_POST['cookie_prefix'])) : $cookie_prefix);
	$db_error = (isset($_POST['db_error']) && isnum($_POST['db_error']) ? $_POST['db_error'] : "0");
	$field_class = ["", "", "", "", ""];
	if ($db_error > "0") {
		$field_class[2] = " alert alert-danger";
		if ($db_error == 1) {
			$field_class[1] = " alert alert-danger";
			$field_class[2] = " alert alert-danger";
		} elseif ($db_error == 2) {
			$field_class[3] = " alert alert-danger";
		} elseif ($db_error == 3) {
			$field_class[4] = " alert alert-danger";
		} elseif ($db_error == 7) {
			if ($db_host == "") { $field_class[0] = " alert alert-danger"; }
			if ($db_user == "") { $field_class[1] = " alert alert-danger"; }
			if ($db_name == "") { $field_class[3] = " alert alert-danger"; }
			if ($db_prefix == "") { $field_class[4] = " alert alert-danger"; }
		}
	}

	echo $locale['030']."<br /><br />\n";
    echo "<div class='mb-3>\n";
	echo "<label for='db_host'>".$locale['031']."</label>\n";
	echo "<input type='text' value='".$db_host."' name='db_host' class='form-control".$field_class[0]."' id='db_host' required/>\n</div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='db_user'>".$locale['032']."</label>\n";
	echo "<input type='text' value='".$db_user."' name='db_user' class='form-control".$field_class[1]."' id='db_user' required />\n</div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='db_pass'>".$locale['033']."</label>\n";
	echo "<input type='password' value='' name='db_pass' class='form-control".$field_class[2]."' id='db_pass' required />\n</div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='db_name'>".$locale['034']."</label>\n";
	echo "<input type='text' value='".$db_name."' name='db_name' class='form-control".$field_class[3]."' id='db_name' required />\n</div>\n";
	// New: PDO or MySQLi , Never ever
    /** @todo bei Zeiten wieder raus, bei kompletter Umstellung auf pdo */
	echo "<input type='hidden' name='db_driver' value='pdo'>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='db_prefix'>".$locale['035']."</label>\n";
	echo "<input type='text' value='".$db_prefix."' name='db_prefix' class='form-control".$field_class[4]."' id='db_prefix' required />\n</div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='cookie_prefix'>".$locale['036']."</td>\n";
	echo "<input type='text' value='".$cookie_prefix."' name='cookie_prefix' class='form-control' id='cookie_prefix' />\n</div>\n";
	echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
	echo "<input type='hidden' name='step' value='4' />\n";
    echo "<div class='mb-3>\n";
	echo "<input type='submit' name='next' value='".$locale['007']."' class='btn btn-purple' />\n</div>\n";

    echo '</div>
        </form>
                </div>
            </div>
        </main>
    </div>
</div>';
echo render_footer();