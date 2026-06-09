# Projet SAV86 — Contexte permanent

## Infrastructure
- **NAS Synology :** `htp_ds418` (192.168.2.198), OS Linux aarch64, nginx/1.23.1, PHP 8.2.28
- **Dolibarr dev :** `https://dolibarr.test/` — racine = `/volume1/web/dolibarr_test/htdocs/`
- **Base MariaDB :** `dolibarr_test`, préfixe `llx_`, sur le NAS
  - User: `dolibarr_test`, password: `xxhtp@1998HTPXX`
  - Accessible uniquement depuis localhost (127.0.0.1:3306)
  - phpMyAdmin : `http://192.168.2.198/phpmyadmin/`
- **Utilisateur NAS :** `htpadmin` (mdp: à demander, aussi root)
- **SSH :** `ssh root@192.168.2.198` (mdp: `xxhtp@1998HTPXX`)
- **Centreon VM :** `192.168.2.107` (CentOS 7)

## JIRA Integration
- **Instance :** `https://htpmultimedia.atlassian.net`
- **Projet :** T86
- **Compte API :** `sav86@htpmultimedia.fr`
- **Token API :** Stocké dans la constante Dolibarr `SAV86_JIRA_API_TOKEN` via `admin/setup.php` (plus dans le code)
- **Issue type ID :** `10003`
- **Fichier de config :** `ajax/param_jira_direct.php` — inclus dans `.gitignore` (ne JAMAIS committer)
- **Endpoint création :** `ajax/create_jira_ticket.php` (style "OVH", charge `main.inc.php` via `param_jira_direct.php`)

## Module SAV86 — Structure
```
custom/sav86/
├── AGENTS.md                    # Ce fichier
├── .gitignore                   # .bak*, *~, sav86_session.*, ajax/param_jira_direct.php
├── sav86version.php             # Version du module (hardcodé pour l'instant)
├── admin/
│   └── setup.php                # Page de config (JIRA, CGV, alertes, contact)
├── ajax/
│   ├── param_jira_direct.php    # Lit JIRA_API_TOKEN depuis dolibarr_get_const()
│   └── create_jira_ticket.php   # Crée un ticket JIRA via API REST
├── class/
│   └── sav86.class.php          # Classe principale SAV86
├── core/modules/
│   └── modSav86.class.php       # Descripteur module Dolibarr (roue dentée intégrée)
├── htdocs/sav86/                # Pages métier (non-standard, sous htdocs/sav86/)
│   ├── index.php, sav86_card.php, sav86_list.php, sav86_print.php
│   └── core/ (sav86_card_actions.inc.php, _form.inc.php, _helpers.inc.php, _view.inc.php)
├── lib/
│   └── sav86.lib.php            # Bibliothèque (VIDE — 6 lignes, à remplir ou supprimer)
├── langs/                       # Traductions (fr_FR, en_US)
└── sql/                         # Structure SQL
```

## Git / GitHub
- **Remote :** `https://github.com/htp86/dolibarr-module-SAV86.git`
- **Branche :** `main`
- **Dernier commit :** `10185bd` — "Initial SAV86 module - version propre"
  - Nettoyé : plus de token dans l'historique, plus de fichiers `.bak.*`
  - `git push --force` effectué le 09/06/2026

## Conventions de code (portabilité SAV86)
- **À faire :** Tous les fichiers PHP doivent suivre le standard smsandroid :
  ```php
  $VERSION = date('Ymd', filemtime(__FILE__));
  $BUILD = date('Hi', filemtime(__FILE__));
  $PATHFILE = __FILE__;
  $DEBUG_LIGHT = true;     // false en prod : affiche version/build/chemin
  $DEBUG_ERRORS = false;   // false en prod : affiche $_POST/$_GET/$_SERVER en bas
  if ($DEBUG_ERRORS) { ini_set('display_errors', 1); error_reporting(E_ALL); }
  ```
- **Actuel (À CORRIGER) :**
  - `$DEBUG_BOOL` utilisé dans `setup.php` et `sav86version.php` — remplacer par `$DEBUG_LIGHT`
  - `$PATHFILE` hardcodé partout (`/volume1/web/dolibarr_test/...`) — remplacer par `__FILE__`
  - `VERSION`/`BUILD` hardcodés (`define('SAV86_VERSION', '20260421')`) — remplacer par `filemtime(__FILE__)`
- **Pas de commentaires inutiles** dans le code
- **Pas d'`ini_set` en dur** — toujours conditionnel (`if ($DEBUG_ERRORS)`)

## Anthologie des décisions
- **29/12/2025 :** Token JIRA créé (`SAV86JIRATOKEN2026_01`), commité en dur dans `param_jira_direct.php` par erreur
- **09/06/2026 :** Fuite détectée par GitGuardian sur GitHub → rotation immédiate
- **09/06/2026 :** Nouveau token (`SAV86JIRATOKEN2026_02`) créé, stocké en base Dolibarr via setup.php
- **09/06/2026 :** Historique GitHub réécrit (force-push), 23 fichiers `.bak.*` supprimés
- **09/06/2026 :** `param_jira_direct.php` modifié pour inclure `main.inc.php` et lire la constante via `dolibarr_get_const()`
- **09/06/2026 :** `.gitignore` créé, `param_jira_direct.php` ajouté dedans

## Commandes utiles
```bash
# Vérifier le token en base
mysql -u dolibarr_test -p'xxhtp@1998HTPXX' dolibarr_test -e "SELECT name, LEFT(value,40) AS debut FROM llx_const WHERE name='SAV86_JIRA_API_TOKEN';"

# Lister toutes les constantes SAV86
mysql -u dolibarr_test -p'xxhtp@1998HTPXX' dolibarr_test -e "SELECT name, LEFT(value,80) AS val FROM llx_const WHERE name LIKE 'SAV86_%';"

# Git — voir le log
cd /volume1/web/dolibarr_test/htdocs/custom/sav86 && git log --oneline -5

# Statut git
cd /volume1/web/dolibarr_test/htdocs/custom/sav86 && git status
```

## Problèmes connus
- `lib/sav86.lib.php` vide (6 lignes, ne fait rien) — soit supprimer, soit implémenter
- Architecture non-standard : les pages métier sont sous `htdocs/sav86/` au lieu d'être à la racine du module
- **Portabilité non uniformisée** : `$DEBUG_BOOL`, `$PATHFILE` hardcodé, `VERSION`/`BUILD` hardcodés — ne pas déployer sur un autre serveur sans corriger
- **Double point d'entrée setup** : `admin/setup.php` ET `modSav86.class.php::setupPage()` (roue dentée) — redondant

## Connexion SSH
```bash
ssh root@192.168.2.198  # mdp: xxhtp@1998HTPXX
# Une fois connecté, les sources sont dans /volume1/web/dolibarr_test/htdocs/custom/sav86/
```

## Module smsandroid (projet frère)
- Voir `custom/smsandroid/AGENTS.md` dans le même dossier Dolibarr
- Utilise le même NAS, la même base, le même serveur
- API SMS Gateway sur `192.168.2.209:8080` (phone Android sans SIM actuellement)
