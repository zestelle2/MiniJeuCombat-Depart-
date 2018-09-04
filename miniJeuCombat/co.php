<?php // Connexion à la base de données
 try {      
       $bd = new PDO('mysql:host=localhost;dbname=miniJeuCombat;charset=utf8', 'root', '');      
       $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 
    }
     catch(Exception $e) { 
        die('Erreur : '.$e->getMessage());
     }; ?>

