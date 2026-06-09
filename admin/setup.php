<?php
/**
 * Page de configuration du module SAV86
 */

$PATHFILE = __FILE__;
$VERSION = date('Ymd', filemtime(__FILE__));
$BUILD = date('Hi', filemtime(__FILE__));
$DEBUG_LIGHT = true;
$DEBUG_ERRORS = false;

if ($DEBUG_LIGHT) {
	print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;font-size:11px;">';
	print '<strong>SAV86</strong> - Version '.$VERSION.' Build '.$BUILD;
	print ' | '.htmlspecialchars($PATHFILE);
	print '</div>';
}

// Activation du debug complet SI activé
if ($DEBUG_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load Dolibarr environment
$res = 0;
if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";

// DÉCLARATION EXPLICITE DES GLOBALES
global $db, $conf, $langs, $user;

// Load translation files
$langs->load("sav86@sav86", DOL_DOCUMENT_ROOT.'/custom/sav86/langs');
$langs->load("admin");

// Protection
if (!$user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');
$value = GETPOST('value', 'alpha');

// ============================================================================
// TRAITEMENT DES ACTIONS DE CONFIGURATION
// ============================================================================
if ($action == 'update') {
    // Configuration JIRA
    if (GETPOSTISSET('SAV86_JIRA_ENABLED')) {
        $res = dolibarr_set_const($db, "SAV86_JIRA_ENABLED", GETPOST('SAV86_JIRA_ENABLED', 'int'), 'chaine', 0, '', $conf->entity);
        if ($res < 0) setEventMessages("Erreur mise à jour JIRA enabled", null, 'errors');
    }

    $jira_fields = array(
        'SAV86_JIRA_BASE_URL',
        'SAV86_JIRA_PROJECT_KEY', 
        'SAV86_JIRA_ISSUE_TYPE',
        'SAV86_JIRA_USER_EMAIL',
        'SAV86_JIRA_API_TOKEN',
        'SAV86_JIRA_ASSIGNEES'
    );

    foreach ($jira_fields as $field) {
        if (GETPOSTISSET($field)) {
            $value = GETPOST($field, 'restricthtml');
            
            // Masquer le token si vide (pour ne pas l'écraser)
            if ($field == 'SAV86_JIRA_API_TOKEN' && empty($value)) {
                continue;
            }
            
            $res = dolibarr_set_const($db, $field, $value, 'chaine', 0, '', $conf->entity);
            if ($res < 0) setEventMessages("Erreur mise à jour ".$field, null, 'errors');
        }
    }
    
    // Conditions générales
    if (GETPOSTISSET('SAV86_CONDITIONS_GENERALES')) {
        $res = dolibarr_set_const($db, "SAV86_CONDITIONS_GENERALES", GETPOST('SAV86_CONDITIONS_GENERALES', 'restricthtml'), 'chaine', 0, '', $conf->entity);
        if ($res < 0) setEventMessages("Erreur mise à jour conditions", null, 'errors');
        else setEventMessages("Conditions générales mises à jour", null, 'mesgs');
    }
    
    // Toggle affichage CGV
    if (GETPOSTISSET('SAV86_AFFICHER_CGV')) {
        $res = dolibarr_set_const($db, "SAV86_AFFICHER_CGV", GETPOST('SAV86_AFFICHER_CGV', 'int'), 'chaine', 0, '', $conf->entity);
        if ($res < 0) setEventMessages("Erreur mise à jour toggle CGV", null, 'errors');
    }
    
    // Alerte mot de passe vide
    if (GETPOSTISSET('SAV86_ALERT_MDP_VIDE')) {
        $res = dolibarr_set_const($db, "SAV86_ALERT_MDP_VIDE", GETPOST('SAV86_ALERT_MDP_VIDE', 'int'), 'chaine', 0, '', $conf->entity);
        if ($res < 0) setEventMessages("Erreur mise à jour alerte MDP", null, 'errors');
    }
    
    // Email destinataire SMS
    if (GETPOSTISSET('SAV86_SMS_EMAIL')) {
        $sms_email = GETPOST('SAV86_SMS_EMAIL', 'email');
        if (!empty($sms_email) && filter_var($sms_email, FILTER_VALIDATE_EMAIL)) {
            $res = dolibarr_set_const($db, "SAV86_SMS_EMAIL", $sms_email, 'chaine', 0, '', $conf->entity);
            if ($res < 0) setEventMessages("Erreur mise à jour email SMS", null, 'errors');
            else setEventMessages("Email SMS mis à jour : ".$sms_email, null, 'mesgs');
        } elseif (empty($sms_email)) {
            $res = dolibarr_set_const($db, "SAV86_SMS_EMAIL", 'sms86@htpmultimedia.fr', 'chaine', 0, '', $conf->entity);
            setEventMessages("Email SMS réinitialisé à la valeur par défaut", null, 'mesgs');
        } else {
            setEventMessages("Email SMS invalide", null, 'errors');
        }
    }
    
    // Informations de contact
    $contact_fields = array('SAV86_CONTACT_TEL', 'SAV86_CONTACT_EMAIL', 'SAV86_CONTACT_JOURS', 'SAV86_CONTACT_HORAIRES');
    foreach ($contact_fields as $field) {
        if (GETPOSTISSET($field)) {
            $res = dolibarr_set_const($db, $field, GETPOST($field, 'alpha'), 'chaine', 0, '', $conf->entity);
            if ($res < 0) setEventMessages("Erreur mise à jour ".$field, null, 'errors');
        }
    }
    
    // Redirection pour éviter resoumission
    header("Location: ".$_SERVER["PHP_SELF"]);
    exit;
}

// ============================================================================
// AFFICHAGE PAGE DE CONFIGURATION
// ============================================================================
llxHeader('', $langs->trans("SAV86Setup"));

print load_fiche_titre($langs->trans("SAV86Configuration"), '', 'sav86@sav86');

print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';

// ============================================================================
// SECTION 1 : Conditions générales
// ============================================================================
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("SAV86ConditionsGenerales").'</td></tr>';

print '<tr><td>'.$langs->trans("SAV86AfficherPage2").'</td><td>';
print '<input type="checkbox" name="SAV86_AFFICHER_CGV" value="1"'.(getDolGlobalInt('SAV86_AFFICHER_CGV') ? ' checked' : '').'> ';
print $langs->trans("SAV86AfficherPage2Help");
print '</td></tr>';

print '<tr><td class="tdtop">'.$langs->trans("SAV86ConditionsGeneralesText").'</td><td>';
print '<textarea name="SAV86_CONDITIONS_GENERALES" rows="10" class="centpercent" style="font-family:monospace;font-size:0.9em;">';
$cgv_text = getDolGlobalString('SAV86_CONDITIONS_GENERALES');
$cgv_text = str_replace(array('\r\n', '\n', '\r'), "\n", $cgv_text);
print dol_escape_htmltag($cgv_text, 0, 1);
print '</textarea>';
print '<br><span class="opacitymedium">'.$langs->trans("SAV86ConditionsGeneralesHelp").'</span>';
print '</td></tr>';

// ============================================================================
// SECTION 2 : Alertes & Notifications
// ============================================================================
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("SAV86Alertes").'</td></tr>';

print '<tr><td>'.$langs->trans("SAV86AlertMdpVide").'</td><td>';
print '<input type="checkbox" name="SAV86_ALERT_MDP_VIDE" value="1"'.(getDolGlobalInt('SAV86_ALERT_MDP_VIDE') ? ' checked' : '').'> ';
print $langs->trans("SAV86AlertMdpVideHelp");
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86SmsEmail").'</td><td>';
print '<input type="email" name="SAV86_SMS_EMAIL" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_SMS_EMAIL', 'sms86@htpmultimedia.fr')).'" class="minwidth300" placeholder="sms86@htpmultimedia.fr">';
print '<br><span class="opacitymedium">'.$langs->trans("SAV86SmsEmailHelp").'</span>';
print '</td></tr>';

