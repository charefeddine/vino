<?php
/**
 * Class MonSQL
 * Classe qui génère ma connection à MySQL à travers un singleton
 *
 * @author Alexandre Pachot
 * @author Fatemeh Homatash
 * @version 1.0
 */
class SAQ extends Modele {
	const DUPLICATION = 'duplication';
	const ERREURDB = 'erreurdb';

	private static $_pageweb;
	private static $_status;
	private $_stmt_bouteille_saq;
	private $_stmt_type;

	 public function __construct() {
		parent::__construct();
		try {
			$this->_stmt_bouteille_saq = $this->_bd->prepare('INSERT INTO vino_bouteille_saq (nom, millesime, id_type, pays, format, code_saq, prix) 
			VALUES (?, ?, ?, ?, ?, ?, ?)');
			$this->_stmt_type = $this->_bd->prepare('INSERT INTO vino_type (type) VALUES (?)');
		} catch (Exception $e) {
			echo "Echec de la préparation : (" . $mysqli->errno . ") " . $mysqli->error;
		}	
	 }

	/**
	 * getProduits
	 * @param int $debut
	 * @param int $nombre
	 */
	public function getProduits($debut = 0, $nombre = 10) {
		// Initialisation du gestionnaire du client URL.
		$gc = curl_init();

		// URL à récupérer.
		curl_setopt($gc, CURLOPT_URL, 'https://www.saq.com/webapp/wcs/stores/servlet/SearchDisplay?storeId=20002&searchTerm=vin&beginIndex=' . $debut . '&pageSize=' . $nombre);

		// Retourne directement le transfert sous forme de chaine au lieu de l’afficher directement.
		curl_setopt($gc, CURLOPT_RETURNTRANSFER, true);

		// Pour que le php laisse accesse a https
		curl_setopt($gc, CURLOPT_SSL_VERIFYPEER, false);

		// Exécution de la session cURL.
		self::$_pageweb = curl_exec($gc);

		// Lecture du dernier code de réponse.
		self::$_status = curl_getinfo($gc, CURLINFO_HTTP_CODE);

		// Fermeture de la session.
		curl_close($gc);

		$doc = new DOMDocument();

		// Activation du mode « recovery », c.-à-d. tentative d’analyser un document mal formé.
		$doc->recover = true;

		// Ne lance pas une DOMException en cas d’erreur.
		$doc->strictErrorChecking = false;

		// Chargement du code HTML à partir d’une chaîne de caractères (self::$_pageweb)
		// @ : permet de ne pas afficher l’éventuel message d’erreur que pourrait retourner la fonction
		@$doc->loadHTML(self::$_pageweb);

		// Recherche tous les éléments qui ont une balise <div>
		$elements = $doc->getElementsByTagName('div');

		$nombreDeProduits = 0;

		foreach ($elements as $noeud) {
			if (strpos($noeud->getAttribute('class'), 'resultats_product') !== false) {
				$info = self::recupereInfo($noeud);
				//var_dump($info);
				$retour = $this->ajoutProduit($info);
				if ($retour->succes == false) {
					$retour->raison;
				} else {
					$nombreDeProduits++;
				}
			}
		}
		return $nombreDeProduits;
	}

	private function get_inner_html($node) {
		$innerHTML = '';
		$children = $node -> childNodes;
		foreach ($children as $child) {
			$innerHTML .= $child -> ownerDocument -> saveXML($child);
		}
		return $innerHTML;
	}

	private function recupereInfo($noeud) {
		// Objet qui va contenir mes informations de la bouteille
		$info = new stdClass();

		// Recherche tous les éléments qui ont une balise <p>
		$paragraphes = $noeud->getElementsByTagName('p');

		foreach ($paragraphes as $paragraphe) {
			switch($paragraphe->getAttribute('class')) {
				// Nom de la bouteille et son millesime
				case 'nom':
					preg_match("/\r\n\s*(.*)(.{4})\r\n/", $paragraphe->textContent, $correspondances);
					if (intval($correspondances[2])) {
						$info->nom = trim($correspondances[1]);
						$info->millesime = intval($correspondances[2]);
					} else {
						$info->nom = $correspondances[1] . $correspondances[2];
						$info->millesime = NULL;
					}
					break;

				// Description de la bouteille
				case 'desc' :
					// Récupération des chaines de caractères excluant les retours charriot et les espaces de début
					preg_match_all("/\r\n\s*(.*)\r\n/", $paragraphe->textContent, $correspondances);

					// Récupération de l’information de la première ligne
					if (isset($correspondances[1][0])) {
						$info->type = trim($correspondances[1][0]);
					}

					// Récupération de l’information de la deuxième ligne
					if (isset($correspondances[1][1])) {
						// Ex: "Arménie (République d'), 750 ml" ou "Arménie (République d'), 1,5 L"
						preg_match("/(.*),(.*)/", $correspondances[1][1], $corres);

						// Remplacement de l’apostrophe droite (') par l’apostrophe typographique (’)
						$info->pays = str_replace("'", '’', $corres[1]);

						// Remplacement du séparateur décimal, format base de données
						$info->format = $corres[2];
					}

					// Récupération de l’information de la troisième ligne
					if (isset($correspondances[1][2])) {
						preg_match("/\d{8}/", $correspondances[1][2], $corres);
						$info->code_SAQ =  $corres[0];
					}
					break;
			}
		}		
		// Récupération du prix
		$cellules = $noeud->getElementsByTagName("td");
		foreach ($cellules as $cellule) {
			if ($cellule->getAttribute('class') == 'price') {
				preg_match("/(\d*),(\d*).*$/", trim($cellule->textContent), $correspondances);
				$info->prix = $correspondances[1] . "." . $correspondances[2];
			}
		}
		return $info;
	}

	private function ajoutProduit($bte) {
		$retour = new stdClass();
		$retour->succes = false;
		$retour->raison = "";
		$dernierId = "";

		// Récupère le type reçu en paramètre 
		$rangeeType = $this->_bd->query("SELECT id_type FROM vino_type WHERE type = '" . $bte->type . "'");
		
		// Vérifier si les rangées ne sont pas vides
		if ($rangeeType->num_rows == 1 ) {
			// Récupère le id de type de vin
			$id_type = $rangeeType->fetch_assoc();
			$id_type = $id_type['id_type'];

		} else {
			// Ajouter le type dans la table de type
			$this->_stmt_type->bind_param("s", $bte->type);
			$this->_stmt_type->execute();
			$id_type = $this->_stmt_type->insert_id;
		}
		
		// Récupère le code_saq pour vérifier après si il existe dans la table ou non
		 $rangeeCodeSaq = $this->_bd->query("SELECT id_bouteille_saq FROM vino_bouteille_saq WHERE code_saq = '" . $bte->code_SAQ . "'");

		//Si le code_saq n'existe pas dans le tableau
		if ($rangeeCodeSaq->num_rows < 1) {			
			$this->_stmt_bouteille_saq->bind_param("siissid", $bte->nom, $bte->millesime, $id_type, $bte->pays, $bte->format, $bte->code_SAQ, $bte->prix);
			$retour->succes = $this->_stmt_bouteille_saq->execute();
		} else {
			$retour->succes = false;
			$retour->raison = self::DUPLICATION;
		}
	return $retour;
	}

	public function obtenirBouteillesSaq() {
		$bouteillesSaq = array();
		$resultat = $this->_bd->query("SELECT bs.id_bouteille_saq AS id,
					bs.code_saq AS code_saq,
					bs.prix AS prix,
					bs.millesime AS millesime,
					bs.pays AS pays,
					bs.format AS format,
					bs.nom AS nom,
					t.type AS type
					FROM vino_bouteille_saq bs
					INNER JOIN vino_type t
					ON bs.id_type = t.id_type
					ORDER BY id");
		//Verifie si il a recu une resultat, si oui il fait un fetch et les mets dans le tableau $resltat
		if ($resultat->num_rows) {
			while ($chaqueResultat = $resultat->fetch_assoc()) {
				$bouteillesSaq[] = $chaqueResultat;
			}
		}
		else {
			throw new Exception("Erreur de requête sur la base de donnée", 1);
		}
		return $bouteillesSaq;
	}
}