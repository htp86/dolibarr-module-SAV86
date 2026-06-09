-- ============================================================================
-- Table llx_sav86_fiche - Fiches d'intervention SAV86
-- ============================================================================

CREATE TABLE IF NOT EXISTS llx_sav86_fiche (
  -- Identifiants & références
  rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(32) NOT NULL, -- Référence unique: SAV86-000001
  fk_soc INTEGER NOT NULL, -- Lien client → llx_societe.rowid
  fk_user_creat INTEGER, -- Commercial créateur → llx_user.rowid
  fk_user_valid INTEGER, -- Technicien assigné → llx_user.rowid

  -- Dates (format DATETIME Dolibarr)
  date_creation DATETIME,
  date_entree DATETIME, -- Date d'entrée du matériel
  date_prevue DATETIME, -- Date prévue de restitution
  date_fin DATETIME, -- Date de fin d'intervention technique
  date_sortie DATETIME, -- Date de sortie/retrait par le client
  tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  -- Statut & priorité
  etat VARCHAR(32) DEFAULT 'PasCommence', -- PasCommence/EnCours/Attente/Fini/Parti
  indice_priorite VARCHAR(32) DEFAULT 'normal', -- 'normal' ou 'urgent'

  -- Infos matériel & problème client
  type_pc VARCHAR(32), -- Tour/Portable/Mobile
  probleme TEXT, -- Description du problème signalé par le client
  Mdpasse VARCHAR(255), -- Mot de passe session utilisateur
  pieces_jointes VARCHAR(255), -- Accessoires fournis par le client

  -- Intervention & résolution
  intervention TEXT, -- Description des travaux réalisés
  commentaire_etat TEXT, -- Note technique sur l'état actuel
  materiel VARCHAR(100), -- Matériel utilisé/remplacé
  prix_materiel DECIMAL(10,2) DEFAULT 0, -- Coût matériel TTC
  nb_heure DECIMAL(6,2) DEFAULT 0, -- Heures de main d'œuvre
  garantie VARCHAR(32), -- oui/non/en partie/hors htp

  -- Suivi & facturation
  consultee INT DEFAULT 0, -- Compteur consultations client (×1000)
  commentaire TEXT, -- Notes internes (non visibles client)
  facturee CHAR(3) DEFAULT 'NON', -- 'OUI'/'NON'
  ref_facture VARCHAR(32), -- Référence facture externe (optionnel)
  fk_facture INTEGER, -- Lien vers llx_facture.rowid si liée

  -- Champs Dolibarr standards
  entity INTEGER DEFAULT 1, -- Multi-sociétés
  import_key VARCHAR(14), -- Clé d'import externe

  -- Index
  INDEX idx_sav86_fiche_ref (ref),
  INDEX idx_sav86_fiche_fk_soc (fk_soc),
  INDEX idx_sav86_fiche_etat (etat),
  INDEX idx_sav86_fiche_date_entree (date_entree),
  INDEX idx_sav86_fiche_date_prevue (date_prevue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;