<main class="mdl-layout__content">
	<div class="demo-card-wide mdl-card mdl-shadow--2dp">
		<?php 
			if(isset($donnees['usager'])){
				$usager = $donnees['usager'];
			}
			$courriel     = isset($usager->courriel) ? $usager->courriel : '';
			$nom          = isset($usager->nom) ? $usager->nom : '';
			$prenom       = isset($usager->prenom) ? $usager->prenom : '';
			//$mot_de_passe       = isset($usager->mot_de_passe) ? $usager->mot_de_passe : '';
			$id_usager = isset($usager->id_usager) ? $usager->id_usager : '';
			//var_dump($id_usager);
		?>
		<div class="mdl-card__title">	
			<h2 class="mdl-card__title-text">Modifier votre compte</h2>
		</div>
		<div class="mdl-card__supporting-text">
			
			<form method="POST">				
				<div class="mdl-textfield mdl-js-textfield">
					Courriel : 
						<input class="mdl-textfield__input" type="text" name="courriel" value="<?php echo $courriel ?>" required="required">
				</div>
				<div class="mdl-textfield mdl-js-textfield">
					nom : 
						<input class="mdl-textfield__input" type="text" name="nom" value="<?php echo $nom ?>" required="required">
				</div>
				<div class="mdl-textfield mdl-js-textfield">
					prenom : 
						<input class="mdl-textfield__input" type="text" name="prenom" value="<?php echo $prenom ?>" required="required">
				</div>
				<div class="mdl-textfield mdl-js-textfield">
					Tapez votre ancien mot de passe : 
					<span data-id='' class='nom_bouteille'>
						<input class="mdl-textfield__input" type="password" name="mot_de_passe" value="">
					</span>
				</div>
				<div class="mdl-textfield mdl-js-textfield">
					Tapez votre nouveau mot de passe :
						<input class="mdl-textfield__input" type="password"  name="mdp1" value="">
				</div>

				<div class="mdl-textfield mdl-js-textfield">
					Confirmer votre mot de passe : 
					
						<input class="mdl-textfield__input" type="password" name="mdp2"  value="">
				</div>
				
			
				<div>
					<?php
						if(isset($id_usager))
						{
					?>
					<input type="hidden" name="id_usager" value="<?php echo $id_usager ?>">
					<?php
						}
					?>
					<input type="hidden" name="action" value="modifier">
					<input type="submit" value="Modifier l’usager" class="mdl-button mdl-js-button mdl-button--raised btnModifierUsager">
				</div>
			</form>
			<?php
					
					// Gestion des erreurs
					if (isset($donnees['erreurs'])) 
					{
						if($donnees['erreurs'] != '')
							{
								echo '<p class="message"><i class="fas fa-exclamation"></i>' . $donnees['erreurs'] . '</p>';
							}
					}
					
				?>
	</div>
	</div>
<!-- </main> -->

<script type="text/javascript">
		window.addEventListener("load", function(){
			document.getElementById("moncompte").classList.add("active");
			document.getElementById("listes_achat").classList.remove("active");
			document.getElementById("cellier").classList.remove("active");
	},  false)
</script>