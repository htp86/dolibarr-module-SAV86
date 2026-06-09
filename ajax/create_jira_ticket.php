<?php
/**
 * Création de ticket JIRA
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
}

// ============================================================================
// 1. CONFIGURATION JIRA - CHARGEMENT DIRECT
// ============================================================================
// On charge le fichier de config avec les constantes en clair
$paramFile = __DIR__ . '/param_jira_direct.php';
if (!file_exists($paramFile)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'param_jira_direct.php not found at: ' . $paramFile]);
    exit;
}
require_once $paramFile;

// ============================================================================
// 2. HEADERS & LOGS (comme OVH)
// ============================================================================
date_default_timezone_set('Europe/Paris');
header('Content-Type: application/json; charset=utf-8');

// Logs optionnels (comme dans ta version OVH)
$logFile = __DIR__ . '/jira_debug.log';
function log_append($file, $text) {
    @file_put_contents($file, $text, FILE_APPEND);
}
log_append($logFile, "----- START " . date('Y-m-d H:i:s') . " -----\n");

// ============================================================================
// 3. LECTURE INPUT JSON
// ============================================================================
$raw = @file_get_contents('php://input');
log_append($logFile, "RAW INPUT: " . substr($raw ?: '', 0, 2000) . "\n");

$input = json_decode($raw, true);
if (!is_array($input)) {
    log_append($logFile, "Invalid JSON input\n");
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

// Extraction des champs (noms identiques à ta version OVH)
$idfiche = $input['idfiche'] ?? '';
$summary = $input['summary'] ?? 'No summary';
$description = $input['description'] ?? '';
$assigneeAccountId = $input['assigneeAccountId'] ?? null;

log_append($logFile, "Fiche: {$idfiche}, Summary: " . substr($summary, 0, 100) . "\n");

// ============================================================================
// 4. PRÉPARATION PAYLOAD JIRA (format ADF - identique à OVH)
// ============================================================================

// Type d'issue : on utilise l'ID numérique défini dans param_jira_direct.php
// Si JIRA_ISSUE_TYPE_ID n'existe pas, fallback sur le nom (pour rétrocompatibilité)
if (defined('JIRA_ISSUE_TYPE_ID') && JIRA_ISSUE_TYPE_ID !== '') {
    $issuetype = ['id' => JIRA_ISSUE_TYPE_ID];
} elseif (defined('JIRA_ISSUE_TYPE')) {
    $issuetype = is_numeric(JIRA_ISSUE_TYPE) 
        ? ['id' => JIRA_ISSUE_TYPE] 
        : ['name' => JIRA_ISSUE_TYPE];
} else {
    $issuetype = ['name' => 'Task']; // Fallback ultime
}

// Conversion texte → ADF (Atlassian Document Format) - COPIE EXACTE de ta fonction OVH
function text_to_adf($text) {
    $text = str_replace("\r\n", "\n", $text);
    $lines = explode("\n", $text);
    $content = [];
    
    foreach ($lines as $line) {
        $line = rtrim($line, "\r");
        if ($line === '') {
            $content[] = ['type' => 'paragraph', 'content' => []];
            continue;
        }
        $pnode = ['type' => 'paragraph', 'content' => []];
        $pnode['content'][] = ['type' => 'text', 'text' => (string)$line];
        $content[] = $pnode;
    }
    
    if (count($content) === 0) {
        $content[] = ['type' => 'paragraph', 'content' => []];
    }
    
    return ['type' => 'doc', 'version' => 1, 'content' => $content];
}

$adf_description = text_to_adf($description);

// Construction du payload (structure identique à OVH)
$payload = [
    'fields' => [
        'project' => ['key' => JIRA_PROJECT_KEY],
        'summary' => mb_substr((string)$summary, 0, 255),
        'description' => $adf_description,
        'issuetype' => $issuetype
    ]
];

if (!empty($assigneeAccountId)) {
    $payload['fields']['assignee'] = ['accountId' => $assigneeAccountId];
}

$body = json_encode($payload, JSON_UNESCAPED_UNICODE);
log_append($logFile, "PAYLOAD TO JIRA: " . substr($body, 0, 2000) . "\n");

// ============================================================================
// 5. APPEL API JIRA VIA CURL (COPIE EXACTE de ta version OVH)
// ============================================================================
$createUrl = rtrim(JIRA_BASE, '/') . '/rest/api/3/issue';

$ch = curl_init($createUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

// Auth Basic (email:token) - comme dans ta version OVH
$userpwd = JIRA_USER_EMAIL . ':' . JIRA_API_TOKEN;
curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Logs verbeux optionnels (comme OVH)
$verboseFile = __DIR__ . '/jira_curl_verbose.txt';
$vf = @fopen($verboseFile, 'w+');
if ($vf) {
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_STDERR, $vf);
}

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($vf) fclose($vf);
curl_close($ch);

log_append($logFile, "CURL HTTP CODE: {$httpCode}\n");
log_append($logFile, "CURL ERROR: " . ($curlErr ?: '<none>') . "\n");
log_append($logFile, "CURL RESPONSE: " . substr(($response !== false ? $response : '<false>'), 0, 2000) . "\n");

// ============================================================================
// 6. TRAITEMENT RÉPONSE & SORTIE JSON (COPIE EXACTE de ta version OVH)
// ============================================================================
$parsed = json_decode($response, true);

if (is_array($parsed)) {
    log_append($logFile, "PARSED JIRA RESPONSE: " . json_encode($parsed, JSON_PRETTY_PRINT) . "\n");
    
    if ($httpCode >= 200 && $httpCode < 300) {
        $issueKey = $parsed['key'] ?? $parsed['id'] ?? null;
        echo json_encode(['success' => true, 'issueKey' => $issueKey, 'jira_raw' => $parsed], JSON_UNESCAPED_UNICODE);
        log_append($logFile, "✅ Issue created: " . ($issueKey ?: '<no-key>') . "\n");
        log_append($logFile, "----- END " . date('Y-m-d H:i:s') . " -----\n\n");
        exit;
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'jira_error',
            'http_status' => $httpCode,
            'jira_json' => $parsed
        ], JSON_UNESCAPED_UNICODE);
        log_append($logFile, "❌ Jira returned non-2xx: {$httpCode}\n");
        log_append($logFile, "----- END " . date('Y-m-d H:i:s') . " -----\n\n");
        exit;
    }
}

// Réponse non-JSON ou vide
log_append($logFile, "⚠️ JIRA RESPONSE NOT JSON OR EMPTY\n");
log_append($logFile, "----- END " . date('Y-m-d H:i:s') . " -----\n\n");

http_response_code(500);
echo json_encode([
    'success' => false,
    'error' => 'invalid_response_from_jira',
    'http_status' => $httpCode,
    'curl_error' => $curlErr,
    'response_preview' => $response ? (strlen($response) > 500 ? substr($response, 0, 500) . '...' : $response) : '<empty>'
], JSON_UNESCAPED_UNICODE);
exit;