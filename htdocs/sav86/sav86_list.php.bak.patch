<?php
/**
 * Liste des interventions SAV86
 * CORRECTION : Lien vers mode VIEW + Recherche texte multi-champs
 * /volume1/web/dolibarr_test/htdocs/custom/sav86/htdocs/sav86/sav86_list.php
 */

// Affichage version pour debug développement
define('SAV86_VERSION', '20260421');
define('SAV86_BUILD', '1213');
$DEBUG_BOOL = true;
$DEBUG_ERRORS = false;

if ($DEBUG_BOOL) {
    print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;">';
	print '<strong>🔧 SAV86 - Version '.SAV86_VERSION.' Build '.SAV86_BUILD.'</strong>';
	print ' | Mode: LIST | Debug: ON';
	print '</div>';
    }


require '../../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once __DIR__.'/../../class/sav86.class.php';

// Load translation files
$langs->load("sav86@sav86", DOL_DOCUMENT_ROOT.'/custom/sav86/langs');

$form = new Form($db);

// ============================================================================
// PARAMÈTRES DE RECHERCHE ET FILTRES
// ============================================================================
$search_ref = GETPOST('search_ref', 'alpha');
$search_soc = GETPOST('search_soc', 'int');
$search_etat = GETPOST('search_etat', 'alpha');
$search_text = GETPOST('search_text', 'alpha');  // ← NOUVEAU : Recherche texte libre

// Pagination
$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : 50;
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if (empty($page) || $page == -1) { $page = 0; }
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

// Tri
$sortfield = GETPOST('sortfield', 'aZ09comma');
$sortorder = GETPOST('sortorder', 'aZ09comma');
if (empty($sortfield)) { $sortfield = "s.rowid"; }
if (empty($sortorder)) { $sortorder = "DESC"; }

// Filtre par défaut : exclure les fiches "Parti"
if (empty($search_etat)) {
    $search_etat = 'all_except_parti';
}

// ============================================================================
// AFFICHAGE EN-TÊTE
// ============================================================================
llxHeader('', $langs->trans("SAV86Interventions"));

// ============================================================================
// MENU DE NAVIGATION (Bouton Recherche supprimé)
// ============================================================================
print '<div class="sav86-menu" style="margin-bottom: 20px; padding: 10px; background: #f0f0f0; border-radius: 5px;">';
print '<div class="tabsAction">';
print '<a class="butAction" href="'.DOL_URL_ROOT.'/societe/list.php?leftmenu=thirdparties">'.$langs->trans("ClientList").'</a>';
print '<a class="butAction" href="sav86_card.php?action=create">'.$langs->trans("NewIntervention").'</a>';
// Bouton Recherche supprimé - recherche intégrée dans les filtres
print '<a class="butAction" href="sav86_list.php?limit=50">'.$langs->trans("InterventionList").'</a>';
print '<a class="butAction" href="../../sav86version.php">'.$langs->trans("Version").'</a>';
print '</div>';
print '</div>';

// ============================================================================
// TITRE
// ============================================================================
print load_fiche_titre($langs->trans("SAV86InterventionList"));

// ============================================================================
// FILTRES - VERSION COMPACTE AVEC RECHERCHE TEXTE
// ============================================================================
print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'" class="filter-form">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="page" value="'.$page.'">';

print '<div class="div-table-responsive-no-min">';
print '<table class="border centpercent">';
print '<tr class="liste_titre">';

// Ref - Label + champ dans le même td
print '<td><strong>Réf.</strong> <input type="text" name="search_ref" value="'.$db->escape($search_ref).'" class="minwidth100" placeholder="Réf."></td>';

// Client - Label + champ dans le même td
print '<td><strong>Client</strong> '.$form->select_company($search_soc, 'search_soc', '', 1, 0, 0, array(), 0, 0, 'minwidth150').'</td>';

// État - Label + select dans le même td
print '<td><strong>État</strong> ';
print '<select name="search_etat" class="minwidth150">';
print '<option value="all_except_parti"'.($search_etat == 'all_except_parti' ? ' selected' : '').'>'. $langs->trans("AllExceptPickedUp").'</option>';
print '<option value="all"'.($search_etat == 'all' ? ' selected' : '').'>'. $langs->trans("All").'</option>';
print '<option value="PasCommence"'.($search_etat == 'PasCommence' ? ' selected' : '').'>'. $langs->trans("StatusNotStarted").'</option>';
print '<option value="EnCours"'.($search_etat == 'EnCours' ? ' selected' : '').'>'. $langs->trans("StatusInProgress").'</option>';
print '<option value="Attente"'.($search_etat == 'Attente' ? ' selected' : '').'>'. $langs->trans("StatusWaiting").'</option>';
print '<option value="Fini"'.($search_etat == 'Fini' ? ' selected' : '').'>'. $langs->trans("StatusDone").'</option>';
print '<option value="Parti"'.($search_etat == 'Parti' ? ' selected' : '').'>'. $langs->trans("StatusPickedUp").'</option>';
print '</select>';
print '</td>';

