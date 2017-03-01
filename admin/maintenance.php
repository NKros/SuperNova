<?php

/**
 * maintenance.php
 *
 * @version 1.0
 * @copyright 2009 by Gorlum for http://oGame.Triolan.COM.UA
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

global $lang, $user;

AdminCheckLevel(AUTH_LEVEL_ADMINISTRATOR);

$script = '
<script type="text/javascript">
$(document).ready(function() {
  $.post("admin/ajax_maintenance.php", function(result) {
    $("#admin_message").html(result);
  }, "json" );
});
</script>';

AdminMessage($script . '<img src=design/images/progressbar.gif><br>' . $lang['sys_wait'], $lang['adm_maintenance_title']);
