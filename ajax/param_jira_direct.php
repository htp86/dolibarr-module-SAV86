<?php
$PATHFILE = __FILE__;
$VERSION = date('Ymd', filemtime(__FILE__));
$BUILD = date('Hi', filemtime(__FILE__));
$DEBUG_LIGHT = false;
$DEBUG_ERRORS = false;

if ($DEBUG_LIGHT) {
    print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;font-size:11px;">';
    print '<strong>SAV86</strong> - Version '.$VERSION.' Build '.$BUILD;
    print ' | '.htmlspecialchars($PATHFILE);
    print '</div>';
}
if ($DEBUG_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Inclusion Dolibarr pour lire la constante SAV86_JIRA_API_TOKEN en base
if (!defined('DOL_INC_USED')) {
    $res = 0;
    if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
    if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
    if (!$res) { die("Include of main fails"); }
}
define('JIRA_BASE', 'https://htpmultimedia.atlassian.net');
define('JIRA_USER_EMAIL', 'sav86@htpmultimedia.fr');
define('JIRA_API_TOKEN', dolibarr_get_const($db, 'SAV86_JIRA_API_TOKEN', 0)); // Stockee via admin/setup.php (roue dentee)
define('JIRA_PROJECT_KEY', 'T86');
define('JIRA_ISSUE_TYPE_ID', '10003');