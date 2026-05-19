<?php
/**
 * Impression reçu intervention SAV86
 * CORRECTION : Logique checkbox CGV + Signature page 2
 * /volume1/web/dolibarr_test/htdocs/custom/sav86/htdocs/sav86/sav86_print.php
 */

define('SAV86_VERSION', '20260421');
define('SAV86_BUILD', '1059');

$DEBUG_BOOL = false;
$DEBUG_ERRORS = false;

if ($DEBUG_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require '../../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once __DIR__.'/../../class/sav86.class.php';

$id = GETPOST('id', 'int');
if (empty($id)) {
    dol_print_error('', 'ID manquant');
    exit;
}

$obj = new HTPSav86($db);
$result = $obj->fetch($id);
if ($result <= 0) {
    dol_print_error($db, $obj->error);
    exit;
}

$soc = new Societe($db);
$soc->fetch($obj->fk_soc);

$commercial = new User($db);
if ($obj->fk_user_creat > 0) $commercial->fetch($obj->fk_user_creat);

$technicien = new User($db);
if ($obj->fk_user_valid > 0) $technicien->fetch($obj->fk_user_valid);

$semaine = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
$jour_prevue = '';
if (!empty($obj->date_prevue) && is_numeric($obj->date_prevue)) {
    $jour_prevue = $semaine[date('w', $obj->date_prevue)];
}

// ✅ CORRECTION : Utilisation de getDolGlobalInt pour gérer correctement la valeur "0"
// Si la constante est "0", getDolGlobalInt retourne 0. Si "1", retourne 1.
$afficher_cgv = (getDolGlobalInt('SAV86_AFFICHER_CGV', 1) == 1);

// Lecture des constantes de configuration
$conditions_generales = getDolGlobalString('SAV86_CONDITIONS_GENERALES', '');

// Infos contact (avec valeurs par défaut si non définies)
$contact_tel = getDolGlobalString('SAV86_CONTACT_TEL', '05.49.88.30.90');
$contact_email = getDolGlobalString('SAV86_CONTACT_EMAIL', 'htp-sav86@htpmultimedia.fr');
$contact_jours = getDolGlobalString('SAV86_CONTACT_JOURS', 'Lun-Ven');
$contact_horaires = getDolGlobalString('SAV86_CONTACT_HORAIRES', '9h-12h30 / 14h30-18h30');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu SAV86 - <?php echo dol_escape_htmltag($obj->ref); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.4; background: #f4f4f4; }
        .receipt, .conditions-full { width: 210mm; min-height: auto; margin: 20px auto; padding: 15mm; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 3px solid #4D4D4D; padding-bottom: 10px; }
        .logo-company { font-size: 20px; font-weight: bold; color: #4D4D4D; }
        .ref-badge { background: #4D4D4D; color: #fff; padding: 8px 15px; font-size: 22px; font-weight: bold; border-radius: 5px; }
        .contact-info { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 15px; font-size: 11px; }
        .contact-info dt { font-weight: bold; color: #666; }
        .central-message { text-align: center; margin: 15px 0; padding: 8px; background: #eef5ff; border-radius: 5px; font-size: 13px; font-weight: bold; }
        .client-section { margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 4px solid #4D4D4D; }
        .client-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 10px; font-size: 11px; }
        .client-grid .field strong { display: block; color: #666; margin-bottom: 2px; }
        .detail-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 12px; background: #fff; page-break-inside: avoid; }
        .detail-box h4 { font-size: 12px; color: #4D4D4D; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 4px; text-transform: uppercase; }
        .info-row { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 4px; font-size: 11px; }
        .info-row span { flex: 1; min-width: 150px; }
        .info-row strong { color: #555; margin-right: 5px; }
        .status-badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; color: #fff; vertical-align: middle; }
        .status-PasCommence { background: #96A6BE; }
        .status-EnCours { background: #FFCC66; color: #000; }
        .status-Attente { background: #FF6666; }
        .status-Fini { background: #99CC00; }
        .pricing { margin: 15px 0; text-align: right; font-size: 12px; }
        .pricing table { width: 200px; margin-left: auto; border-collapse: collapse; }
        .pricing td { padding: 3px 8px; border-bottom: 1px solid #eee; }
        .pricing .total { font-size: 13px; border-top: 2px solid #4D4D4D; font-weight: bold; }
        .signature { margin-top: 25px; padding-top: 12px; border-top: 2px dashed #ccc; }
        .signature-box { width: 180px; height: 70px; border: 1px solid #ccc; margin-top: 8px; }
        .page-break { page-break-before: always; display: <?php echo $afficher_cgv ? 'block' : 'none'; ?>; }
        .conditions-full h3 { font-size: 12px; color: #4D4D4D; margin: 12px 0 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .text-content { white-space: pre-wrap; font-size: 11px; line-height: 1.2; }
        .print-actions { position: fixed; top: 20px; right: 20px; z-index: 1000; }
        .print-actions button { padding: 7px 12px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold; }
        .btn-print { background: #007bff; color: #fff; }
        .btn-close { background: #6c757d; color: #fff; }
        @media print { .print-actions { display: none; } body { background: #fff; } .receipt, .conditions-full { margin: 0; padding: 10mm; box-shadow: none; width: 100%; } }
    </style>
</head>
<body>
    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimer</button>
        <button class="btn-close" onclick="window.close()">✕ Fermer</button>
    </div>
    
    <div class="receipt">
        <div class="header">
            <div class="logo-company">HTP MULTIMEDIA<br><small style="font-size: 11px; color: #666;">Service Après-Vente</small></div>
            <div class="ref-badge"><?php echo dol_escape_htmltag($obj->ref); ?></div>
        </div>
        <dl class="contact-info">
            <dt>Tél.</dt><dd><?php echo dol_escape_htmltag($contact_tel); ?></dd>
            <dt>Email</dt><dd><?php echo dol_escape_htmltag($contact_email); ?></dd>
            <dt>Reçu par</dt><dd><?php echo dol_escape_htmltag($commercial->firstname.' '.$commercial->lastname); ?></dd>
            <dt>Horaires</dt><dd><?php echo dol_escape_htmltag($contact_jours.' : '.$contact_horaires); ?></dd>
        </dl>
        <div class="central-message">
            Merci de nous contacter le <?php echo $jour_prevue ? dol_escape_htmltag($jour_prevue.' ') : ''; ?><?php echo dol_print_date($obj->date_prevue, 'day'); ?> (après-midi)
        </div>
        <div class="client-section">
            <h4 style="margin-bottom: 8px; font-size: 12px;">👤 Client</h4>
            <div class="client-grid">
                <div class="field"><strong>Nom</strong><?php echo dol_escape_htmltag($soc->name); ?></div>
                <div class="field"><strong>Tél. domicile</strong><?php echo !empty($soc->phone) ? dol_escape_htmltag($soc->phone) : '---'; ?></div>
                <div class="field"><strong>Tél. portable</strong><?php echo !empty($soc->phone_mobile) ? dol_escape_htmltag($soc->phone_mobile) : '---'; ?></div>
                <div class="field"><strong>Email</strong><?php echo !empty($soc->email) ? dol_escape_htmltag($soc->email) : '---'; ?></div>
                <div class="field"><strong>Adresse</strong><?php echo !empty($soc->address) ? nl2br(dol_escape_htmltag($soc->address)) : '---'; ?></div>
                <div class="field"><strong>Date d'entrée</strong><?php echo dol_print_date($obj->date_entree, 'day'); ?></div>
            </div>
        </div>
        <div class="detail-box">
            <h4>🔧 Matériel</h4>
            <div class="info-row">
                <span><strong>Type :</strong> <?php echo dol_escape_htmltag($obj->type_pc ?: '---'); ?></span>
                <span><strong>Format :</strong> <?php echo dol_escape_htmltag($obj->format ?: '---'); ?></span>
                <span><strong>Garantie :</strong> <?php echo dol_escape_htmltag($obj->garantie ?: '---'); ?></span>
            </div>
            <div class="info-row">
                <span><strong>Mot de passe :</strong> <?php echo dol_escape_htmltag($obj->Mdpasse ?: '---'); ?></span>
                <span><strong>Pièces jointes :</strong> <?php echo dol_escape_htmltag($obj->pieces_jointes ?: '---'); ?></span>
            </div>
        </div>
        <div class="detail-box">
            <h4>📋 Suivi</h4>
            <div class="info-row">
                <span><strong>État :</strong> <span class="status-badge status-<?php echo dol_escape_htmltag($obj->etat); ?>"><?php echo HTPSav86::LibStatut($obj->etat, 0); ?></span></span>
                <span><strong>Priorité :</strong> <span style="color: <?php echo $obj->indice_priorite === 'urgent' ? 'red' : 'inherit'; ?>; font-weight: bold;"><?php echo ucfirst(dol_escape_htmltag($obj->indice_priorite ?: 'normal')); ?></span></span>
                <span><strong>Commercial :</strong> <?php echo dol_escape_htmltag($commercial->firstname.' '.$commercial->lastname); ?></span>
                <span><strong>Technicien :</strong> <?php echo dol_escape_htmltag($technicien->firstname.' '.$technicien->lastname); ?></span>
            </div>
        </div>
        <div class="detail-box">
            <h4>⚠️ Problème signalé par le client</h4>
            <div class="text-content"><?php echo dol_escape_htmltag($obj->probleme, 0, 1) ?: '---'; ?></div>
        </div>
        <?php if (!empty($obj->intervention)): ?>
        <div class="detail-box">
            <h4>✅ Intervention réalisée</h4>
            <div class="text-content"><?php echo dol_escape_htmltag($obj->intervention, 0, 1); ?></div>
        </div>
        <?php endif; ?>
        <div class="pricing">
            <table>
                <tr><td class="label">Main d'œuvre TTC</td><td class="value"><?php echo price($obj->nb_heure ?: 0); ?> €</td></tr>
                <tr><td class="label">Matériel TTC</td><td class="value"><?php echo price($obj->prix_materiel ?: 0); ?> €</td></tr>
                <tr class="total"><td class="label">Total TTC</td><td class="value"><?php echo price(($obj->nb_heure ?: 0) + ($obj->prix_materiel ?: 0)); ?> €</td></tr>
            </table>
        </div>
        <div class="signature">
            <p><strong>Signature du client :</strong></p>
            <div class="signature-box"></div>
            <p style="font-size: 9px; margin-top: 4px;"><em>(Lu et approuvé)</em></p>
        </div>
    </div>
    
    <!-- ✅ PAGE 2 CONDITIONNELLE : Uniquement si $afficher_cgv est vrai -->
    <?php if ($afficher_cgv): ?>
    <div class="page-break"></div>
    <div class="conditions-full">
        <h3>CONDITIONS GÉNÉRALES DU SERVICE APRÈS-VENTE HTP MULTIMEDIA</h3>
        <div class="text-content"><?php echo dol_escape_htmltag($conditions_generales, 0, 1); ?></div>
        
        <!-- ✅ SIGNATURE CLIENT (bas de page 2) -->
        <div style="margin-top: 50px; page-break-inside: avoid;">
            <p style="font-size: 11px; font-weight: bold; margin-bottom: 10px;">Signature du client :</p>
            <div style="width: 200px; height: 80px; border: 1px solid #ccc; margin-bottom: 5px;"></div>
            <p style="font-size: 9px; color: #666; font-style: italic;">(Lu et approuvé)</p>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>