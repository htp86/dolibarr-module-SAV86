<?php
/**
 * Fiche intervention SAV86 - Router principal (Architecture split finale)
 * Version 20260507 Build 1757
 * /volume1/web/dolibarr_test/htdocs/custom/sav86/htdocs/sav86/sav86_card.php
 */

// ============================================================================
// BLOC DEBUG STANDARD (à conserver en tête de tous les fichiers PHP)
// ============================================================================
$PATHFILE = '/volume1/web/dolibarr_test/htdocs/custom/sav86/htdocs/sav86/sav86_card.php';
$VERSION = '20260507';
$BUILD = '1757';
$DEBUG_LIGHT = true;
$DEBUG_ERRORS = false;

if ($DEBUG_LIGHT) {
    print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;font-size:11px;">';
    print '<strong>📡 SAV86</strong> - Version '.$VERSION.' Build '.$BUILD;
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
// ============================================================================

// ============================================================================
// CHARGEMENT DOLIBARR
// ============================================================================
require '../../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once __DIR__.'/../../class/sav86.class.php';

// ✅ Inclusion des modules split
require_once __DIR__.'/core/sav86_card_helpers.inc.php';

// Variables globales
global $db, $conf, $langs, $user, $form;

// Paramètres
$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');

// ============================================================================
// DISPATCHER - Router minimal
// ============================================================================

// ✅ CREATE & EDIT → Formulaire externalisé
if ($action == 'create' || ($action == 'edit' && $id > 0)) {
    if ($action == 'edit' && $id > 0) {
        $obj = new HTPSav86($db);
        $obj->fetch($id);
    } else {
        $obj = null;
    }
    require_once __DIR__.'/core/sav86_card_form.inc.php';
    exit;
}

// ✅ VIEW → Affichage externalisé
if ($action == 'view' && $id > 0) {
    $obj = new HTPSav86($db);
    $obj->fetch($id);
    require_once __DIR__.'/core/sav86_card_view.inc.php';
    exit;
}

// ✅ ADD & UPDATE → Traitement externalisé
if ($action == 'add' || ($action == 'update' && $id > 0)) {
    if ($action == 'update' && $id > 0) {
        // 🔍 DEBUG DATE_FIN - À SUPPRIMER APRÈS TEST
        // Décommenter pour logger les valeurs POST reçues
        /*
        $log = '/tmp/sav86_update_'.date('Ymd').'.log';
        $msg = date('H:i:s').' | id='.(int)$id.
               ' | day_fin='.GETPOST('date_fin_day','alpha').
               ' | month_fin='.GETPOST('date_fin_month','alpha').
               ' | year_fin='.GETPOST('date_fin_year','alpha').
               ' | etat='.GETPOST('etat','alpha')."\n";
        file_put_contents($log, $msg, FILE_APPEND);
        */
        $obj = new HTPSav86($db);
        $obj->fetch($id);
    } else {
        $obj = null;
    }
    require_once __DIR__.'/core/sav86_card_actions.inc.php';
    exit;
}

// ✅ DELETE → Suppression (reste dans le router, trop simple pour externaliser)
if ($action == 'ask_delete' && $id > 0) {
    require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
    $form = new Form($db);
    
    if ($DEBUG_LIGHT) {
        print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;">';
        print '<strong>🔧 SAV86 - Version '.$VERSION.' Build '.$BUILD.'</strong> | Mode: ASK_DELETE | Debug: ON';
        print '</div>';
    }
    
    $obj = new HTPSav86($db);
    $obj->fetch($id);
    $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$id, 'Supprimer', 'Confirmer suppression de '.$obj->ref.' ?', 'confirm_delete', '', 0, 1);
    llxHeader('', 'Confirmation');
    print $formconfirm;
    llxFooter();
    exit;
}

if ($action == 'confirm_delete' && $id > 0) {
    if (empty(GETPOST('token', 'alpha')) || !preg_match('/^[a-f0-9]{32}$/', GETPOST('token', 'alpha'))) {
        setEventMessages("Token invalide", null, 'errors');
        header("Location: sav86_list.php");
        exit;
    }
    $obj = new HTPSav86($db);
    $obj->fetch($id);
    $result = $obj->delete($user);
    setEventMessages($result > 0 ? "Supprimée" : "Erreur: ".$obj->error, null, $result > 0 ? 'mesgs' : 'errors');
    header("Location: sav86_list.php");
    exit;
}

// ✅ Default redirect
header("Location: sav86_list.php");
exit;