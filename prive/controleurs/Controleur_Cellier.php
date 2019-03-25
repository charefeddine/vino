<?php
class Controleur_Cellier extends Controleur
{
	protected $modele_bouteille;
	protected $modele_cellier;
	
	public function __construct()
	{
		$this->modele_bouteille = $this->modele('modele_bouteille');
		$this->modele_cellier = $this->modele('modele_cellier');
	}

	public function traite(array $params)
	{
		// On vérifie que l’usagé est bien connecté
		if ( ! isset($_SESSION['id_usager']) )
		{
			header('Location: ' . base_url() );
		}
		switch($params['action'])
		{
			case 'index':
				$this->index();
				break;

			case 'voir':
				$this->voir();
				break;

			// Affichage du formulaire
			case 'ajouter-form':
				$this->ajouter_form();
				break;

			// Ajout d’un cellier
			case 'ajouter':
				$this->ajouter();
				break;

			// Suppression de cellier
			case 'supprimer':
				$this->supprimer();
				break;

			default :
				trigger_error('Action invalide.');
		}
	}

	public function index()
	{
		// Affiche la liste des celliers de l’usager connecté
		$donnees['celliers'] = $this->modele_cellier->obtenir_par_usager($_SESSION['id_usager']);
		$this->afficheVue('modeles/en-tete');
		$this->afficheVue('modeles/menu-usager');
		$this->afficheVue('cellier/liste', $donnees);
		$this->afficheVue('modeles/bas-de-page');
	}

	public function voir()
	{
		// Recuperation de nom de cellier pour l'afficher en haut de la page

		$idCellier = $this->modele_cellier->verifParUsager($_GET['id_cellier'],$_SESSION['id_usager']);

		if ($idCellier == null) {
			header('Location: ' . site_url('login&action=logout') );
		}

		// Recuperation de tous les bouteilles qui appartient a un cellier specifique
		$resultat = $this->modele_cellier->obtenir_par_id($_GET['id_cellier']);
		$donnees['bouteilles'] = $this->modele_bouteille->obtenir_par_id_t($_GET['id_cellier']);
		$monCellier = $resultat[0];
		$donnees['cellier'] = $monCellier->nom;

		$this->afficheVue('modeles/en-tete');
		$this->afficheVue('modeles/menu-usager');
		$this->afficheVue('cellier/cellier', $donnees);
		$this->afficheVue('modeles/bas-de-page');
	}
	
	public function ajouter_form()
	{
		$this->afficheVue('modeles/en-tete');
		$this->afficheVue('modeles/menu-usager');
		$this->afficheVue('cellier/ajouter');
		$this->afficheVue('modeles/bas-de-page');
	}
	
	public function ajouter()
	{
		$this->modele_cellier->ajouter($_SESSION['id_usager']);
		$donnees['celliers'] = $this->modele_cellier->obtenir_par_usager($_SESSION['id_usager']);
		$this->afficheVue('modeles/en-tete');
		$this->afficheVue('modeles/menu-usager');
		$this->afficheVue('cellier/liste', $donnees);
		$this->afficheVue('modeles/bas-de-page');
	}
	
	public function supprimer()
	{
		$body = json_decode(file_get_contents('php://input'));
		$this->modele_cellier->supprimer_par_id($body->id);
		echo json_encode(true);
	}
	
}
