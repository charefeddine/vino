<?php
	class BouteilleSaq
	{
		public $id_bouteille_saq;
		public $code_saq;
		public $prix;
		public $millesime;
		public $id_type;
		public $pays;
		public $format;
		public $nom;
		
		public function __construct($id_bouteille_saq = 0, $code_saq = '', $prix = 0, $millesime = 0, $id_type = 0, $pays ='', $format = '', $nom = '')
		{
			$this->id_bouteille_saq = $id_bouteille_saq;
			$this->code_saq = $code_saq;
			$this->prix = $prix;
			$this->millesime = $millesime;
			$this->id_type = $id_type;
			$this->pays = $pays;
			$this->format = $format;
			$this->nom = $nom;
		}
	}