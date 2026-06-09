<?php
/**
 * Formulaire CREATE/EDIT pour sav86_card.php
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

if (!is_object($form)) {
    require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
    $form = new Form($db);
}

$mode = ($action == 'create') ? 'create' : 'edit';

llxHeader('', ($mode == 'create' ? 'Nouvelle intervention SAV86' : 'Modifier '.($obj->ref ?? '')));
if ($DEBUG_LIGHT) {
    print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;">';
    print '<strong>🔧 SAV86</strong> | Mode: '.strtoupper($mode).' | Debug: ON';
    print '</div>';
}
print load_fiche_titre($mode == 'create' ? "Créer une nouvelle intervention" : "Modifier l'intervention ".($obj->ref ?? ''));

print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'" name="form_sav86" id="form_sav86">';
print '<input type="hidden" name="action" value="'.($mode == 'create' ? 'add' : 'update').'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
if ($mode == 'edit') print '<input type="hidden" name="id" value="'.($obj->id ?? '').'">';

print '<div class="div-table-responsive-no-min"><table class="border centpercent">';

// Client
print '<tr><td class="fieldrequired">Client</td><td>';
print '<div class="client-field-wrapper">';
$searchFkSoc = ($mode == 'edit') ? ($obj->fk_soc ?? GETPOST('fk_soc','int')) : GETPOST('fk_soc','int');
print $form->select_company($searchFkSoc, 'fk_soc', '', 1, 0, 0, array(), 0, 0, 'minwidth300');
print '<a href="'.DOL_URL_ROOT.'/societe/card.php?leftmenu=thirdparties&action=create&type=c" class="btn-create-client" target="_blank">';
print img_picto('Nouveau client', 'add', 'class="paddingright"').'Nouveau client';
print '</a></div></td></tr>';

// Dates entrée/prévue
print '<tr><td class="fieldrequired">Date d\'entrée *</td><td>';
$val = ($mode == 'edit' && !empty($obj->date_entree)) ? $obj->date_entree : (($mode == 'create') ? dol_now() : GETPOST('date_entree','alpha'));
print $form->select_date($val, 'date_entree', 0, 0, 0, '', 1, 1);
print '</td></tr>';
print '<tr><td class="fieldrequired">Date prévue *</td><td>';
$val = ($mode == 'edit' && !empty($obj->date_prevue)) ? $obj->date_prevue : (($mode == 'create') ? dol_now() : GETPOST('date_prevue','alpha'));
print $form->select_date($val, 'date_prevue', 0, 0, 0, '', 1, 1);
print '</td></tr>';

// Priorité
print '<tr><td class="fieldrequired">Priorité *</td><td>';
$v = ($mode == 'edit') ? ($obj->indice_priorite ?? '') : GETPOST('indice_priorite','alpha');
print '<select name="indice_priorite" class="flat minwidth150">';
print '<option value="normal"'.($v == 'normal' ? ' selected' : '').'>Normal</option>';
print '<option value="urgent"'.($v == 'urgent' ? ' selected' : '').'>Urgent</option>';
print '</select></td></tr>';

// Problème
print '<tr><td class="fieldrequired">Problème *</td><td>';
$v = ($mode == 'edit') ? ($obj->probleme ?? '') : GETPOST('probleme','restricthtml');
print '<textarea name="probleme" rows="4" class="centpercent" required>'.dol_escape_htmltag($v, 0, 1).'</textarea></td></tr>';

// Type PC
print '<tr><td>Type PC</td><td>';
$v = ($mode == 'edit') ? ($obj->type_pc ?? '') : GETPOST('type_pc','alpha');
print '<input type="radio" name="type_pc" value="Tour" id="type_tour"'.($v == 'Tour' ? ' checked' : '').'> <label for="type_tour">Tour</label>';
print ' <input type="radio" name="type_pc" value="Portable" id="type_portable"'.($v == 'Portable' || (!$v && $mode == 'create') ? ' checked' : '').'> <label for="type_portable">Portable</label>';
print ' <input type="radio" name="type_pc" value="Mobile" id="type_mobile"'.($v == 'Mobile' ? ' checked' : '').'> <label for="type_mobile">Mobile</label>';
print '</td></tr>';

// Champs divers
print '<tr><td>Pièces jointes</td><td><input type="text" name="pieces_jointes" value="'.dol_escape_htmltag(($mode == 'edit') ? ($obj->pieces_jointes ?? '') : GETPOST('pieces_jointes','alpha')).'" class="minwidth300"></td></tr>';
print '<tr><td>Mot de passe</td><td><input type="text" name="Mdpasse" value="'.dol_escape_htmltag(($mode == 'edit') ? ($obj->Mdpasse ?? '') : GETPOST('Mdpasse','alpha')).'" class="minwidth300"></td></tr>';

// Format
print '<tr><td>Format</td><td>';
$v = ($mode == 'edit') ? ($obj->format ?? '') : GETPOST('format','alpha');
print '<select name="format" class="flat minwidth150">';
print '<option value="Aucun"'.($v == 'Aucun' || (!$v && $mode == 'create') ? ' selected' : '').'>Aucun</option>';
print '<option value="C seulement"'.($v == 'C seulement' ? ' selected' : '').'>C seulement</option>';
print '<option value="Tous"'.($v == 'Tous' ? ' selected' : '').'>Tous</option>';
print '</select></td></tr>';

// État
print '<tr><td class="fieldrequired">État *</td><td>';
$v = ($mode == 'edit') ? ($obj->etat ?? '') : GETPOST('etat','alpha');
print '<select name="etat" class="flat minwidth200" id="etat_select">';
print '<option value="PasCommence"'.($v == 'PasCommence' || (!$v && $mode == 'create') ? ' selected' : '').'>PC pas commencé</option>';
print '<option value="EnCours"'.($v == 'EnCours' ? ' selected' : '').'>PC en cours</option>';
print '<option value="Attente"'.($v == 'Attente' ? ' selected' : '').'>PC en attente</option>';
print '<option value="Fini"'.($v == 'Fini' ? ' selected' : '').'>PC fini</option>';
print '<option value="Parti"'.($v == 'Parti' ? ' selected' : '').'>Client venu chercher PC</option>';
print '</select></td></tr>';

// Garantie
print '<tr><td>Garantie</td><td>';
$v = ($mode == 'edit') ? ($obj->garantie ?? '') : GETPOST('garantie','alpha');
print '<select name="garantie" class="flat minwidth150">';
print '<option value="hors htp"'.($v == 'hors htp' || (!$v && $mode == 'create') ? ' selected' : '').'>hors htp</option>';
print '<option value="oui"'.($v == 'oui' ? ' selected' : '').'>oui</option>';
print '<option value="non"'.($v == 'non' ? ' selected' : '').'>non</option>';
print '<option value="en partie"'.($v == 'en partie' ? ' selected' : '').'>en partie</option>';
print '</select></td></tr>';

// Commercial / Technicien
print '<tr><td class="fieldrequired">Commercial *</td><td>';
$v = ($mode == 'edit') ? ($obj->fk_user_creat ?? '') : GETPOST('id_commercial','int');
print $form->select_dolusers($v, 'id_commercial', 1, '', 0, 'AND u.statut = 1', 0, 0, 0, 0, '', 0, 'minwidth300');
if (GETPOST('error_field','alpha') == 'commercial') print '<br><span style="color:red;font-weight:bold;">⚠️ Commercial requis</span>';
print '</td></tr>';
print '<tr><td>Technicien</td><td>';
$v = ($mode == 'edit') ? ($obj->fk_user_valid ?? '') : GETPOST('id_technicien','int');
print $form->select_dolusers($v, 'id_technicien', 1, '', 0, 'AND u.statut = 1', 0, 0, 0, 0, '', 0, 'minwidth300');
print '</td></tr>';

// Main d'œuvre / Matériel
print '<tr><td>Main d\'œuvre TTC</td><td><input type="number" step="0.01" name="nb_heure" value="'.($mode == 'edit' ? ($obj->nb_heure ?? '0') : GETPOST('nb_heure','alpha')).'" class="minwidth150" placeholder="0.00"></td></tr>';
print '<tr><td>Matériel rajouté</td><td><input type="text" name="materiel" value="'.dol_escape_htmltag(($mode == 'edit') ? ($obj->materiel ?? '') : GETPOST('materiel','alpha')).'" class="minwidth300"></td></tr>';
print '<tr><td>Facturation matériel</td><td><input type="number" step="0.01" name="prix_materiel" value="'.($mode == 'edit' ? ($obj->prix_materiel ?? '0') : GETPOST('prix_materiel','alpha')).'" class="minwidth150" placeholder="0.00"></td></tr>';

// Intervention
print '<tr><td>Intervention</td><td>';
$v = ($mode == 'edit') ? ($obj->intervention ?? '') : GETPOST('intervention','restricthtml');
print '<textarea name="intervention" rows="6" class="centpercent" placeholder="Décrivez les interventions réalisées...">'.dol_escape_htmltag($v, 0, 1).'</textarea></td></tr>';

// ✅ DATE FIN / DATE SORTIE (0 si vide pour champs vides + bouton Maintenant)
if ($mode == 'edit') {
    $val_fin = (!empty($obj->date_fin) && $obj->date_fin > 0) ? $obj->date_fin : 0;
    $val_sortie = (!empty($obj->date_sortie) && $obj->date_sortie > 0) ? $obj->date_sortie : 0;
    print '<tr><td>Date de fin</td><td>'.$form->select_date($val_fin, 'date_fin', 0, 0, 0, '', 1, 1).'</td></tr>';
    print '<tr><td>Date de sortie</td><td>'.$form->select_date($val_sortie, 'date_sortie', 0, 0, 0, '', 1, 1).'</td></tr>';
}

// Commentaire
print '<tr><td>Commentaire HTP</td><td>';
$v = ($mode == 'edit') ? ($obj->commentaire ?? '') : GETPOST('commentaire','restricthtml');
print '<textarea name="commentaire" rows="2" class="centpercent">'.dol_escape_htmltag($v, 0, 1).'</textarea>';
print '<br><span class="opacitymedium">Ce commentaire n\'apparaît pas au client</span></td></tr>';

print '</table></div>';
print '<br><div class="center tabsAction">';
print '<input type="submit" class="butAction" value="Enregistrer">';
print '&nbsp;<a class="butActionRefused" href="'.($mode == 'edit' ? $_SERVER["PHP_SELF"].'?action=view&id='.($obj->id ?? '') : 'sav86_list.php').'">Annuler</a>';
print '</div></form>';

// Script alerte mot de passe vide
if (!empty($conf->global->SAV86_ALERT_MDP_VIDE)) {
    print '<script>
    $("form[name=\'form_sav86\']").submit(function(e) {
        var mdp = $("input[name=\'Mdpasse\']").val().trim();
        if (mdp === "") {
            if (!confirm("⚠️ Le champ \\"Mot de passe\\" est vide.\\n\\nEst-ce volontaire ?")) {
                e.preventDefault(); return false;
            }
        }
        return true;
    });
    </script>';
}

// ✅ JS COMPLET : datepickers + validation + FIX DATE_FIN (sync au submit)
print '<script>
$(document).ready(function() {
    // Initialiser tous les datepickers
    $(".datepicker").datepicker({
        dateFormat: "dd/mm/yy",
        showOn: "button",
        buttonImage: "'.DOL_URL_ROOT.'/theme/eldy/img/calendar.png",
        buttonImageOnly: true,
        buttonText: "Choisir date"
    });
    
    // ✅ FIX DATE_FIN : Au submit, lire le champ texte et remplir les selects
    $("#form_sav86").on("submit", function(e) {
        // Date de fin
        var valFin = $("input[name=\'date_fin\']").val();
        if (valFin && valFin.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
            var p = valFin.split("/");
            $("select[name=\'date_fin_day\']").val(parseInt(p[0], 10));
            $("select[name=\'date_fin_month\']").val(parseInt(p[1], 10));
            $("select[name=\'date_fin_year\']").val(parseInt(p[2], 10));
            console.log("✅ Date fin envoyée: "+valFin);
        }
        // Date de sortie
        var valSortie = $("input[name=\'date_sortie\']").val();
        if (valSortie && valSortie.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
            var p = valSortie.split("/");
            $("select[name=\'date_sortie_day\']").val(parseInt(p[0], 10));
            $("select[name=\'date_sortie_month\']").val(parseInt(p[1], 10));
            $("select[name=\'date_sortie_year\']").val(parseInt(p[2], 10));
            console.log("✅ Date sortie envoyée: "+valSortie);
        }
        return true;
    });
    
    // Validation dates entrée/prévue
    $("#form_sav86").on("submit", function(e) {
        var dateEntreeStr = $("input[name=\'date_entree\']").val();
        var datePrevueStr = $("input[name=\'date_prevue\']").val();
        if (!dateEntreeStr || !datePrevueStr) {
            alert("Les dates d\'entrée et prévue sont obligatoires");
            e.preventDefault(); return false;
        }
        var parseDate = function(str) { var p = str.split("/"); return new Date(p[2], p[1]-1, p[0]); };
        var dateEntree = parseDate(dateEntreeStr);
        var datePrevue = parseDate(datePrevueStr);
        var priorite = $("select[name=\'indice_priorite\']").val();
        if (datePrevue < dateEntree) { alert("La date prévue ne peut pas être antérieure à la date d\'entrée"); e.preventDefault(); return false; }
        if (priorite == "normal") {
            var diffHeures = (datePrevue - dateEntree) / (1000 * 60 * 60);
            if (diffHeures < 48) { alert("Pour une priorité normale, la date prévue doit être au moins 48h après la date d\'entrée."); e.preventDefault(); return false; }
        }
        if (dateEntree.getTime() === datePrevue.getTime() && priorite != "urgent") {
            alert("Si la date d\'entrée est égale à la date prévue, la priorité doit être Urgent.");
            e.preventDefault(); return false;
        }
        return true;
    });
});
</script>';

// Styles CSS
print '<style>
.btn-create-client { display:inline-flex;align-items:center;justify-content:center;padding:6px 12px;margin-left:10px;background:#fff;color:#333;text-decoration:none;border-radius:4px;font-size:0.9em;font-weight:500;border:1px solid #ccc;transition:all 0.3s; }
.btn-create-client:hover { background:#f5f5f5;border-color:#999;color:#000; }
@media (max-width:768px) { .client-field-wrapper { display:flex;flex-wrap:wrap;align-items:center;gap:10px; } .client-field-wrapper .select_company_container { flex:1 1 auto;min-width:200px; } .btn-create-client { margin-left:0;flex:0 0 auto; } }
</style>';

llxFooter();
exit;