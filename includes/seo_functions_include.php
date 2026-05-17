<?php
// SEO helper functions for Fusion integration
// Verwende vorhandene Fusion-Funktionen (dbquery, dbarray, dbresult)

if (!function_exists('seo_quote')) {
    function seo_quote(string $s) {
        return str_replace("'", "\\'", (string)$s);
    }
}

if (!function_exists('generate_slug')) {
    function generate_slug(string $text) {
        // Fall back to existing editurl() if available
        if (function_exists('editurl')) {
            $slug = editurl($text);
            if ($slug) return $slug;
        }

        $text = mb_strtolower($text, 'UTF-8');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', trim($text));
        $text = trim($text, '-');
        return $text !== '' ? $text : 'item';
    }
}

if (!function_exists('ensure_seo_table')) {
    function ensure_seo_table() {
        // Create table if it doesn't exist (best effort)
        $sql = "CREATE TABLE IF NOT EXISTS `seo_urls` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `slug` VARCHAR(255) NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `ref_id` INT NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug_unique` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if (function_exists('dbquery')) {
            @dbquery($sql);
        }
    }
}

if (!function_exists('save_seo_url')) {
    function save_seo_url(string $type,mixed $ref_id,string $title) {
        // Ensure table exists

        $base_slug = generate_slug($title);
        $slug = $base_slug;

        // Make unique: if slug exists for other item, append -n
        $i = 1;
        while (true) {
            $check_sql = "SELECT id, type, ref_id FROM seo_urls WHERE slug='" . seo_quote($slug) . "' LIMIT 1";
            $exists = false;
            if (function_exists('dbquery')) {
                $res = @dbquery($check_sql);
                if ($res && function_exists('dbrows') && dbrows($res) > 0) {
                    $row = function_exists('dbarray') ? dbarray($res) : null;
                    if ($row) {
                        // If same record (same type & ref_id) it's ok to reuse slug
                        if (isset($row['type']) && isset($row['ref_id']) && $row['type'] == $type && $row['ref_id'] == $ref_id) {
                            $exists = false;
                        } else {
                            $exists = true;
                        }
                    }
                }
            }
            if (!$exists) break;
            $slug = $base_slug . '-' . $i++;
        }

        // Insert or update
        $found = false;
        if (function_exists('dbquery')) {
            $sel = "SELECT id FROM seo_urls WHERE type='" . seo_quote($type) . "' AND ref_id='" . ((int)$ref_id) . "' LIMIT 1";
            $r = @dbquery($sel);
            if ($r && function_exists('dbrows') && dbrows($r) > 0) {
                $row = function_exists('dbarray') ? dbarray($r) : null;
                if ($row && isset($row['id'])) {
                    $found = $row['id'];
                }
            }

            if ($found) {
                $upd = "UPDATE seo_urls SET slug='" . seo_quote($slug) . "', updated_at=NOW() WHERE id=" . ((int)$found);
                @dbquery($upd);
            } else {
                $ins = "INSERT INTO seo_urls (slug, type, ref_id) VALUES ('" . seo_quote($slug) . "', '" . seo_quote($type) . "', '" . ((int)$ref_id) . "')";
                @dbquery($ins);
            }
        }

        return $slug;
    }
}

if (!function_exists('fusion_save_seo')) {
    function fusion_save_seo(string $type,mixed $ref_id,string $title) {
        $slug = function_exists('editurl') ? editurl($title) : generate_slug($title);
        if (!function_exists('dbquery')) return $slug;
        $type_s = preg_replace('/[^a-z0-9_\-]/i', '', (string)$type);
        $ref_i = (int)$ref_id;
        $res = @dbquery("SELECT id FROM seo_urls WHERE type='".$type_s."' AND ref_id='".$ref_i."' LIMIT 1");
        if ($res && function_exists('dbrows') && dbrows($res) > 0) {
            $rowid = function_exists('dbresult') ? dbresult($res, 0) : null;
            if ($rowid) dbquery("UPDATE seo_urls SET slug='".str_replace("'","\\'",$slug)."' WHERE id=".(int)$rowid);
        } else {
            dbquery("INSERT INTO seo_urls (slug, type, ref_id) VALUES ('".str_replace("'","\\'",$slug)."', '".str_replace("'","\\'",$type_s)."', '".$ref_i."')");
        }
        return $slug;
    }
}

if (!function_exists('seo_get_slug')) {
    function seo_get_slug(string $type, mixed $ref_id) {
        static $seo_slug_cache = [];
        $type_s = preg_replace('/[^a-z0-9_\-]/i', '', (string)$type);
        $ref_i = (int)$ref_id;
        if (isset($seo_slug_cache[$type_s][$ref_i])) {
            return $seo_slug_cache[$type_s][$ref_i];
        }
        $seo_slug_cache[$type_s][$ref_i] = null;
        if (!function_exists('dbquery')) {
            return null;
        }
        $res = @dbquery("SELECT slug FROM seo_urls WHERE type='".$type_s."' AND ref_id='".$ref_i."' LIMIT 1");
        if ($res && function_exists('dbrows') && dbrows($res) > 0) {
            $row = function_exists('dbarray') ? dbarray($res) : null;
            if ($row && isset($row['slug'])) {
                $seo_slug_cache[$type_s][$ref_i] = trim($row['slug'], '/');
            }
        }
        return $seo_slug_cache[$type_s][$ref_i];
    }
}

if (!function_exists('seo_url')) {
    /**
     * Summary of seo_url
     * @param string $type
     * @param mixed $ref_id
     * @param array $params
     * @return string
     */
    function seo_url(string $type, mixed $ref_id = 0, array $params = []) {
        $base = defined('BASEDIR') ? BASEDIR : '/';
        $slug = seo_get_slug($type, $ref_id);
        if ($slug) {
            $url = $base . ltrim($slug, '/');
        } else {
            $type_s = preg_replace('/[^a-z0-9_\-]/i', '', (string)$type);
            switch ($type_s) {
                case 'forum_thread':
                    $url = $base . 'forum/viewthread.php?thread_id=' . (int)$ref_id;
                    break;
                case 'forum_forum':
                case 'forum_cat':
                    $url = $base . 'forum/viewforum.php?forum_id=' . (int)$ref_id;
                    break;
                case 'forum_index':
                    $url = $base . 'forum/index.php';
                    break;
                default:
                    $url = $base;
                    break;
            }
        }
        if (!empty($params)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
        }
        return $url;
    }
}

if (!function_exists('editurl')) {
    function editurl(string $string) {
        $string = (string)$string;
        $string = mb_strtolower($string, 'UTF-8');
        $conv = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        if ($conv !== false) $string = $conv;
        $string = preg_replace('/[\/\?\\#%&:;"\'\(\)\[\]\{\}<\>\+\*=,.!@\$\^]/', '-', $string);
        $string = preg_replace('/[^a-z0-9\s-]/i', '', $string);
        $string = preg_replace('/[\s-]+/', '-', trim($string));
        return trim($string, '-');
    }
}