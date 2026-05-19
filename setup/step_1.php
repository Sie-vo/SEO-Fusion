<?php
/*-------------------------------------------------------+
| SEO-Fusion based on PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: setup/step_1.php
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
$locale_files = makefilelist("locale/", ".svn|.|..", true, "folders");
$locale_list = makefileopts($locale_files);
echo $locale['010']."<br /><br />";
echo "<select name='localeset' class='form-select'>\n";
echo $locale_list."</select><br /><br />\n";
echo $locale['011']."<br />\n";
echo "<input type='hidden' name='step' value='2' />\n";
echo "<input type='submit' name='next' value='".$locale['007']."' class='btn btn-purple' />\n";
echo '</div>
        </form>
                </div>
            </div>
        </main>
    </div>
</div>';
render_footer();