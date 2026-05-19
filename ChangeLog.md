# SAV86 - Historique des versions

## Version 1.5 - 22/04/2026 (Build 20260422-1900)
**INTÉGRATION JIRA FONCTIONNELLE ✅**
- Architecture "OVH Style" : config JIRA via fichier dédié param_jira_direct.php
- Format ADF pour description JIRA (conversion texte → structure Atlassian)
- Logs de debug : jira_debug.log et jira_curl_verbose.txt
- Gestion d'erreur JSON propre (plus de HTML dans les réponses AJAX)
- Correction driver PDO : mysql au lieu de mysqli
- Assignés configurables via modal de sélection
- Issue Type par ID numérique (10003) pour compatibilité JIRA Cloud
- Token API en clair dans le fichier de config (contourne le chiffrement Dolibarr)

## Version 1.31 - 21/04/2026 (Build 20260421-1600)
**INTÉGRATION JIRA (préliminaire) & CORRECTIONS**
- Bouton SMS : utilisation exclusive de phone_mobile (Tél portable)
- Encodage sujet mailto: : espaces en %20 pour compatibilité client mail
- Email destinataire SMS configurable via roue dentée (SAV86_SMS_EMAIL)
- Lien liste → fiche : ajout action=view pour affichage direct mode visualisation
- Configuration JIRA dans la roue dentée (URL, API token, projet, type d'issue, assignés)
- Format ADF : conversion auto description vers Atlassian Document Format
- Modal assignés : sélection dynamique selon config

## Version 1.30 - 21/04/2026 (Build 20260421-1300)
**STABILITÉ & CONFIGURATION AVANCÉE ✅**
- Validation dates robuste (checkdate : rejet 31 février, etc.)
- Préservation formulaire en cas d'erreur (pas de perte de données)
- Correction toggle affichage page 2 (checkbox CGV)
- Signature client ajoutée en bas de page 2
- Suppression pages blanches à l'impression (CSS min-height: auto)
- Infos contact configurables : Tél, Email, Jours, Horaires
- Utilisation getDolGlobalString() pour éviter warnings constantes
- Correction logique checkbox (GETPOSTISSET pour gérer valeur 0)

## Version 1.20 - 20/04/2026 (Build 20260420-1700)
**CONFIGURATION & PARAMÈTRES ✅**
- Roue dentée ⚙️ fonctionnelle (admin/setup.php)
- Édition conditions générales via interface admin
- Toggle affichage/masquage page 2 (CGV)
- Alerte configurable si champ "Mot de passe" vide
- Constantes Dolibarr : SAV86_CONDITIONS_GENERALES, SAV86_AFFICHER_CGV, SAV86_ALERT_MDP_VIDE
- Correction erreur 500 (include admin.lib.php)
- Debug amélioré : 2 niveaux ($DEBUG_BOOL / $DEBUG_ERRORS)

## Version 1.0.0 - 20/04/2026 (Build 20260420-1500)
**Phase 1 TERMINÉE ✅**
- Recherche texte libre multi-champs
- Filtres compacts sur une ligne
- Centrage titres de colonnes et noms de clients
- Correction affichage retours ligne
- Optimisation impression (interlignes 1.2, layout compact)
- Correction bug création (exit() + ob_end_clean())
- Ajout champ Intervention dans CREATE/EDIT
- Correction bug $DEBUG_BOOL (sav86.class.php)
- Correction conversion dates DATETIME → timestamp
- Correction bug select_dolusers ($morefilter)
- Suppression liens cliquables Commercial/Technicien (impression)

## Version 0.9.0 - 17/04/2026 (Build 20260417-1400)
**Version bêta**
- Structure du module /custom/sav86/
- Création table llx_sav86_fiche (29 champs)
- Formulaire de création/édition de base
- Liste des interventions avec filtres simples
- Impression basique du reçu

## Version 0.1.0 - 16/04/2026 (Build 20260416-1202)
**Initialisation**
- modSav86.class.php (activation/désactivation)
- sav86.class.php (classe métier HTPSav86)
- SQL de création de table llx_sav86_fiche
- Fichiers de langue FR/EN de base