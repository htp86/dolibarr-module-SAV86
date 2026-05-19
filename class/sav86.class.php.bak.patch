<?php
/**
 * Class HTPSav86 - Gestion des interventions SAV86
 * Version 20260421 Build 1634 - CORRECTION suppression statut (n'existe pas en BDD)
 * /volume1/web/dolibarr_test/htdocs/custom/sav86/class/sav86.class.php
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

class HTPSav86 extends CommonObject
{
    public $element       = 'sav86';
    public $table_element = 'sav86_fiche';
    public $picto         = 'generic';

    public $id;
    public $ref;
    public $entity;
    public $fk_soc;

    public $fk_user_creat;
    public $fk_user_valid;

    public $date_entree;
    public $date_prevue;
    public $date_fin;
    public $date_sortie;

    public $indice_priorite;
    public $probleme;
    public $type_pc;
    public $pieces_jointes;
    public $Mdpasse;
    public $format;
    public $etat;
    public $garantie;

    public $nb_heure;
    public $materiel;
    public $prix_materiel;
    public $intervention;

    public $commentaire;

    // ❌ SUPPRIMÉ : public $statut; (colonne n'existe pas dans la table)

    /**
     * Constructor
     */
    public function __construct($db)
    {
        $this->db     = $db;
        $this->entity = getEntity('sav86');
    }

    /**
     * Get next number for interventions
     */
    private function getNextNumber()
    {
        global $conf;

        if (empty($conf->global->SAV86_NEXTNUM)) {
            dolibarr_set_const($this->db, 'SAV86_NEXTNUM', 1, 'integer', 0, '', $this->entity);
            $conf->global->SAV86_NEXTNUM = 1;
        }

        return (int) $conf->global->SAV86_NEXTNUM;
    }

    /**
     * Increment next number
     */
    private function incrementNextNumber()
    {
        global $conf;
        $newvalue = ((int) $conf->global->SAV86_NEXTNUM) + 1;

        dolibarr_set_const($this->db, 'SAV86_NEXTNUM', $newvalue, 'integer', 0, '', $this->entity);
        $conf->global->SAV86_NEXTNUM = $newvalue;
    }

    /**
     * Create intervention in database
     */
    public function create($user)
    {
        $this->fk_user_creat = $user->id;
        $this->datec = time();

        // Générer la référence automatique
        $this->ref = 'SAV86-' . str_pad($this->getNextNumber(), 6, '0', STR_PAD_LEFT);

        $this->db->begin();

        $sql = "INSERT INTO ".MAIN_DB_PREFIX."sav86_fiche(";
        $sql.= "entity, ref, fk_soc, fk_user_creat, ";
        $sql.= "date_entree, date_prevue, date_creation, indice_priorite, ";
        $sql.= "probleme, type_pc, pieces_jointes, Mdpasse, `format`, etat, garantie, ";
        $sql.= "fk_user_valid, nb_heure, materiel, prix_materiel, intervention, ";
        $sql.= "date_fin, date_sortie, commentaire";
        $sql.= ") VALUES (";
        $sql.= (int)$this->entity.", ";
        $sql.= "'".$this->db->escape($this->ref)."', ";
        $sql.= (int)$this->fk_soc.", ";
        $sql.= (int)$this->fk_user_creat.", ";
        $sql.= ($this->date_entree ? "'".date('Y-m-d H:i:s', $this->date_entree)."'" : "NULL").", ";
        $sql.= ($this->date_prevue ? "'".date('Y-m-d H:i:s', $this->date_prevue)."'" : "NULL").", ";
        $sql.= ($this->datec ? "'".date('Y-m-d H:i:s', $this->datec)."'" : "NULL").", ";
        $sql.= "'".$this->db->escape($this->indice_priorite)."', ";
        $sql.= "'".$this->db->escape($this->probleme)."', ";
        $sql.= "'".$this->db->escape($this->type_pc)."', ";
        $sql.= "'".$this->db->escape($this->pieces_jointes)."', ";
        $sql.= "'".$this->db->escape($this->Mdpasse)."', ";
        $sql.= "'".$this->db->escape($this->format)."', ";
        $sql.= "'".$this->db->escape($this->etat)."', ";
        $sql.= "'".$this->db->escape($this->garantie)."', ";
        $sql.= (int)$this->fk_user_valid.", ";
        $sql.= (float)$this->nb_heure.", ";
        $sql.= "'".$this->db->escape($this->materiel)."', ";
        $sql.= (float)$this->prix_materiel.", ";
        $sql.= "'".$this->db->escape($this->intervention)."', ";
        $sql.= ($this->date_fin ? "'".date('Y-m-d H:i:s', $this->date_fin)."'" : "NULL").", ";
        $sql.= ($this->date_sortie ? "'".date('Y-m-d H:i:s', $this->date_sortie)."'" : "NULL").", ";
        $sql.= "'".$this->db->escape($this->commentaire)."'";
        $sql.= ")";

        if (!$this->db->query($sql)) {
            $this->error = $this->db->lasterror();
            $this->db->rollback();
            return -1;
        }

        $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."sav86_fiche");

        // Incrémenter le compteur
        $this->incrementNextNumber();

        $this->db->commit();
        return $this->id;
    }

    /**
     * Load object from database
     * Version 20260421 Build 1600 - CORRECTION suppression statut
     */
    public function fetch($id)
    {
        $sql = "SELECT * FROM ".MAIN_DB_PREFIX."sav86_fiche WHERE rowid=".(int)$id;
        $res = $this->db->query($sql);

        if (!$res) return -1;
        if ($this->db->num_rows($res) == 0) return 0;

        $obj = $this->db->fetch_object($res);

        $this->id                = $obj->rowid;
        $this->ref               = $obj->ref;
        $this->fk_soc            = $obj->fk_soc;
        $this->fk_user_creat     = $obj->fk_user_creat;
        $this->fk_user_valid     = $obj->fk_user_valid;

        // CONVERSION DES DATES : MySQL DATETIME → Timestamp Unix
        $this->date_entree       = !empty($obj->date_entree) ? strtotime($obj->date_entree) : null;
        $this->date_prevue       = !empty($obj->date_prevue) ? strtotime($obj->date_prevue) : null;
        $this->date_fin          = !empty($obj->date_fin) ? strtotime($obj->date_fin) : null;
        $this->date_sortie       = !empty($obj->date_sortie) ? strtotime($obj->date_sortie) : null;

        $this->indice_priorite   = $obj->indice_priorite;
        $this->probleme          = $obj->probleme;
        $this->type_pc           = $obj->type_pc;
        $this->pieces_jointes    = $obj->pieces_jointes;
        $this->Mdpasse           = $obj->Mdpasse;
        $this->format            = $obj->format;
        $this->etat              = $obj->etat;
        $this->garantie          = $obj->garantie;

        $this->nb_heure          = $obj->nb_heure;
        $this->materiel          = $obj->materiel;
        $this->prix_materiel     = $obj->prix_materiel;
        $this->intervention      = $obj->intervention;

        $this->commentaire       = $obj->commentaire;

        // ❌ SUPPRIMÉ : $this->statut = $obj->statut; (colonne n'existe pas)

        return 1;
    }

    /**
     * Update intervention in database
     */
    public function update($user)
    {
        $this->fk_user_valid = $user->id;
        $this->tms = time();

        $sql = "UPDATE ".MAIN_DB_PREFIX."sav86_fiche SET ";
        $sql.= "fk_user_valid=".(int)$this->fk_user_valid.", ";
        $sql.= "fk_soc=".(int)$this->fk_soc.", ";
        $sql.= "date_entree=".($this->date_entree ? "'".date('Y-m-d H:i:s', $this->date_entree)."'" : "NULL").", ";
        $sql.= "date_prevue=".($this->date_prevue ? "'".date('Y-m-d H:i:s', $this->date_prevue)."'" : "NULL").", ";
        $sql.= "indice_priorite='".$this->db->escape($this->indice_priorite)."', ";
        $sql.= "probleme='".$this->db->escape($this->probleme)."', ";
        $sql.= "type_pc='".$this->db->escape($this->type_pc)."', ";
        $sql.= "pieces_jointes='".$this->db->escape($this->pieces_jointes)."', ";
        $sql.= "Mdpasse='".$this->db->escape($this->Mdpasse)."', ";
        $sql.= "`format`='".$this->db->escape($this->format)."', ";
        $sql.= "etat='".$this->db->escape($this->etat)."', ";
        $sql.= "garantie='".$this->db->escape($this->garantie)."', ";
        $sql.= "nb_heure=".(float)$this->nb_heure.", ";
        $sql.= "materiel='".$this->db->escape($this->materiel)."', ";
        $sql.= "prix_materiel=".(float)$this->prix_materiel.", ";
        $sql.= "intervention='".$this->db->escape($this->intervention)."', ";
        $sql.= "date_fin=".($this->date_fin ? "'".date('Y-m-d H:i:s', $this->date_fin)."'" : "NULL").", ";
        $sql.= "date_sortie=".($this->date_sortie ? "'".date('Y-m-d H:i:s', $this->date_sortie)."'" : "NULL").", ";
        $sql.= "commentaire='".$this->db->escape($this->commentaire)."' ";
        $sql.= "WHERE rowid=".(int)$this->id;

        return $this->db->query($sql);
    }

    /**
     * Delete intervention from database
     */
    public function delete($user)
    {
        $this->db->begin();

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."sav86_fiche WHERE rowid = ".((int)$this->id);

        $res = $this->db->query($sql);

        if (!$res) {
            $this->error = $this->db->lasterror();
            $this->db->rollback();
            return -1;
        }

        $this->db->commit();
        return 1;
    }

    /**
     * Return label of status
     *
     * @param   string  $status     Status code
     * @param   int     $mode       0=libelle, 1=short, 2=Picto short
     * @return  string              Label of status
     */
    public static function LibStatut($status, $mode = 0)
    {
        $label = '';
        $picto = '';

        switch($status) {
            case 'PasCommence':
                $label = 'PC pas commencé';
                $picto = 'statut0';
                break;
            case 'EnCours':
                $label = 'PC en cours';
                $picto = 'statut4';
                break;
            case 'Attente':
                $label = 'PC en attente';
                $picto = 'statut6';
                break;
            case 'Fini':
                $label = 'PC fini';
                $picto = 'statut9';
                break;
            case 'Parti':
                $label = 'Client venu chercher PC';
                $picto = 'statut8';
                break;
        }

        if ($mode == 0) return $label;
        if ($mode == 1) return $label;
        if ($mode == 2) return $picto;
        return $label;
    }
}