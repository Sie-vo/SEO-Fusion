<?php
/*-------------------------------------------------------+
| SEO-Fusion based on PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: setup/footer.php
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

function render_footer(string $footer = '') {
    $html = $footer;
    $html .= '<footer class="footer mt-5 py-3 text-center">
    <div class="container">
        <span>&copy; 2026 SEO-Fusion | Erstellt mit <i class="fas fa-heart"> </i><br />';
    $html .= "Powered by";
    $html .= " <a target=\"_blank\" href=\"http://www.php-fusion.co.uk/\">PHP-Fusion</a> ";
    $html .= "copyright &copy; 2002 - ".date("Y")." by Nick Jones. Released as free software without warranties under";
    $html .= " <a href=\"http://www.gnu.org/licenses/agpl-3.0.html\" target=\"_blank\">GNU Affero GPL</a> ";        
    $html .= '</span>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function(){
        $("#toggleInfo").click(function(){
            alert("jQuery ist bereit! Hier könnten Statistiken geladen werden.");
        });
    });
</script></body></html>';
    return $html;
}