// ============================================================================
// SECTION 3 : Informations de contact
// ============================================================================
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("SAV86Contact").'</td></tr>';

print '<tr><td>'.$langs->trans("SAV86ContactTel").'</td><td>';
print '<input type="text" name="SAV86_CONTACT_TEL" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_CONTACT_TEL')).'" class="minwidth200">';
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86ContactEmail").'</td><td>';
print '<input type="email" name="SAV86_CONTACT_EMAIL" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_CONTACT_EMAIL')).'" class="minwidth200">';
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86ContactJours").'</td><td>';
print '<input type="text" name="SAV86_CONTACT_JOURS" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_CONTACT_JOURS')).'" class="minwidth200" placeholder="Lundi-Vendredi">';
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86ContactHoraires").'</td><td>';
print '<input type="text" name="SAV86_CONTACT_HORAIRES" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_CONTACT_HORAIRES')).'" class="minwidth200" placeholder="9h-12h / 14h-18h">';
print '</td></tr>';

// ============================================================================
// SECTION 4 : Intégration JIRA
// ============================================================================
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans("SAV86JiraIntegration").'</td></tr>';

print '<tr><td>'.$langs->trans("SAV86JiraEnabled").'</td><td>';
print '<input type="checkbox" name="SAV86_JIRA_ENABLED" value="1"'.(getDolGlobalInt('SAV86_JIRA_ENABLED') ? ' checked' : '').'> ';
print $langs->trans("SAV86JiraEnabledHelp");
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86JiraBaseUrl").'</td><td>';
print '<input type="url" name="SAV86_JIRA_BASE_URL" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_JIRA_BASE_URL')).'" class="minwidth400" placeholder="https://htpmultimedia.atlassian.net">';
print '<br><span class="opacitymedium">URL de base de votre instance Jira</span>';
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86JiraProjectKey").'</td><td>';
print '<input type="text" name="SAV86_JIRA_PROJECT_KEY" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_JIRA_PROJECT_KEY')).'" class="minwidth100" placeholder="T86">';
print '<br><span class="opacitymedium">Clé du projet (ex: T86)</span>';
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86JiraIssueType").'</td><td>';
print '<input type="text" name="SAV86_JIRA_ISSUE_TYPE" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_JIRA_ISSUE_TYPE', 'Task')).'" class="minwidth150" placeholder="Task">';
print '<br><span class="opacitymedium">Type d\'issue (Task, Story, Bug...)</span>';
print '</td></tr>';

