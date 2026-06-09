<?php
/**
 * Point d'entrée module SAV86
 */

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
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}


// Redirection vers la liste des interventions
header("Location: sav86_list.php");
exit;
