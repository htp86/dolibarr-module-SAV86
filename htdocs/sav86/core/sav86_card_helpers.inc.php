<?php
/**
 * Fonctions utilitaires pour sav86_card.php
 * /volume1/web/dolibarr_test/htdocs/custom/sav86/htdocs/sav86/core/sav86_card_helpers.inc.php
 */

// Déclaration des globales nécessaires
global $db, $conf, $langs, $user, $form;
global $DEBUG_BOOL, $DEBUG_ERRORS;

/**
 * Parse et valide une date au format JJ/MM/AAAA
 * @param string $dateStr Date au format JJ/MM/AAAA
 * @return int|false Timestamp Unix ou false si invalide
 */
function parseAndValidateDate($dateStr) {
    $parts = explode('/', trim($dateStr));
    if (count($parts) != 3) return false;
    $day = (int)$parts[0];
    $month = (int)$parts[1];
    $year = (int)$parts[2];
    // checkdate vérifie la réalité de la date (pas de 31 février, etc.)
    if (!checkdate($month, $day, $year)) return false;
    return dol_mktime(0, 0, 0, $month, $day, $year);
}

/**
 * Retourne le label d'un statut avec option picto
 * @param string $status Code statut
 * @param int $mode 0=label, 1=short, 2=picto
 * @return string Label ou picto
 */
function getSav86StatusLabel($status, $mode = 0) {
    $labels = [
        'PasCommence' => 'PC pas commencé',
        'EnCours' => 'PC en cours',
        'Attente' => 'PC en attente',
        'Fini' => 'PC fini',
        'Parti' => 'Client venu chercher PC'
    ];
    $pictos = [
        'PasCommence' => 'statut0',
        'EnCours' => 'statut4',
        'Attente' => 'statut6',
        'Fini' => 'statut9',
        'Parti' => 'statut8'
    ];
    if ($mode == 2) return $pictos[$status] ?? 'statut0';
    return $labels[$status] ?? $status;
}

/**
 * Formate un prix pour affichage
 * @param float $value Valeur numérique
 * @return string Prix formaté avec €
 */
function formatSav86Price($value) {
    return price($value) . ' €';
}