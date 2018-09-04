<?php

session_start(); // On appelle session_start() APRÈS avoir enregistré l'autoload.
if (isset($_GET['deconnexion'])){
  session_destroy();

  header('Location: miniJeu.php');

  exit();
}

//chargement des fichier et des classe
require ('co.php');// fichier de connection
// require 'Class/Personnage.php';
// require 'Class/PersonnagesManager.php';
// require ('combat.php'); 


function chargerClasse($classe){
  require 'Class/' . $classe . '.php'; // On inclut la classe correspondante au paramètre passé.
}

spl_autoload_register('chargerClasse'); // On enregistre la fonction en autoload pour qu'elle soit appelée dès qu'on instanciera une classe non déclarée.
require ('combat.php');
 
?>

<!DOCTYPE html>
<html>
  <head>
    <title>TP : Mini jeu de combat</title>

    <meta charset="utf-8" />
  </head>
  <body>
    <p>Nombre de personnages créés : <?= $manager->count() ?></p>
<?php
if (isset($message)){ // On a un message à afficher ?
  echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}

if (isset($perso)){ // Si on utilise un personnage (nouveau ou pas).
?>
  <p><a href="?deconnexion=1">Déconnexion</a></p>

    <fieldset>
      <legend>Mes informations</legend>
      <p>
        Nom : <?= htmlspecialchars($perso->getNom()) ?><br />
        Dégâts : <?= $perso->getDegats() ?>
        Expérience : <?= $perso->experience() ?>
        Niveau : <?= $perso->niveau() ?>
        Nombre des coups : <?= $perso->nbCoups() ?>
        Date de dernier coup : <?= $perso->dateDernierCoup()->format('d/m/Y') ?>
      </p>
    </fieldset>

    <fieldset>
      <legend>Qui frapper ?</legend>
      <p>
<?php
$persos = $manager->getList($perso->getNom());

if (empty($persos)){

  echo 'Personne à frapper !';
}
else{
  foreach ($persos as $unPerso){
    echo '<a href="?frapper=', $unPerso->getId(), '">'. htmlspecialchars($unPerso->getNom()) . '</a> (dégâts : ' . $unPerso->getDegats() . ', expérience :  ' .$unPerso->experience() .' niveau: ' .$unPerso->niveau() . ' , nombre des coups : '.$unPerso->nbCoups().', date de dernier coup : '.$unPerso->dateDernierCoup()->format('d/m/Y'). ') <br />';
  }
}
?>
      </p>
    </fieldset>
<?php
}
else
{
?>
    <form action="" method="post">
      <p>
        Nom : <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Créer ce personnage" name="creer" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" />
      </p>
    </form>
<?php
}
?>
  </body>
</html>
<?php

if (isset($perso)) // Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
{
  $_SESSION['perso'] = $perso;
}
?>

<?php

//Les données correspondante aux Personnage
/*$donnees = [
    'id' => 1,
    'nom' => 'Gladiatuer',
    'degats' => 55,
];
*/


//creation de personnage
//$perso1= new Personnage($donnees);

//creation du personnage Manager

//$manager = new PersonnagesManager($db);
//$manager->add($perso1);
