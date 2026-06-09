<?php
/**
 * Module descriptor for SAV86
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

class modSav86 extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;

        $this->numero = 500860;
        $this->rights_class = 'sav86';
        $this->family = "other";
        $this->module_position = '90';
        
		$this->changelog = 'ChangeLog.md';

        // ✅ FIX PRINCIPAL : Nom fixe en minuscules
        $this->name = 'sav86';
        
        $this->description = "Gestion des interventions SAV en atelier - Réparation informatique";
        $this->descriptionlong = "Module de gestion des interventions de dépannage informatique en atelier.";
        $this->version = '1.1.0';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto = 'generic';
		
		// Config pages - Pour faire apparaître la roue dentée
		$this->config_page_url = array('/custom/sav86/admin/setup.php');
        
        // Fichiers de langue
        $this->langfiles = array("sav86@sav86");

        // ✅ Permissions : structure à 3 niveaux comme DoliReset
        $this->rights = array();
        $r = 0;
        
        $this->rights[$r][0] = 500860001;
        $this->rights[$r][1] = 'Lire les interventions SAV';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'sav86_fiche';  // niveau intermédiaire
        $this->rights[$r][5] = 'read';          // action
        $r++;
        
        $this->rights[$r][0] = 500860002;
        $this->rights[$r][1] = 'Créer/modifier les interventions SAV';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'sav86_fiche';
        $this->rights[$r][5] = 'write';
        $r++;
        
        $this->rights[$r][0] = 500860003;
        $this->rights[$r][1] = 'Supprimer les interventions SAV';
        $this->rights[$r][3] = 1;
        $this->rights[$r][4] = 'sav86_fiche';
        $this->rights[$r][5] = 'delete';
        $r++;

        // ✅ Menus : structure top menu comme DoliReset
        $this->menu = array();
        $r = 0;
        
        $this->menu[$r++] = array(
            'fk_menu' => '',  // top menu
            'type' => 'top',
            'titre' => 'SAV86',
            'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
            'mainmenu' => 'sav86',
            'leftmenu' => '',
            'url' => '/custom/sav86/htdocs/sav86/sav86_list.php',
            'langs' => 'sav86@sav86',
            'position' => 1000 + $r,
            'enabled' => '$user->rights->sav86->sav86_fiche->read',
            'perms' => '$user->rights->sav86->sav86_fiche->read',
            'target' => '',
            'user' => 0
        );

        // Tables SQL à créer
        $this->tables = array('sav86_fiche');
    }

public function init($options = '')
{
    global $conf, $langs;

    // Enregistrer explicitement le dossier de langues
    if (is_dir(DOL_DOCUMENT_ROOT.'/custom/sav86/langs')) {
        $langs->load("sav86@sav86", DOL_DOCUMENT_ROOT.'/custom/sav86/langs');
    }

    // Charger les tables SQL
    $result = $this->_load_tables('/sav86/sql/');
    if ($result < 0) {
        return -1;
    }

    // ✅ CRÉATION DES CONSTANTES DE CONFIGURATION
    // Conditions générales (valeur par défaut = texte complet)
    $default_cgv = "1. La date de retour figurant sur ce document est à titre indicatif, merci de nous contacter par téléphone afin de vous assurer de la disponibilité de votre matériel.\n\n".
    "2. Les tarifs en vigueur pour les opérations de maintenance sont consultables dans notre magasin. Le client s'engage ici à en avoir pris connaissance.\n\n".
    "3. Toutes les opérations effectuées sur un matériel ne faisant pas l'objet d'une garantie dans un de nos magasins seront facturées aux tarifs en vigueur, pour un minimum forfaitaire de 25 euros TTC.\n\n".
    "4. Notre SAV n'interviendra que sur les pannes constatées par nos soins et/ou figurant dans l'encart \"Description du problème\" du recto de ce document. Le client devant s'assurer que le problème décrit est bien celui qu'il rencontre.\n\n".
    "5. La société HTP Multimedi@ ne pourra être tenue responsable quant aux pertes de données ou aux défaillances survenues sur le matériel pendant l'intervention par notre service technique ainsi que lors de son transport.\n\n".
    "6. Toute installation de logiciel faisant l'objet d'une licence d'exploitation ou d'utilisation ne pourra être réalisée que si cette licence nous est au préalable fournie par le client. Aucune copie/photocopie/télécopie ne sera acceptée.\n\n".
    "7. La société HTP Multimedi@ s'engage à effectuer un échange direct de toute pièce en garantie dans notre magasin si celle-ci est en stock et après constatation de la panne par nos techniciens. Toutefois si cet article n'est plus commercialisé, il fera l'objet d'un retour chez le fournisseur. Le délai d'immobilisation ne dépendra alors plus de notre service technique qui ne pourra pas être tenu responsable du délai de retour de la pièce.\n\n".
    "8. Si le matériel n'est pas déposé par son propriétaire, le commanditaire en devient alors responsable et doit être en mesure de nous fournir les explications nécessaires.\n\n".
    "9. La non-présentation de ce document lors d'un retrait de matériel pourra faire l'objet d'un refus de livraison.\n\n".
    "10. Dans le cas d'un test de pièce détachée, si la panne n'est pas constatée, le client devra alors nous fournir le restant de l'environnement (en général l'ordinateur) pour affiner les tests. Dans le cas contraire, la société HTP Multimedi@ se réserve le droit de facturer les tests effectués même si l'article est en garantie dans nos magasins.\n\n".
    "11. La société HTP Multimedi@ s'engage à conserver les matériels en diagnostic 2 Mois après la date de rendue prévue indiquée sur la fiche d'intervention. Au-delà HTP Multimedi@ vous notifiera alors votre abandon du produit à ou aux coordonnées fournies lors de la demande de réparation.\n\n".
    "12. Dans le cas où le produit serait abandonné, HTP Multimedi@ peut en disposer conformément aux dispositions légales applicables, et, de manière spécifique, peut vendre votre produit à l'occasion d'une vente privée ou publique de manière à couvrir les frais d'exécution des services impayés.\n\n".
    "13. HTP Multimedi@ se réserve tous droits et privilèges légaux sur le produit déposé en réparation pour les charges impayées.";
    
    dolibarr_set_const($this->db, 'SAV86_CONDITIONS_GENERALES', $default_cgv, 'chaine', 0, '', $this->entity);
    dolibarr_set_const($this->db, 'SAV86_AFFICHER_CGV', '1', 'bool', 0, '', $this->entity);
    dolibarr_set_const($this->db, 'SAV86_ALERT_MDP_VIDE', '0', 'bool', 0, '', $this->entity);
	// Contact info (valeurs par défaut)
	dolibarr_set_const($this->db, 'SAV86_CONTACT_TEL', '05.49.88.30.90', 'chaine', 0, '', $this->entity);
	dolibarr_set_const($this->db, 'SAV86_CONTACT_EMAIL', 'htp-sav86@htpmultimedia.fr', 'chaine', 0, '', $this->entity);
	dolibarr_set_const($this->db, 'SAV86_CONTACT_JOURS', 'Lun-Ven', 'chaine', 0, '', $this->entity);
	dolibarr_set_const($this->db, 'SAV86_CONTACT_HORAIRES', '9h-12h30 / 14h30-18h30', 'chaine', 0, '', $this->entity);

    $sql = array();
    return $this->_init($sql, $options);
}

    public function remove($options = '')
    {
        $sql = array();
        return $this->_remove($sql, $options);
    }
}