// Recherche texte - Label + champ dans le même td
print '<td><strong>Rechercher</strong> <input type="text" name="search_text" value="'.$db->escape($search_text).'" class="minwidth200" placeholder="Mot dans fiches"></td>';

// Boutons
print '<td class="right">';
print '<input type="submit" class="button small" value="'.$langs->trans("Filter").'">';
print ' ';
print '<a class="button small" href="sav86_list.php?limit=50">'.$langs->trans("Reset").'</a>';
print '</td>';

print '</tr>';
print '</table>';
print '</div>';
print '</form>';
print '<br>';

// ============================================================================
// REQUÊTE SQL
// ============================================================================
$sql = "SELECT s.rowid, s.ref, s.date_entree, s.date_prevue, s.date_fin, s.date_sortie,";
$sql .= " s.etat, s.indice_priorite, s.fk_soc, s.fk_user_creat, s.fk_user_valid,";
$sql .= " s.prix_materiel, s.nb_heure,";
$sql .= " s.probleme, s.intervention, s.materiel, s.commentaire,";
$sql .= " soc.nom as socname, soc.rowid as socid";
$sql .= " FROM ".MAIN_DB_PREFIX."sav86_fiche as s";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe as soc ON s.fk_soc = soc.rowid";

// Filtre entity
$entity_filter = getEntity('sav86_fiche');
if (empty($entity_filter)) $entity_filter = '1';
$sql .= " WHERE s.entity IN (".$entity_filter.")";

// Filtres dynamiques
if (!empty($search_ref)) {
    $sql .= " AND s.ref LIKE '%".$db->escape($search_ref)."%'";
}
if (!empty($search_soc) && $search_soc > 0) {
    $sql .= " AND s.fk_soc = ".(int)$search_soc;
}
if (!empty($search_etat) && $search_etat != 'all' && $search_etat != 'all_except_parti') {
    $sql .= " AND s.etat = '".$db->escape($search_etat)."'";
}
if ($search_etat == 'all_except_parti') {
    $sql .= " AND s.etat != 'Parti'";
}

// ← NOUVEAU : Recherche texte dans tous les champs configurables
if (!empty($search_text)) {
    $search_term = $db->escape($search_text);
    $sql .= " AND (";
    $sql .= " s.ref LIKE '%".$search_term."%'";
    $sql .= " OR s.probleme LIKE '%".$search_term."%'";
    $sql .= " OR s.intervention LIKE '%".$search_term."%'";
    $sql .= " OR s.materiel LIKE '%".$search_term."%'";
    $sql .= " OR s.commentaire LIKE '%".$search_term."%'";
    $sql .= " OR s.Mdpasse LIKE '%".$search_term."%'";
    $sql .= " OR s.pieces_jointes LIKE '%".$search_term."%'";
    $sql .= " OR s.type_pc LIKE '%".$search_term."%'";
    $sql .= " OR s.indice_priorite LIKE '%".$search_term."%'";
    $sql .= ")";
}

// Count total pour pagination
$sqlcount = $sql;
$resqlcount = $db->query($sqlcount);
$total = ($resqlcount ? $db->num_rows($resqlcount) : 0);

// Complete query with order and limit
$sql .= " ORDER BY ".$sortfield." ".$sortorder;
$sql .= " LIMIT ".$limit." OFFSET ".$offset;

$resql = $db->query($sql);

// ============================================================================
// DEBUG SQL (optionnel)
// ============================================================================
if ($DEBUG_ERRORS) {
    print '<div style="background:#fff3cd;padding:10px;margin:10px;border:1px solid #ffc107;">';
    print '<strong>🔍 DEBUG SQL :</strong><br>';
    print '<pre style="background:#f8f9fa;padding:10px;overflow:auto;font-size:11px;">';
    print 'Requête SQL : '.htmlspecialchars($sql)."\n\n";
    print 'Total count : '.$total."\n";
    print 'Lignes retournées : '.($resql ? $db->num_rows($resql) : 0)."\n";
    print 'Entity filter : '.$entity_filter."\n";
    print 'Search text : '.htmlspecialchars($search_text)."\n";
    print '</pre>';
    print '</div>';
}

if (!$resql) {
    dol_print_error($db);
    exit;
}

// ============================================================================
// AFFICHAGE LISTE
// ============================================================================
print '<div class="div-table-responsive-no-min">';
print '<table class="liste centpercent">';

// CSS pour centrer les titres de colonnes
print '<style>
table.liste tr.liste_titre th,
table.liste tr.liste_titre td {
    text-align: center !important;
}
</style>';

