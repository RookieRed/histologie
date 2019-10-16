<?php
function verificationIdentifiant() {
	$idRoot = "uid=ldapreader-toul,ou=sysusers,dc=local";
	$mdpRoot = 'YeEa#hh6e';
			
	$idUtilisateur = $_POST['identifiant'];
	            
	if (filter_var($idUtilisateur, FILTER_VALIDATE_EMAIL))
	{
		list($idUtilisateur, $domaine) = explode("@", $_POST['identifiant']);

		$messageEchec = "Erreur lors de l'authentification aupres du serveur de verification.";
		$connexion = connexionServeur($idRoot, $mdpRoot, $messageEchec);

		if ($connexion != null) 
		{
				
			$filtre = "(uid=".$idUtilisateur.")";
			$recherche = ldap_search($connexion, 'dc=local', $filtre);
			if ($recherche) 
			{
				$resultat = ldap_get_entries($connexion, $recherche);
				if($resultat['count'] > 0) 
				{
					// on place en variable globale le groupe (region-centre-euipe) de l utilisateur
					$GLOBALS['groupe'] =  $resultat[0]["ou"][0];	
					return true;
				} 
				else 
				{
					$mail = $_POST['identifiant'];
					?>
					<div class="alert alert-danger"><center><?="Adresse mail inconnue"?></center></div>
					<?
				}
			} 
			else 
			{
				?>
				<div class="alert alert-danger"><center><?="Erreur lors de la recherche"?></center></div>
				<?
			}
		}
		ldap_close($connexion);
		return false;
	 
	}
	else
	{
		?>
		<div class="alert alert-danger"><center><?="Adresse mail non valide"?></center></div>
	<?
	}
}

function verificationUtilisateur() {

	list($idUtilisateur, $domaine) = explode("@", $_POST['identifiant']);

	//$idUtilisateur = $_POST['identifiant'];
	$mdpConnexion = $_POST['mdp'];
	$idConnexion = "uid=".$idUtilisateur.",ou=users,dc=inserm.fr,dc=local";

	$mail = $idUtilisateur;
	$messageEchec = "Mot de passe incorrect pour l'adresse mail : \"$mail\".";
	$connexion = connexionServeur($idConnexion, $mdpConnexion, $messageEchec);
	// teste la variable $connexion est nulle donc si la connexion s est bien deroulee
	if (isset($connexion))
	{
		ldap_close($connexion);
	}
	return ($connexion != null);
	}
		
	function connexionServeur($idConnexion, $mdpConnexion, $messageEchec) {
	$adresse1 = "ldaps://ldap1.inserm.fr";
	$adresse2 = "ldaps://ldap2.inserm.fr";
	$adresse3 = "ldaps://ldap3.inserm.fr";
			
	$connexionLDAP = ldap_connect($adresse1);
	// comme l inserm est en OpenLDAP, la fonction retournera toujours une ressource et non un booleen
	if ($connexionLDAP) 
	{
		// le @ sert a ne pas envoyer un code erreur 
		$bindLDAP = @ldap_bind($connexionLDAP, $idConnexion, $mdpConnexion);
		if ($bindLDAP) 
		{
			//echo "connexion LDAP réussi";
			return $connexionLDAP;
		} 
		else 
		{
			// essai d une connection sur ldap2 Inserm
			$connexionLDAP = ldap_connect($adresse2);
			if ($connexionLDAP) 
			{
				$bindLDAP = @ldap_bind($connexionLDAP, $idConnexion, $mdpConnexion);
				if ($bindLDAP) 
				{
					//echo "connexion LDAP réussi";
					return $connexionLDAP;
				} 
				else
				{
					// essai d une connection sur ldap3 Inserm
					$connexionLDAP = ldap_connect($adresse3);
					if ($connexionLDAP) 
					{
						$bindLDAP = @ldap_bind($connexionLDAP, $idConnexion, $mdpConnexion);
						if ($bindLDAP) 
						{
							//echo "connexion LDAP réussi";
							return $connexionLDAP;
						} 
					}	
				}	
			
				?>
					<div class="alert alert-danger"><center><?="Identifiant ou mot de passe erroné";?></center></div>
				<?
			}
		}
	} 
	else
	{
		?>
		<div class="alert alert-danger"><center><?="Impossible de joindre le serveur LDAP";?></center></div>
		<?
	}
	return null;
}
?>