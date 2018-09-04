<?php
$db = new PDO('mysql:host=localhost;dbname=miniJeuCombat', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.
$manager = new PersonnagesManager($db);


if(isset($_POST['nom'])) {
    
    $_SESSION["nom"] = $_POST["nom"];
}

if (isset($_POST['creer'])){ // Si on a voulu créer un personnage.

  $perso = new Personnage(['nom' => $_POST['nom']]); // On crée un nouveau personnage.

  if (!$perso->nomValide()){
    $message = 'Le nom choisi est invalide.';
    unset($perso);
  }
  elseif ($manager->exists($perso->getnom())) {
    $message = 'Le nom du personnage est déjà pris.';
    unset($perso);
  }
  else {
    $manager->add($perso);
  }

}

elseif (isset($_POST['utiliser'])) // Si on a voulu utiliser un personnage.
{
  if ($manager->exists($_SESSION['nom'])){ // Si celui-ci existe.
    $perso = $manager->get($_SESSION['nom']);
    $now = new DateTime('NOW');
    $diff = $perso->dateDerniereConnexion()->diff($now);

    if ($diff->h + 24*$diff->d > 24){
      $perso->setDateDerniereConnexion($now->format('Y-m-d'));
      if ($perso->getDegats() >= 10) {
          $perso->setDegats($perso->getDegats() - 10);
      }
      $manager->update($perso);
  }
  }else {
    $message = 'Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
  }
}


elseif (isset($_GET['frapper'])) // Si on a cliqué sur un personnage pour le frapper.
{
    $perso = $manager->get($_SESSION['nom']);
    
  if (!isset($perso)) {
    $message = 'Merci de créer un personnage ou de vous identifier.';
  }
  else
  {
    if (!$manager->exists((int) $_GET['frapper'])){

      $message = 'Le personnage que vous voulez frapper n\'existe pas !';

    } else{

      $persoAFrapper = $manager->get((int) $_GET['frapper']);
      $retour = $perso->frapper($persoAFrapper); // On stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper.

      switch ($retour){

        case Personnage::CEST_MOI :
          $message = 'Mais... pourquoi voulez-vous vous frapper O.O ???';
          break;

        case Personnage::PAS_AUJOURDHUI :
          $message = 'Vous avais déjà frappé 5 fois aujourd\'hui. Revenez demain !';
          break;

        case Personnage::PERSONNAGE_FRAPPE :
          $message = 'Le personnage a bien été frappé !';

          $perso->gagnerExperience();

          $manager->update($perso);
          $manager->update($persoAFrapper);
          break;

        case Personnage::PERSONNAGE_TUER :
          $message = 'Vous avez tué ce personnage !';
          $manager->update($perso);
          $manager->delete($persoAFrapper);
          break;
      }
    }
  }
}