// En-têtes
$param = ''; // ← CORRIGÉ: variable manquante
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "s.ref", "", $param, "", $sortfield, $sortorder);
print_liste_field_titre($langs->trans("Client"), $_SERVER["PHP_SELF"], "soc.nom", "", $param, "", $sortfield, $sortorder);
print_liste_field_titre($langs->trans("DateEntry"), $_SERVER["PHP_SELF"], "s.date_entree", "", $param, "", $sortfield, $sortorder);
print_liste_field_titre($langs->trans("DatePlanned"), $_SERVER["PHP_SELF"], "s.date_prevue", "", $param, "", $sortfield, $sortorder);
print_liste_field_titre($langs->trans("DateEnd"), $_SERVER["PHP_SELF"], "s.date_fin", "", $param, "", $sortfield, $sortorder);
print_liste_field_titre($langs->trans("Status"), $_SERVER["PHP_SELF"], "s.etat", "", $param, "", $sortfield, $sortorder);
print_liste_field_titre($langs->trans("Priority"), $_SERVER["PHP_SELF"], "s.indice_priorite", "", $param, "", $sortfield, $sortorder);
print '<th class="center">'.$langs->trans("Actions").'</th>';
print '</tr>';

// Lignes
if ($db->num_rows($resql) > 0) {
    while ($obj = $db->fetch_object($resql)) {
        
        // Couleur de fond selon le statut (CORRIGÉ: méthode inexistante remplacée)
        $bgcolor = '#ffffff';
        if ($obj->etat == 'PasCommence') $bgcolor = '#96A6BE';
        elseif ($obj->etat == 'EnCours') $bgcolor = '#FFCC66';
        elseif ($obj->etat == 'Attente') $bgcolor = '#FF6666';
        elseif ($obj->etat == 'Fini') $bgcolor = '#99CC00';
        
        // Priorité urgente
        $classUrgent = ($obj->indice_priorite == 'urgent') ? ' class="urgent"' : '';
        
        print '<tr class="oddeven"'.$classUrgent.'>';
        
        // Ref - ✅ AJOUT DE action=view
        print '<td'.($obj->indice_priorite == 'urgent' ? ' style="background:#ffcccc;"' : '').'>';
        print '<a href="sav86_card.php?action=view&id='.$obj->rowid.'">'.$db->escape($obj->ref).'</a>';
        print '</td>';
        
        // Client
        print '<td class="center">';  // ← AJOUT de class="center"
        if ($obj->socid > 0) {
            $soc = new Societe($db);
            $soc->fetch($obj->socid);
            print $soc->getNomUrl(1);
        } else {
            print $langs->trans("Unknown");
        }
        print '</td>';
        
        // Date entrée
        print '<td class="center">'.dol_print_date($obj->date_entree, 'day').'</td>';
        
        // Date prévue
        print '<td class="center">'.dol_print_date($obj->date_prevue, 'day').'</td>';
        
        // Date fin
        print '<td class="center">'.dol_print_date($obj->date_fin, 'day').'</td>';
        
        // État (avec couleur)
        print '<td class="center" style="background:'.$bgcolor.'; color:'.(in_array($obj->etat, array('Fini')) ? 'white' : 'black').';">';
        print HTPSav86::LibStatut($obj->etat, 0);
        print '</td>';
        
        // Priorité
        print '<td class="center">';
        print ($obj->indice_priorite == 'urgent' ? '<span style="color:red; font-weight:bold;">'.$langs->trans("Urgent").'</span>' : $langs->trans("Normal"));
        print '</td>';
        
        // Actions
        print '<td class="center">';
        // Print
        print '<a href="sav86_print.php?id='.$obj->rowid.'" target="_blank" title="'.$langs->trans("Print").'">';
        print img_picto($langs->trans("Print"), 'printer');
        print '</a> ';
        // Edit
        print '<a href="sav86_card.php?action=edit&id='.$obj->rowid.'" title="'.$langs->trans("Edit").'">';
        print img_picto($langs->trans("Edit"), 'edit');
        print '</a> ';
        // Delete ← CORRIGÉ: ajout du token CSRF
        print '<a href="sav86_card.php?action=confirm_delete&id='.$obj->rowid.'&token='.newToken().'" title="'.$langs->trans("Delete").'" onclick="return confirm(\''.$langs->trans("ConfirmDelete").'\')">';
        print img_picto($langs->trans("Delete"), 'delete');
        print '</a>';
        print '</td>';
        
        print '</tr>';
    }
} else {
    print '<tr><td colspan="8" class="center">'.$langs->trans("NoRecordFound").'</td></tr>';
}

print '</table>';
print '</div>';

// Pagination
if ($total > $limit) {
    print '<br>';
    print_barre_liste($langs->trans("Interventions"), $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $total, $limit);
}

// Footer
llxFooter();
$db->close();