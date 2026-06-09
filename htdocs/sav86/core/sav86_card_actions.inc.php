<?php
/**
 * Traitement ADD/UPDATE pour sav86_card.php
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

global $db, $conf, $langs, $user, $form;

if (ob_get_level()) ob_end_clean();

// Charger l'objet si UPDATE
if ($action == 'update' && !empty($id) && empty($obj)) {
    $obj = new HTPSav86($db);
    $obj->fetch($id);
}

// Validation champs obligatoires
$errors = array();
if (GETPOST('fk_soc', 'int') <= 0) $errors[] = 'Client manquant';
if (empty(GETPOST('date_entree', 'alpha'))) $errors[] = 'Date d\'entrée manquante';
if (empty(GETPOST('date_prevue', 'alpha'))) $errors[] = 'Date prévue manquante';
if (empty(GETPOST('probleme', 'restricthtml'))) $errors[] = 'Problème manquant';
if (GETPOST('id_commercial', 'int') <= 0) { $errors[] = 'Commercial manquant'; $_GET['error_field'] = 'commercial'; }

// Validation dates
$date_entree = parseAndValidateDate(GETPOST('date_entree', 'alpha'));
$date_prevue = parseAndValidateDate(GETPOST('date_prevue', 'alpha'));
if (!$date_entree) $errors[] = 'Date d\'entrée invalide';
if (!$date_prevue) $errors[] = 'Date prévue invalide';
if ($date_entree && $date_prevue) {
    if ($date_prevue < $date_entree) $errors[] = 'Date prévue < date entrée';
    if (GETPOST('indice_priorite','alpha') == 'normal' && ($date_prevue - $date_entree) < (48 * 3600)) $errors[] = 'Priorité normale : min 48h';
    if ($date_prevue == $date_entree && GETPOST('indice_priorite','alpha') != 'urgent') $errors[] = 'Date entrée = prévue → Priorité Urgent requise';
}

// Si erreurs : ré-afficher formulaire
if (!empty($errors)) {
    foreach ($errors as $error) setEventMessages($error, null, 'errors');
    $action = ($action == 'add') ? 'create' : 'edit';
    if ($action == 'edit') { $obj = new HTPSav86($db); $obj->fetch($id); } else { $obj = null; }
    require_once __DIR__.'/sav86_card_form.inc.php';
    exit;
}

// Initialisation objet
if ($action == 'add') {
    $obj = new HTPSav86($db);
    $obj->fk_soc = GETPOST('fk_soc', 'int');
    $obj->fk_user_creat = GETPOST('id_commercial', 'int');
} else {
    $obj->fk_user_valid = GETPOST('id_technicien', 'int');
}

// Champs communs
$obj->date_entree = $date_entree;
$obj->date_prevue = $date_prevue;
$obj->indice_priorite = GETPOST('indice_priorite', 'alpha');
$obj->probleme = GETPOST('probleme', 'restricthtml');
$obj->type_pc = GETPOST('type_pc', 'alpha');
$obj->pieces_jointes = GETPOST('pieces_jointes', 'alpha');
$obj->Mdpasse = GETPOST('Mdpasse', 'alpha');
$obj->format = GETPOST('format', 'alpha');

// ✅ Gestion robuste date_fin (CORRECTION BUG)
$day_fin = (int)GETPOST('date_fin_day', 'int');
$month_fin = (int)GETPOST('date_fin_month', 'int');
$year_fin = (int)GETPOST('date_fin_year', 'int');
if ($day_fin > 0 && $month_fin > 0 && $year_fin > 0) {
    $obj->date_fin = dol_mktime(0, 0, 0, $month_fin, $day_fin, $year_fin);
    $obj->etat = 'Fini'; // Auto-état
} else {
    $obj->date_fin = null;
    $obj->etat = GETPOST('etat', 'alpha');
}

// Autres champs
$obj->garantie = GETPOST('garantie', 'alpha');
$obj->fk_user_creat = ($action == 'add') ? GETPOST('id_commercial', 'int') : $obj->fk_user_creat;
$obj->fk_user_valid = GETPOST('id_technicien', 'int');
$obj->nb_heure = price2num(GETPOST('nb_heure', 'alpha'), 2);
$obj->materiel = GETPOST('materiel', 'alpha');
$obj->prix_materiel = price2num(GETPOST('prix_materiel', 'alpha'), 2);
$obj->intervention = GETPOST('intervention', 'restricthtml');

// Date de sortie (même logique)
$day_sortie = (int)GETPOST('date_sortie_day', 'int');
$month_sortie = (int)GETPOST('date_sortie_month', 'int');
$year_sortie = (int)GETPOST('date_sortie_year', 'int');
$obj->date_sortie = ($day_sortie > 0 && $month_sortie > 0 && $year_sortie > 0) ? dol_mktime(0, 0, 0, $month_sortie, $day_sortie, $year_sortie) : null;

$obj->commentaire = GETPOST('commentaire', 'restricthtml');

// Exécution
$result = ($action == 'add') ? $obj->create($user) : $obj->update($user);
$redirect = ($action == 'add') ? 'create' : 'edit';

if ($result > 0) {
    setEventMessages(($action == 'add') ? "Intervention ".$obj->ref." créée" : "Intervention mise à jour avec succès", null, 'mesgs');
    header("Location: ".$_SERVER["PHP_SELF"]."?action=view&id=".$obj->id);
    exit;
} else {
    setEventMessages("Erreur: ".$obj->error, null, 'errors');
    $action = $redirect;
    if ($action == 'edit' && empty($obj)) { $obj = new HTPSav86($db); $obj->fetch($id); }
    require_once __DIR__.'/sav86_card_form.inc.php';
    exit;
}