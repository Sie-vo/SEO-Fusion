<?php
/*-------------------------------------------------------+
| SEO-Fusion based on PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: setup/index.php
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
define("FUSION_SELF", basename($_SERVER['PHP_SELF']));
define("IN_FUSION", true);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('setup_functions.php');

if (isset($_POST['localeset']) && file_exists("../locale/".$_POST['localeset']) && is_dir("locale/".$_POST['localeset'])) {
	include "../locale/".$_POST['localeset']."/setup.php";
} else {
	$_POST['localeset'] = "English";
	include "../locale/English/setup.php";
}

if ((isset($_POST['step']) && $_POST['step'] == "7") || (isset($_GET['step']) && $_GET['step'] == "7")) {
	header("Location: index.php");
}

if (!isset($_POST['step']) || $_POST['step'] == "" || $_POST['step'] == "1") {
    $step = '1';
}else {
    $step = $_POST["step"];
}

switch ($step) {
    case "1":
        require_once('header.php');
        require_once("footer.php");
        require_once("step_1.php");
        exit;
    case "2":
        require_once("header.php");
        require_once("footer.php");
        require_once("step_2.php");
        exit;
    case "3":
        require_once("header.php");
        require_once("footer.php");
        require_once("step_3.php");
        exit;
    case "4":
         require_once("header.php");
        require_once("footer.php");
        require_once("step_4.php");
        exit;
    case "5":
        require_once("header.php");
        require_once("footer.php");
        require_once("step_5.php");
        exit;
    case "6":
        require_once("header.php");
        require_once("footer.php");
        require_once "step_6.php";
        exit;
    default:
        require_once("header.php");
        require_once("footer.php");
        require_once("step_1.php");
        exit;
}