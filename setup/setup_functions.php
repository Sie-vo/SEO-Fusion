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
if(!defined('IN_FUSION')) die('No direct access allowed');

function makefileopts(array $files, $selected = "") {
	$res = "";
	for ($i=0; $i < count($files); $i++) {
		$sel = ($selected == $files[$i] ? " selected='selected'" : "");
		$res .= "<option value='".$files[$i]."'$sel>".$files[$i]."</option>\n";
	}
	return $res;
}

// Clean URL Function, prevents entities in server globals
function cleanurl(string $url) {
	$bad_entities = array("&", "\"", "'", '\"', "\'", "<", ">", "(", ")", "*");
	$safe_entities = array("&amp;", "", "", "", "", "", "", "", "", "");
	$url = str_replace($bad_entities, $safe_entities, $url);
	return $url;
}

// Get Current URL
function getCurrentURL() {
	$s = (empty($_SERVER["HTTPS"]) ? "" : (($_SERVER["HTTPS"] == "on") ? "s" : ""));
	$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.(str_replace(basename(cleanurl($_SERVER['PHP_SELF'])), "", $_SERVER['REQUEST_URI']));
}

function strleft(string $s1,string $s2) {
	return substr($s1, 0, strpos($s1, $s2));
}

function makefilelist(string $folder,string $filter, $sort = true, $type = "files", $ext_filter = "") {
	$res = array();
	$filter = explode("|", $filter);
	if ($type == "files" && !empty($ext_filter)) {
		$ext_filter = explode("|", strtolower($ext_filter));
	}
	$temp = opendir($folder);
	while ($file = readdir($temp)) {
		if ($type == "files" && !in_array($file, $filter)) {
			if (!empty($ext_filter)) {
				if (!in_array(substr(strtolower(stristr($file, '.')), +1), $ext_filter) && !is_dir($folder.$file)) { $res[] = $file; }
			} else {
				if (!is_dir($folder.$file)) { $res[] = $file; }
			}
		} elseif ($type == "folders" && !in_array($file, $filter)) {
			if (is_dir($folder.$file)) { $res[] = $file; }
		}
	}
	closedir($temp);
	if ($sort) { sort($res); }
	return $res;
}