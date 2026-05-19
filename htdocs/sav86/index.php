<?php
/**
 * Point d'entrée module SAV86
 */

$PATHFILE = '/volume1/web/dolibarr_test/htdocs/custom/sav86/htdocs/sav86/index.php';
$VERSION = '20260416';
$BUILD = '1731';
$DEBUG_LIGHT = true;
$DEBUG_ERRORS = false;
if ($DEBUG_LIGHT) {
    print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;font-size:11px;">';
    print '<strong>📡 Import Ciel 8.11</strong> - Version '.$VERSION.' Build '.$BUILD;
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
