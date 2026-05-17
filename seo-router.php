<?php
// Clean Fusion-compatible SEO router 
if (file_exists(__DIR__ . '/maincore.php')) {
    include_once __DIR__ . '/maincore.php';
}

$slug = isset($_GET['slug']) ? trim($_GET['slug'], "/") : '';
if ($slug === '') {
    include __DIR__ . $settings['opening_page'];
    exit;
}

function seo_quote(string $s) {
    return str_replace("'", "\\'", (string)$s);
}

function seo_resolve_path(string $root, string $slug): ?string {
    $slug = trim(str_replace("\0", '', $slug), "/\\");
    if ($slug === '') {
        $indexFile = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'index.php';
        return is_file($indexFile) ? realpath($indexFile) : null;
    }
    if (strpos($slug, '..') !== false) {
        return null;
    }

    $candidate = $root . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $slug);
    if (is_file($candidate)) {
        $resolved = realpath($candidate);
    } elseif (is_file($candidate . '.php')) {
        $resolved = realpath($candidate . '.php');
    } else {
        $indexCandidate = rtrim($candidate, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'index.php';
        $resolved = is_file($indexCandidate) ? realpath($indexCandidate) : false;
    }

    if (!$resolved) {
        return null;
    }

    $rootReal = realpath($root);
    if (!$rootReal || strpos($resolved, $rootReal) !== 0) {
        return null;
    }

    return $resolved;
}

function seo_include_route(string $root, string $slug): bool {
    $target = seo_resolve_path($root, $slug);
    if (!$target) {
        return false;
    }
    include $target;
    return true;
}

function seo_dispatch_generic(string $slug): bool {
    $slug = trim($slug, "/");
    if (preg_match('#^forum(?:/(.*))?$#i', $slug, $m)) {
        return seo_include_route(FORUM, $m[1] ?? '');
    }
    if (preg_match('#^infusions?(?:/(.*))?$#i', $slug, $m)) {
        return seo_include_route(INFUSIONS, $m[1] ?? '');
    }
    return false;
}

$found = false;
$type = null;
$ref  = null;
$sql = "SELECT seo_type, ref_id FROM seo_urls WHERE slug='" . seo_quote($slug) . "' LIMIT 1";
if (function_exists('dbquery')) {
    $res = @dbquery($sql);
    if ($res && function_exists('dbrows') && dbrows($res) > 0 && function_exists('dbarray')) {
        $row = dbarray($res);
        $type = isset($row['seo_type']) ? $row['seo_type'] : null;
        $ref  = isset($row['ref_id']) ? $row['ref_id'] : null;
        $found = true;
    }
}

if (!$found) {
    if (preg_match('/^news-(\d+)/', $slug, $m)) { $_GET['readmore'] = (int)$m[1]; include __DIR__ . '/news.php'; exit; }
    if (preg_match('/^(artikel|article)-?(\d+)/i', $slug, $m)) { $_GET['article_id'] = (int)$m[2]; include __DIR__ . '/articles.php'; exit; }
    if (preg_match('/^benutzerprofil-(\d+)/i', $slug, $m)) { $_GET['lookup'] = (int)$m[1]; include __DIR__ . '/profile.php'; exit; }
    if (preg_match('/^download-(?:details-)?(\d+)/i', $slug, $m)) { $_GET['download_id'] = (int)$m[1]; include __DIR__ . '/downloads.php'; exit; }
    if (preg_match('/^foto-(\d+)/i', $slug, $m)) { $_GET['photo_id'] = (int)$m[1]; include __DIR__ . '/photogallery.php'; exit; }
    if (preg_match('/^seite-(\d+)/i', $slug, $m)) { $_GET['page_id'] = (int)$m[1]; include __DIR__ . '/viewpage.php'; exit; }
    if (seo_dispatch_generic($slug)) {
        exit;
    }
    http_response_code(404);
    include __DIR__ . '/error-404.php';
    exit;
}

switch (strtolower($type)) {
    case 'news':
        $_GET['readmore'] = (int)$ref; include __DIR__ . '/news.php'; break;
    case 'article':
        $_GET['article_id'] = (int)$ref; include __DIR__ . '/articles.php'; break;
    case 'profile':
        $_GET['lookup'] = (int)$ref; include __DIR__ . '/profile.php'; break;
    case 'download':
        $_GET['download_id'] = (int)$ref; include __DIR__ . '/downloads.php'; break;
    case 'photo':
        $_GET['photo_id'] = (int)$ref; include __DIR__ . '/photogallery.php'; break;
    case 'custompage':
        $_GET['page_id'] = (int)$ref; include __DIR__ . '/viewpage.php'; break;
    case 'forum_thread':
        $_GET['thread_id'] = (int)$ref; include __DIR__ . '/forum/viewthread.php'; break;
    case 'forum_cat':
    case 'forum_forum':
        $_GET['forum_id'] = (int)$ref; include __DIR__ . '/forum/viewforum.php'; break;
    case 'forum_index':
        include __DIR__ . '/forum/index.php'; break;
    case 'infusion':
        if (seo_include_route(INFUSIONS, $slug)) {
            break;
        }
        // fall through to 404 if no matching infusion file exists
    default:
        if (seo_dispatch_generic($slug)) {
            break;
        }
        http_response_code(404);
        include __DIR__ . '/error-404.php';
}

exit;
