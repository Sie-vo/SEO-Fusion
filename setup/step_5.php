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

$username = (isset($_POST['username']) ? stripinput(trim($_POST['username'])) : "");
	$email = (isset($_POST['email']) ? stripinput(trim($_POST['email'])) : "");
	$error_pass = (isset($_POST['error_pass']) && isnum($_POST['error_pass']) ? $_POST['error_pass'] : "0");
	$error_name = (isset($_POST['error_name']) && isnum($_POST['error_name']) ? $_POST['error_name'] : "0");
	$error_mail = (isset($_POST['error_mail']) && isnum($_POST['error_mail']) ? $_POST['error_mail'] : "0");

	$field_class = array("", "", "", "", "", "");
	if ($error_pass == "1" || $error_name == "1" || $error_mail == "1") {
		$field_class = array("", " alert alert-danger", " alert alert-danger", " alert alert-danger", " alert alert-danger", "");
		if ($error_name == 1) { $field_class[0] = " alert alert-danger"; }
		if ($error_mail == 1) { $field_class[5] = " alert alert-danger"; }
	}

	echo $locale['060']."<br /><br />\n";
	echo "<div class='mb-3>\n";
	echo "<label for='username'>".$locale['061']."</label>\n";
	echo "<input type='text' name='username' value='".$username."' maxlength='30' class='form-control".$field_class[0]."' id='username' required /></div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='password1'>".$locale['062']."</label>\n";
	echo "<input type='password' name='password1' maxlength='64' class='form-control".$field_class[1]."' id='password1' required /></div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='password2'>".$locale['063']."</label>\n";
	echo "<input type='password' name='password2' maxlength='64' class='form-control".$field_class[2]."' id='password2' required /></div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='admin_password1>".$locale['064']."</td>\n";
	echo "<input type='password' name='admin_password1' maxlength='64' class='form-control".$field_class[3]."' id='admin_password1' required /></div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='admin_password2>".$locale['065']."</td>\n";
	echo "<input type='password' name='admin_password2' maxlength='64' class='form-control".$field_class[4]."' id='admin_password2' required /></div>\n";
    echo "<div class='mb-3>\n";
	echo "<label for='email'>".$locale['066']."</td>\n";
	echo "<input type='text' name='email' value='".$email."' maxlength='100' class='form-control".$field_class[5]."' id='email' required /></div>\n";
	echo "<input type='hidden' name='localeset' value='".stripinput($_POST['localeset'])."' />\n";
	echo "<input type='hidden' name='step' value='6' />\n";
    echo "<div class='mb-3>\n";
	echo "<input type='submit' name='next' value='".$locale['007']."' class='btn btn-purple' />\n</div>\n";

    echo '</div>
        </form>
                </div>
            </div>
        </main>
    </div>
</div>';
render_footer();