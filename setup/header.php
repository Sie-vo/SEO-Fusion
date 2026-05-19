<?php
/*-------------------------------------------------------+
| SEO-Fusion based on PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: setup/header.php
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

function render_header(string $title){
    global $locale;
    $html =  '<!DOCTYPE html>
<html lang="'.$locale['xml_lang'].'">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">';

    $html .= '<title>'. $title .'</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
            --primary-purple: #4B0082; /* Indigo/Dunkellila */
            --light-purple: #f3e5f5;
        }
        body { background-color: #f8f9fa; }
        .navbar, .footer { background-color: var(--primary-purple) !important; color: white; }
        .sidebar { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; height: fit-content; }
        .main-content { background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd; min-height: 600px; }
        .btn-purple { background-color: var(--primary-purple); color: white; }
        .btn-purple:hover { background-color: #380062; color: white; }
        .nav-link { color: rgba(255,255,255,0.8); }
        .nav-link:hover { color: white; }
        .alert-info, h5, h2, .fas-style{background: #8e24aa; color: azure;}
        h2 {text-align: center;}
       .fa-style { color: #8e24aa;}
    </style>
</head>
<body>';
return $html;
}