<?php
/**
 * Version du module SAV86
 * Version 1.5 - Intégration JIRA fonctionnelle (Architecture OVH Style)
 * /volume1/web/dolibarr_test/htdocs/custom/sav86/sav86version.php
 */

// Affichage version pour debug développement
$PATHFILE = '/volume1/web/dolibarr_test/htdocs/custom/sav86/sav86version.php';
$VERSION = '20260507';
$BUILD = '1756';
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

// Version autonome garantie fonctionnelle
$module_version = '1.5';
$module_build = '20260422-1900';
$module_date = '22 avril 2026';
$module_author = 'HTP Multimedia';
$module_copyright = '© 2026 HTP Multimedia';

// Affichage debug si activé
if ($DEBUG_BOOL) {
    print '<div style="background:#e7f3ff;padding:8px;margin:10px;border-left:4px solid #007bff;font-family:monospace;">';
    print '<strong>🔧 SAV86 - Version '.SAV86_VERSION.' Build '.SAV86_BUILD.'</strong>';
    print ' | Debug: ON';
    print '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAV86 - Version du module</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 20px; background: #f5f7fa; color: #333; line-height: 1.5; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 3px solid #007bff; padding-bottom: 15px; margin-bottom: 25px; font-size: 1.8em; }
        h2 { color: #34495e; margin: 30px 0 20px; font-size: 1.3em; border-left: 4px solid #007bff; padding-left: 15px; }
        h3 { color: #555; margin: 25px 0 15px; font-size: 1.1em; }
        .section { margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid #eee; }
        .section:last-child { border-bottom: none; }
        .info { display: flex; margin: 10px 0; }
        .info label { font-weight: 600; color: #666; min-width: 180px; }
        .info span { color: #333; }
        .badge { background: #007bff; color: #fff; padding: 5px 12px; border-radius: 4px; font-size: 0.85em; font-weight: 500; display: inline-block; }
        .badge.secondary { background: #6c757d; }
        .badge.success { background: #28a745; }
        .features ul { list-style: none; padding-left: 0; }
        .features li { padding: 8px 0 8px 30px; position: relative; }
        .features li::before { content: "✓"; position: absolute; left: 0; color: #28a745; font-weight: bold; font-size: 1.2em; }
        .roadmap li { padding: 8px 0 8px 30px; position: relative; }
        .roadmap li::before { content: "🔄"; position: absolute; left: 0; }
        table.changelog { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.changelog th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; color: #555; border-bottom: 2px solid #dee2e6; }
        table.changelog td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        table.changelog tr:last-child td { border-bottom: none; }
        table.changelog tr:hover { background: #f8f9fa; }
        .back { margin-top: 35px; text-align: center; padding-top: 25px; border-top: 1px solid #eee; }
        .back a { display: inline-block; padding: 14px 35px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; font-weight: 500; transition: background 0.3s; }
        .back a:hover { background: #0056b3; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .highlight { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } .info { flex-direction: column; } .info label { margin-bottom: 5px; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 SAV86 - Gestion des interventions SAV</h1>

        <!-- INFORMATIONS GÉNÉRALES -->
        <div class="section">
            <h2>📋 Informations générales</h2>
            <div class="info"><label>Module :</label> <span>SAV86 - Gestion des interventions SAV</span></div>
            <div class="info"><label>Description :</label> <span>Module de gestion des interventions de dépannage informatique en atelier</span></div>
            <div class="info"><label>Version :</label> <span class="badge success"><?php echo $module_version; ?></span></div>
            <div class="info"><label>Build :</label> <span><?php echo $module_build; ?></span></div>
            <div class="info"><label>Date :</label> <span><?php echo $module_date; ?></span></div>
            <div class="info"><label>Auteur :</label> <span><?php echo $module_author; ?></span></div>
            <div class="info"><label>Copyright :</label> <span><?php echo $module_copyright; ?></span></div>
            <div class="info"><label>ID Module :</label> <span>500860</span></div>
            <div class="info"><label>Chemin :</label> <span>/custom/sav86/</span></div>
        </div>

        <!-- FONCTIONNALITÉS -->
        <div class="section">
            <h2>✅ Fonctionnalités principales</h2>
            <div class="grid-2">
                <div class="features">
                    <h3>Gestion des interventions</h3>
                    <ul>
                        <li>CRUD complet (créer, lire, modifier, supprimer)</li>
                        <li>Formulaire de création avec validation serveur + client</li>
                        <li>Édition des fiches existantes</li>
                        <li>Suppression avec confirmation</li>
                        <li>Autocomplétion client</li>
                        <li>Champ Intervention (description des travaux)</li>
                        <li><strong>Préservation des données en cas d'erreur</strong></li>
                    </ul>
                </div>
                <div class="features">
                    <h3>Suivi & Statuts</h3>
                    <ul>
                        <li>5 statuts : Pas commencé, En cours, En attente, Terminé, Parti</li>
                        <li>Couleurs par statut : #96A6BE, #FFCC66, #FF6666, #99CC00</li>
                        <li>Priorités Normal / Urgent</li>
                        <li>Validation automatique : date_entree == date_prevue → Urgent</li>
                        <li>Règle 48h pour priorité normale</li>
                    </ul>
                </div>
            </div>
            <div class="grid-2">
                <div class="features">
                    <h3>Impression & Export</h3>
                    <ul>
                        <li>Reçu recto : infos client, problème, intervention, signature</li>
                        <li>Verso : conditions générales de vente (13 articles, éditables)</li>
                        <li>Impression via window.print()</li>
                        <li>Design responsive imprimable</li>
                        <li>Interlignes réduites pour optimiser l'espace</li>
                        <li>Layout compact (Matériel/Suivi optimisés)</li>
                        <li><strong>Signature client en bas de page 2</strong></li>
                        <li><strong>Suppression des pages blanches à l'impression</strong></li>
                    </ul>
                </div>
                <div class="features">
                    <h3>Recherche & Filtres</h3>
                    <ul>
                        <li>Filtre par référence (SAV86-XXXXXX)</li>
                        <li>Filtre par client (autocomplétion)</li>
                        <li>Filtre par état (liste déroulante)</li>
                        <li><strong>Recherche texte libre multi-champs</strong></li>
                        <li>Exclusion automatique des fiches "Parti"</li>
                        <li>Tri dynamique par colonnes</li>
                    </ul>
                </div>
            </div>
            <div class="grid-2">
                <div class="features">
                    <h3>Interface & UX</h3>
                    <ul>
                        <li>Interface responsive (mobile/tablette/desktop)</li>
                        <li>Standards Dolibarr 23.0.2</li>
                        <li>Traductions FR/EN via fichiers .lang</li>
                        <li>Permissions granulaires (read/write/delete)</li>
                        <li>Menu top "SAV86" intégré</li>
                        <li>Filtres compacts sur une ligne</li>
                        <li>Colonnes centrées dans la liste</li>
                    </ul>
                </div>
                <div class="features">
                    <h3>Configuration & Paramètres ⚙️</h3>
                    <ul>
                        <li><strong>Roue dentée fonctionnelle</strong> (admin/setup.php)</li>
                        <li>Édition des conditions générales via interface</li>
                        <li>Toggle affichage/masquage page 2 (CGV) - ✅ Corrigé</li>
                        <li>Alerte configurable si champ "Mot de passe" vide</li>
                        <li><strong>Informations de contact éditables</strong> : Tél, Email, Jours, Horaires</li>
                        <li><strong>Adresse email SMS configurable</strong> : destinataire des notifications SMS</li>
                        <li>Constantes Dolibarr : SAV86_CONDITIONS_GENERALES, SAV86_AFFICHER_CGV, SAV86_ALERT_MDP_VIDE, SAV86_CONTACT_*, SAV86_SMS_EMAIL</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- BASE DE DONNÉES -->
        <div class="section">
            <h2>🗄️ Base de données</h2>
            <div class="info"><label>Table principale :</label> <span>llx_sav86_fiche</span></div>
            <div class="info"><label>Moteur :</label> <span>InnoDB</span></div>
            <div class="info"><label>Encodage :</label> <span>UTF8MB4 / utf8mb4_unicode_ci</span></div>
            <div class="info"><label>Nombre de champs :</label> <span>29</span></div>
            <div class="info"><label>Index :</label> <span>5 (ref, fk_soc, etat, date_entree, date_prevue)</span></div>
            <div class="info"><label>Clé primaire :</label> <span>rowid (AUTO_INCREMENT)</span></div>
            <div class="info"><label>Référence unique :</label> <span>ref (SAV86-000001, auto-générée)</span></div>
        </div>

        <!-- ENVIRONNEMENT -->
        <div class="section">
            <h2>⚙️ Environnement technique</h2>
            <div class="grid-2">
                <div>
                    <div class="info"><label>NAS :</label> <span>Synology DS418</span></div>
                    <div class="info"><label>DSM :</label> <span>7.x</span></div>
                    <div class="info"><label>Web server :</label> <span>Nginx + PHP-FPM</span></div>
                    <div class="info"><label>PHP :</label> <span>7.4+ (via PHP-FPM)</span></div>
                </div>
                <div>
                    <div class="info"><label>Base de données :</label> <span>MariaDB 10</span></div>
                    <div class="info"><label>Dolibarr :</label> <span>23.0.2</span></div>
                    <div class="info"><label>Chemin test :</label> <span>/volume1/web/dolibarr_test/</span></div>
                    <div class="info"><label>URL test :</label> <span>https://192.168.2.198:54321/</span></div>
                </div>
            </div>
        </div>

        <!-- NOUVEAUTÉS VERSION 1.5 -->
        <div class="section">
            <h2>🎉 Nouveautés version 1.5</h2>
            <div class="highlight">
                <strong>Dernière mise à jour (Build 1900 - 22/04/2026) :</strong>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <li>✅ <strong>Intégration JIRA 100% fonctionnelle</strong> : Création de tickets depuis les fiches SAV</li>
                    <li>✅ <strong>Architecture "OVH Style"</strong> : Fichier de config dédié <code>param_jira_direct.php</code> avec constantes en clair</li>
                    <li>✅ <strong>Format ADF JIRA</strong> : Conversion automatique texte → Atlassian Document Format</li>
                    <li>✅ <strong>Logs de debug</strong> : Fichiers <code>jira_debug.log</code> et <code>jira_curl_verbose.txt</code> pour le troubleshooting</li>
                    <li>✅ <strong>Gestion d'erreur JSON propre</strong> : Plus de messages HTML, uniquement du JSON</li>
                    <li>✅ <strong>Assignés configurables</strong> : Sélection dynamique dans le modal (Joël, Jean-Michel, Florian...)</li>
                    <li>✅ <strong>Correction PDO</strong> : Utilisation du driver <code>mysql</code> au lieu de <code>mysqli</code></li>
                </ul>
            </div>
        </div>

        <!-- ROADMAP -->
        <div class="section">
            <h2>🗓️ Roadmap - Phase 2 (À venir)</h2>
            <ul class="roadmap">
                <li><strong>Migration OVH → Dolibarr</strong> : Import client86/fiche86 → llx_societe/llx_sav86_fiche avec mapping complexe</li>
                <li><strong>Intégration facturation</strong> : Lien natif avec factures/devis Dolibarr via add_object_linked()</li>
                <li><strong>Statistiques avancées</strong> : Tableau de bord, graphiques, KPI (interventions par état, CA, délais)</li>
                <li><strong>Consultation client</strong> : Lien public de suivi avec compteur consultee × 1000</li>
                <li><strong>Notifications</strong> : Email / SMS automatiques (statut changé, prêt à récupérer)</li>
                <li><strong>Gestion stocks</strong> : Suivi des pièces détachées utilisées</li>
                <li><strong>Planning</strong> : Calendrier des interventions par technicien</li>
                <li><strong>Champs de recherche configurables</strong> : Via roue dentée, choisir quels champs indexer pour la recherche texte</li>
            </ul>
        </div>

        <!-- CHANGELOG -->
        <div class="section">
            <h2>📜 Historique des versions</h2>
            <table class="changelog">
                <thead>
                    <tr><th>Version</th><th>Date</th><th>Build</th><th>Modifications</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge success">1.5</span></td>
                        <td>22/04/2026</td>
                        <td>20260422-1900</td>
                        <td><strong>INTÉGRATION JIRA FONCTIONNELLE ✅</strong><br>
                        • Architecture "OVH Style" : config JIRA via fichier dédié <code>param_jira_direct.php</code><br>
                        • Format ADF pour description JIRA (conversion texte → structure Atlassian)<br>
                        • Logs de debug : <code>jira_debug.log</code> et <code>jira_curl_verbose.txt</code><br>
                        • Gestion d'erreur JSON propre (plus de HTML dans les réponses AJAX)<br>
                        • Correction driver PDO : <code>mysql</code> au lieu de <code>mysqli</code><br>
                        • Assignés configurables via modal de sélection<br>
                        • Issue Type par ID numérique (<code>10003</code>) pour compatibilité JIRA Cloud<br>
                        • Token API en clair dans le fichier de config (contourne le chiffrement Dolibarr)</td>
                    </tr>
                    <tr>
                        <td><span class="badge success">1.31</span></td>
                        <td>21/04/2026</td>
                        <td>20260421-1600</td>
                        <td><strong>INTÉGRATION JIRA (préliminaire) & CORRECTIONS</strong><br>
                        • Bouton SMS : utilisation exclusive de phone_mobile (Tél portable)<br>
                        • Encodage sujet mailto: : espaces en %20 pour compatibilité client mail<br>
                        • Email destinataire SMS configurable via roue dentée (SAV86_SMS_EMAIL)<br>
                        • Lien liste → fiche : ajout action=view pour affichage direct mode visualisation<br>
                        • Configuration JIRA dans la roue dentée (URL, API token, projet, type d'issue, assignés)<br>
                        • Format ADF : conversion auto description vers Atlassian Document Format<br>
                        • Modal assignés : sélection dynamique selon config</td>
                    </tr>
                    <tr>
                        <td><span class="badge success">1.30</span></td>
                        <td>21/04/2026</td>
                        <td>20260421-1300</td>
                        <td><strong>STABILITÉ & CONFIGURATION AVANCÉE ✅</strong><br>
                        • Validation dates robuste (checkdate : rejet 31 février, etc.)<br>
                        • Préservation formulaire en cas d'erreur (pas de perte de données)<br>
                        • Correction toggle affichage page 2 (checkbox CGV)<br>
                        • Signature client ajoutée en bas de page 2<br>
                        • Suppression pages blanches à l'impression (CSS min-height: auto)<br>
                        • Infos contact configurables : Tél, Email, Jours, Horaires<br>
                        • Utilisation getDolGlobalString() pour éviter warnings constantes<br>
                        • Correction logique checkbox (GETPOSTISSET pour gérer valeur 0)</td>
                    </tr>
                    <tr>
                        <td><span class="badge">1.20</span></td>
                        <td>20/04/2026</td>
                        <td>20260420-1700</td>
                        <td><strong>CONFIGURATION & PARAMÈTRES ✅</strong><br>
                        • Roue dentée ⚙️ fonctionnelle (admin/setup.php)<br>
                        • Édition conditions générales via interface admin<br>
                        • Toggle affichage/masquage page 2 (CGV)<br>
                        • Alerte configurable si champ "Mot de passe" vide<br>
                        • Constantes Dolibarr : SAV86_CONDITIONS_GENERALES, SAV86_AFFICHER_CGV, SAV86_ALERT_MDP_VIDE<br>
                        • Correction erreur 500 (include admin.lib.php)<br>
                        • Debug amélioré : 2 niveaux ($DEBUG_BOOL / $DEBUG_ERRORS)</td>
                    </tr>
                    <tr>
                        <td><span class="badge">1.0.0</span></td>
                        <td>20/04/2026</td>
                        <td>20260420-1500</td>
                        <td><strong>Phase 1 TERMINÉE ✅</strong><br>
                        • Recherche texte libre multi-champs<br>
                        • Filtres compacts sur une ligne<br>
                        • Centrage titres de colonnes et noms de clients<br>
                        • Correction affichage retours ligne<br>
                        • Optimisation impression (interlignes 1.2, layout compact)<br>
                        • Correction bug création (exit() + ob_end_clean())<br>
                        • Ajout champ Intervention dans CREATE/EDIT<br>
                        • Correction bug $DEBUG_BOOL (sav86.class.php)<br>
                        • Correction conversion dates DATETIME → timestamp<br>
                        • Correction bug select_dolusers ($morefilter)<br>
                        • Suppression liens cliquables Commercial/Technicien (impression)</td>
                    </tr>
                    <tr>
                        <td><span class="badge secondary">0.9.0</span></td>
                        <td>17/04/2026</td>
                        <td>20260417-1400</td>
                        <td><strong>Version bêta</strong><br>
                        • Structure du module /custom/sav86/<br>
                        • Création table llx_sav86_fiche (29 champs)<br>
                        • Formulaire de création/édition de base<br>
                        • Liste des interventions avec filtres simples<br>
                        • Impression basique du reçu</td>
                    </tr>
                    <tr>
                        <td><span class="badge">0.1.0</span></td>
                        <td>16/04/2026</td>
                        <td>20260416-1202</td>
                        <td><strong>Initialisation</strong><br>
                        • modSav86.class.php (activation/désactivation)<br>
                        • sav86.class.php (classe métier HTPSav86)<br>
                        • SQL de création de table llx_sav86_fiche<br>
                        • Fichiers de langue FR/EN de base</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- FOOTER -->
        <div class="back">
            <a href="javascript:history.back()">← Retour à la page précédente</a>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #999; font-size: 0.85em;">
            Page générée le <?php echo date('d/m/Y à H:i'); ?> • Module SAV86 v<?php echo $module_version; ?> • Build <?php echo $module_build; ?>
        </div>
    </div>
</body>
</html>