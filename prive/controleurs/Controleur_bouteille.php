<?php
	class Controleur_Bouteille extends BaseControleur
	{
		public function traite(array $params)
		{
			switch($params['action'])
			{
				case 'index':
					$modeleBouteille = $this->getDAO('Bouteille');
					$donnees['bouteilles'] = $modeleBouteille->obtenir_tous();
					$this->afficheVue('modeles/en-tete');
					$this->afficheVue('modeles/menu-usager');
					$this->afficheVue('cellier', $donnees);
					$this->afficheVue('modeles/bas-de-page');
					break;

				case 'visiterCellier':
					$modeleBouteille = $this->getDAO('Bouteille');
					$donnees['bouteilles'] = $modeleBouteille->obtenir_par_id_cellier($_GET['id']);
					$this->afficheVue('modeles/en-tete');
					$this->afficheVue('modeles/menu-usager');
					$this->afficheVue('cellier', $donnees);
					$this->afficheVue('modeles/bas-de-page');
					break;

				case 'modifier-form':
					$modeleBouteille = $this->getDAO('Bouteille');
					$donnees['bouteille'] = $modeleBouteille->obtenir_par_id($_GET['id']);
					$modeleType = $this->getDAO('Type');
					$donnees['types'] = $modeleType->obtenir_tous();
					$modeleCellier = $this->getDAO('Cellier');
					$donnees['celliers'] = $modeleCellier->obtenir_tous();
					$donnees['titre'] = 'Modifier Bouteille';
					$donnees['actionBouton'] = 'modifier';
					$donnees['titreBouton'] = 'Modifier la bouteille';
					$this->afficheVue('modeles/en-tete');
					$this->afficheVue('modeles/menu-usager');
					$this->afficheVue('bouteille/formulaire', $donnees);
					$this->afficheVue('modeles/bas-de-page');
					break;

				case 'modifier':
					$modeleBouteille = $this->getDAO('Bouteille');
					$modeleBouteille->modifierBouteille();
					$donnees['bouteilles'] = $modeleBouteille->obtenir_tous();
					echo '<script>alert("La bouteille a été modifiée.")</script>';
					$this->afficheVue('modeles/en-tete');
					$this->afficheVue('modeles/menu-usager');
					$this->afficheVue('cellier', $donnees);
					$this->afficheVue('modeles/bas-de-page');
					break;

				case 'ajouter':
					$modeleBouteille = $this->getDAO('Bouteille');
					$modeleBouteille->ajouterUneBouteille();
					$donnees['bouteilles'] = $modeleBouteille->obtenir_tous();
					echo '<script>alert("La bouteille a été ajoutée.")</script>';
					$this->afficheVue('modeles/en-tete');
					$this->afficheVue('modeles/menu-usager');
					$this->afficheVue('cellier', $donnees);
					$this->afficheVue('modeles/bas-de-page');
					break;

				case 'boire-js':
					$body = json_decode(file_get_contents('php://input'));
					$modeleBouteille = $this->getDAO('Bouteille');
					$modeleBouteille->modifierQuantiteBouteilleCellier($body->id,-1);
					$resultat = $modeleBouteille->recupererQuantiteBouteilleCellier($body->id);	
					echo json_encode($resultat);
					break;
					
				case 'ajouter-js':
					$body = json_decode(file_get_contents('php://input'));
					$modeleBouteille = $this->getDAO('Bouteille');
					$modeleBouteille->modifierQuantiteBouteilleCellier($body->id, 1);
					$resultat = $modeleBouteille->recupererQuantiteBouteilleCellier($body->id);	
					echo json_encode($resultat);
					break;
					
				case 'ajouter-form':
					$modeleType = $this->getDAO('Type');
					$donnees['types'] = $modeleType->obtenir_tous();
					$modeleCellier = $this->getDAO('Cellier');
					$donnees['celliers'] = $modeleCellier->obtenir_tous();
					$donnees['titre'] = 'Ajouter Bouteille';
					$donnees['actionBouton'] = 'ajouter';
					$donnees['titreBouton'] = 'Ajouter la bouteille';
					$this->afficheVue('modeles/en-tete');
					$this->afficheVue('modeles/menu-usager');
					$this->afficheVue('bouteille/formulaire', $donnees);
					$this->afficheVue('modeles/bas-de-page');
					break;

				case 'saisie-semi-automatique':
					$body = json_decode(file_get_contents('php://input'));
					//var_dump($body->nom);die;
					$modeleBouteille = $this->getDAO('Bouteille');
					$listeBouteilles = $modeleBouteille->autocomplete($body->nom);
					echo json_encode($listeBouteilles);
					break;

				case 'alex':
					// echo "coucou";die;
					$modeleBouteille = $this->getDAO('Bouteille');
					$listeBouteilles = $modeleBouteille->autocomplete('a');
					break;

				default :
					trigger_error('Action invalide.');
			}
		}
	}
