<?php
/*
  Simple script to create SEO slugs and store them in `seo_urls`.
  Usage (browser): seo-save.php?type=news&id=123
  Usage (batch):  seo-save.php?mode=batch
  Use befor you create new content
*/

if (file_exists(__DIR__ . '/maincore.php')) include_once __DIR__ . '/maincore.php';
if (file_exists(__DIR__ . '/includes/seo-functions_include.php')) include_once __DIR__ . 'includes/seo-functions_include.php';

// Simple output helper
function echo_line(mixed $s) { echo htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "<br>\n"; }

$mode = isset($_GET['mode']) ? $_GET['mode'] : null;
// Checke on SEO Tabelle in Datenbank angelegt ist
// wenn nicht dann wird sie gleich angelegt
ensure_seo_table();

// wandle alle Inhalte in SEO URL um.
if ($mode === 'batch') {
    echo_line('Batch-Mode gestartet');

    // News
    if (defined('DB_NEWS')) {
        $res = dbquery("SELECT news_id, news_subject FROM " . DB_NEWS);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('news', $row['news_id'], $row['news_subject']);
            echo_line("news: {$row['news_id']} -> {$slug}");
        }
    }

    // Articles
    if (defined('DB_ARTICLES')) {
        $res = dbquery("SELECT article_id, article_subject FROM " . DB_ARTICLES);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('article', $row['article_id'], $row['article_subject']);
            echo_line("article: {$row['article_id']} -> {$slug}");
        }
    }

    // Custom pages
    if (defined('DB_CUSTOM_PAGES')) {
        $res = dbquery("SELECT page_id, page_title FROM " . DB_CUSTOM_PAGES);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('custompage', $row['page_id'], $row['page_title']);
            echo_line("page: {$row['page_id']} -> {$slug}");
        }
    }

    // Users (profiles)
    if (defined('DB_USERS')) {
        $res = dbquery("SELECT user_id, user_name FROM " . DB_USERS);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('profile', $row['user_id'], $row['user_name']);
            echo_line("profile: {$row['user_id']} -> {$slug}");
        }
    }

    // Downloads
    if (defined('DB_DOWNLOADS')) {
        $res = dbquery("SELECT download_id, download_title FROM " . DB_DOWNLOADS);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('download', $row['download_id'], $row['download_title']);
            echo_line("download: {$row['download_id']} -> {$slug}");
        }
    }

    // Photos
    if (defined('DB_PHOTOS')) {
        $res = dbquery("SELECT photo_id, photo_title FROM " . DB_PHOTOS);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('photo', $row['photo_id'], $row['photo_title']);
            echo_line("photo: {$row['photo_id']} -> {$slug}");
        }
    }

    // Forum threads
    if (defined('DB_THREADS')) {
        $res = dbquery("SELECT thread_id, thread_subject FROM " . DB_THREADS);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('forum_thread', $row['thread_id'], $row['thread_subject']);
            echo_line("forum_thread: {$row['thread_id']} -> {$slug}");
        }
    }

    // Forums / categories
    if (defined('DB_FORUMS')) {
        $res = dbquery("SELECT forum_id, forum_name FROM " . DB_FORUMS);
        while ($row = dbarray($res)) {
            $slug = save_seo_url('forum_forum', $row['forum_id'], $row['forum_name']);
            echo_line("forum_forum: {$row['forum_id']} -> {$slug}");
        }
    }

    // Forum index alias (optional)
    if (defined('DB_FORUMS')) {
        $slug = save_seo_url('forum_index', 0, 'Forum');
        echo_line("forum_index: 0 -> {$slug}");
    }

    echo_line('Batch fertig');
    exit;
}

// Single save mode
$type = isset($_GET['type']) ? $_GET['type'] : null;
$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($type && ($id > 0 || $type === 'forum_index')) {
    switch ($type) {
        case 'news':
            $res = dbquery("SELECT news_subject FROM " . DB_NEWS . " WHERE news_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['news_subject'];
            break;
        case 'article':
            $res = dbquery("SELECT article_subject FROM " . DB_ARTICLES . " WHERE article_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['article_subject'];
            break;
        case 'custompage':
            $res = dbquery("SELECT page_title FROM " . DB_CUSTOM_PAGES . " WHERE page_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['page_title'];
            break;
        case 'profile':
            $res = dbquery("SELECT user_name FROM " . DB_USERS . " WHERE user_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['user_name'];
            break;
        case 'download':
            $res = dbquery("SELECT download_title FROM " . DB_DOWNLOADS . " WHERE download_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['download_title'];
            break;
        case 'photo':
            $res = dbquery("SELECT photo_title FROM " . DB_PHOTOS . " WHERE photo_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['photo_title'];
            break;
        case 'forum_thread':
            $res = dbquery("SELECT thread_subject FROM " . DB_THREADS . " WHERE thread_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['thread_subject'];
            break;
        case 'forum_forum':
            $res = dbquery("SELECT forum_name FROM " . DB_FORUMS . " WHERE forum_id='" . intval($id) . "' LIMIT 1");
            $row = dbarray($res);
            $title = $row['forum_name'];
            break;
        case 'forum_index':
            $title = 'Forum';
            break;
        default:
            $title = null;
    }

    if (!$title) { echo_line('Eintrag nicht gefunden oder leerer Titel'); exit; }

    $slug = save_seo_url($type, $id, $title);
    echo_line("Gespeichert: {$slug}");
    exit;
}

// If nothing else, show usage
echo_line('Verwendung: seo-save.php?type=news&id=123 (für einzelne Seiten) oder seo-save.php?mode=batch (für den gesammten Content');
