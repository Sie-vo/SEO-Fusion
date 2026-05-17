<?php 
/*-------------------------------------------------------+

| SEO-Fusion based on Content Management System PHP Fusion

| Copyright (C) 2002 - 2011 Nick Jones

| http://www.php-fusion.co.uk/

| SEO-Fusion Copyright by Sievo

| https://sievo.de

+--------------------------------------------------------+

| Filename: error-404.php

| Author: Dennis Vorpahl (Sievo)

| Modified for SEO-Fusion by Dennis Vorpahl (Sievo)

+--------------------------------------------------------+

| This program is released as free software under the

| Affero GPL license. You can redistribute it and/or

| modify it under the terms of this license which you

| can read by viewing the included agpl.txt or online

| at www.gnu.org/licenses/agpl.html. Removal of this

| copyright header is strictly prohibited without

| written permission from the original author(s).

+--------------------------------------------------------*/
header("HTTP/1.0 404 Not Found");

include_once "maincore.php";

include THEME."theme.php";
require_once THEMES."templates/header.php";

unset($_SERVER['QUERY_STRING']);

add_to_head("<meta http-equiv='refresh' content='5; URL=".$settings['siteurl']."'>");

opentable('Seite nicht gefunden');
echo "<center>Du wirst in 5 Sekunden auf die Startseite umgeleitet.</center>
<br>
<img src='".$settings['siteurl'].IMAGES."ajax-loader.gif' border='0' alt='Seite nicht gefunden' /><br />
<br>
<center>Falls dies nicht automatisch geschieht, klicke bitte <a href='".$settings['siteurl']."'>HIER</a> !</center>";
closetable();

require_once THEMES."templates/footer.php";