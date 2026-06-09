<?php
/**
 * Affichage VIEW pour sav86_card.php
 */

$PATHFILE = __FILE__;
$VERSION = date('Ymd', filemtime(__FILE__));
$BUILD = date('Hi', filemtime(__FILE__));
$DEBUG_LIGHT = false;
$DEBUG_ERRORS = false;

// Déclaration des globales nécessaires
global $db, $conf, $langs, $user, $form;

if ($DEBUG_LIGHT) {
    print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;font-size:11px;">';
    print '<strong>SAV86</strong> - Version '.$VERSION.' Build '.$BUILD;
    print ' | '.htmlspecialchars($PATHFILE);
    print '</div>';
}

llxHeader('', 'Intervention '.$obj->ref);
print load_fiche_titre("Intervention ".$obj->ref);

// Charger les objets liés
$soc = new Societe($db);
$soc->fetch($obj->fk_soc);

$commercial = new User($db);
if ($obj->fk_user_creat > 0) $commercial->fetch($obj->fk_user_creat);

$technicien = new User($db);
if ($obj->fk_user_valid > 0) $technicien->fetch($obj->fk_user_valid);

// Affichage de la fiche
print '<div class="fiche centpercent">';
print '<div class="div-table-responsive-no-min">';
print '<table class="border centpercent">';

print '<tr><td class="titlefield">Client</td><td>'.$soc->getNomUrl(1).'</td></tr>';
print '<tr><td>Référence</td><td><b>'.$obj->ref.'</b></td></tr>';
print '<tr><td>Date d\'entrée</td><td>'.dol_print_date($obj->date_entree, 'day').'</td></tr>';
print '<tr><td>Date prévue</td><td>'.dol_print_date($obj->date_prevue, 'day').'</td></tr>';
print '<tr><td>Priorité</td><td>';
print $obj->indice_priorite == 'urgent' ? '<span style="color:red; font-weight:bold;">Urgent</span>' : 'Normal';
print '</td></tr>';
print '<tr><td>État</td><td>'.HTPSav86::LibStatut($obj->etat, 0).'</td></tr>';
print '<tr><td>Type PC</td><td>'.$obj->type_pc.'</td></tr>';
print '<tr><td>Problème</td><td>'.nl2br(dol_escape_htmltag($obj->probleme, 0, 1)).'</td></tr>';

if (!empty($obj->pieces_jointes)) print '<tr><td>Pièces jointes</td><td>'.dol_escape_htmltag($obj->pieces_jointes).'</td></tr>';
if (!empty($obj->Mdpasse)) print '<tr><td>Mot de passe</td><td>'.dol_escape_htmltag($obj->Mdpasse).'</td></tr>';
if (!empty($obj->format)) print '<tr><td>Format</td><td>'.dol_escape_htmltag($obj->format).'</td></tr>';
if (!empty($obj->garantie)) print '<tr><td>Garantie</td><td>'.dol_escape_htmltag($obj->garantie).'</td></tr>';

if ($commercial->id > 0) print '<tr><td>Commercial</td><td>'.$commercial->getNomUrl(1).'</td></tr>';
if ($technicien->id > 0) print '<tr><td>Technicien</td><td>'.$technicien->getNomUrl(1).'</td></tr>';

if (!empty($obj->intervention)) print '<tr><td>Intervention</td><td>'.nl2br(dol_escape_htmltag($obj->intervention, 0, 1)).'</td></tr>';
if ($obj->date_fin) print '<tr><td>Date de fin</td><td>'.dol_print_date($obj->date_fin, 'day').'</td></tr>';
if ($obj->date_sortie) print '<tr><td>Date de sortie</td><td>'.dol_print_date($obj->date_sortie, 'day').'</td></tr>';

if ($obj->nb_heure > 0) print '<tr><td>Main d\'œuvre TTC</td><td>'.price($obj->nb_heure).' €</td></tr>';
if (!empty($obj->materiel)) print '<tr><td>Matériel</td><td>'.dol_escape_htmltag($obj->materiel).'</td></tr>';
if ($obj->prix_materiel > 0) print '<tr><td>Prix matériel</td><td>'.price($obj->prix_materiel).' €</td></tr>';

if (!empty($obj->commentaire)) {
    print '<tr><td>Commentaire interne</td><td><i>'.nl2br(dol_escape_htmltag($obj->commentaire, 0, 1)).'</i></td></tr>';
}

print '</table>';
print '</div>';
print '</div>';

// Boutons d'action
print '<br><div class="center tabsAction">';
print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&id='.$obj->id.'">Modifier</a> ';
print '<a class="butAction" href="sav86_print.php?id='.$obj->id.'" target="_blank">Imprimer</a> ';
print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?action=ask_delete&id='.$obj->id.'">Supprimer</a> ';
print '<a class="butAction" href="sav86_list.php">Retour liste</a>';
print '</div>';

llxFooter();
exit;
