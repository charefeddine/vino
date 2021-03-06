<?php
/**
 * Classe prise en compte lors de la récupération du jeu de résultat PDO.
 * 
 * @package  Vino 
 * @version  1.0
 */
class Classe_Usager
{
	public $id_usager;
	public $admin;
	public $courriel;
	public $nom;
	public $prenom;
	public $mot_de_passe;
	
	public function __construct($id_usager = 0,  $admin = '', $courriel = '', $nom = '', $prenom = '', $mot_de_passe = '')
	{
		$this->id_usager = $id_usager;
		$this->admin = $admin;
		$this->courriel = $courriel;
		$this->nom = $nom;
		$this->prenom = $prenom;
		$this->mot_de_passe = $mot_de_passe;
	}
}