print '<tr><td>'.$langs->trans("SAV86JiraUserEmail").'</td><td>';
print '<input type="email" name="SAV86_JIRA_USER_EMAIL" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_JIRA_USER_EMAIL')).'" class="minwidth300" placeholder="sav86@htpmultimedia.fr">';
print '<br><span class="opacitymedium">Email du compte de service pour l\'API</span>';
print '</td></tr>';

print '<tr><td class="tdtop">'.$langs->trans("SAV86JiraApiToken").'</td><td>';
print '<input type="password" name="SAV86_JIRA_API_TOKEN" value="'.dol_escape_htmltag(getDolGlobalString('SAV86_JIRA_API_TOKEN')).'" class="minwidth400" placeholder="Votre token API Jira">';
print '<br><span class="opacitymedium">Token d\'application Jira (ne sera pas affiché après sauvegarde)</span>';
print '</td></tr>';

print '<tr><td class="tdtop">'.$langs->trans("SAV86JiraAssignees").'</td><td>';
print '<textarea name="SAV86_JIRA_ASSIGNEES" rows="6" class="centpercent" style="font-family:monospace;font-size:0.85em;" placeholder="Nom Prénom|accountId&#10;Nom2 Prénom2|accountId2">';
// ✅ CORRECTION : Enlève l'échappement automatique de Dolibarr
$assignees_text = getDolGlobalString('SAV86_JIRA_ASSIGNEES');
$assignees_text = stripslashes($assignees_text);
print $assignees_text;
print '</textarea>';
print '<br><span class="opacitymedium">Liste des utilisateurs assignables (un par ligne)<br>Format : <strong>Nom Prénom|accountId</strong><br>Exemple :<br>Joël PICOU|712020:cf19dee6-5f64-4010-8efc-790b920f9a9a<br>VENTE86|712020:b2b4d9a5-cab7-42fc-95e6-36b76b592403</span>';
print '</td></tr>';

print '</table>';
print '</div>';

print '<br><div class="center">';
print '<input type="submit" class="butAction" value="'.$langs->trans("Save").'">';
print ' <a class="butActionRefused" href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("Cancel").'</a>';
print ' <a class="butAction" href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToList").'</a>';
print '</div>';

print '</form>';

llxFooter();