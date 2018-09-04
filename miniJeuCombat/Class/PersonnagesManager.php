<?php


class PersonnagesManager
{
  private $db; //instance de PDO

  public function __construct($db)
  {
    $this->setDb($db);
  }

/* ENREGISTRER UN NOUVEAU PERSONNAGE */
  public function add(Personnage $perso)
  {
    // Préparation de la requête d'insertion.
    $req = $this->db->prepare('INSERT INTO personnages(nom, dateDerniereConnexion) VALUES (:nom, NOW())');

    // Assignation des valeurs pour le nom, la force, les dégâts, l'expérience et le niveau du personnage.
    $req->bindValue(':nom', $perso->getNom());
    
    // Exécution de la requête.
    $req->execute();

    //LA DATE ACTUEL
    $now = new DateTime('NOW');

    //Hydratation du personnage passé en paramètre avec assignation de son identifiant et des dégâts initiaux (= 0).
    $perso->hydrate([
      'id' => $this->db->lastInsertId(),
      'degats' => 0,
      'experience' => 0,
      'niveau' => 1,
      'nbCoups' => 0,
      'dateDernierCoup' => '0000-00-00',
      'dateDerniereConnexion' => $now->format('Y-m-d')]);
  }

/*COMPTE LE NOMBRE DE PERSONNAGE */
  public function count()
  {
    // Exécute une requête COUNT() et retourne le nombre de résultats retourné.
    return $this->db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
  }

/* SUPPRIMER UN NOUVEAU PERSONNAGE */ 
  public function delete(Personnage $perso)  
  {    
    $this->db->exec('DELETE FROM personnages WHERE id = '.$perso->getId()); 
  }

 /* enregistrer un nouveau personnage */
 public function exists($info)
 {
   // Si le paramètre est un entier, c'est qu'on a fourni un identifiant.
   if (is_int($info)){ // On veut voir si tel personnage ayant pour id $info existe.
       
    // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean.
     return (bool) $this->db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();

   }

    // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.
    $req = $this->db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
    
    // Exécution d'une requête COUNT() avec une clause WHERE, et retourne un boolean.
    $req->execute([':nom' => $info]);

    return (bool) $req->fetchColumn();

 }

 public function get($info)
 {
   // Si le paramètre est un entier, on veut récupérer le personnage avec son identifiant.
   if (is_int($info)){
      
    // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
      $req = $this->db->query('SELECT id, nom, degats, experience, niveau, nbCoups, dateDernierCoup, dateDerniereConnexion FROM personnages WHERE id = '.$info);
      $donnees = $req->fetch(PDO::FETCH_ASSOC);
      return new Personnage($donnees);
    }
   // Sinon, on veut récupérer le personnage avec son nom.
   else
   {
     $req = $this->db->prepare('SELECT id, nom, degats, experience, niveau, nbCoups, dateDernierCoup, dateDerniereConnexion FROM personnages WHERE nom = :nom');
    
     // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
     $req->execute([':nom' => $info]);
  
     return new Personnage($req->fetch(PDO::FETCH_ASSOC));
   }

 }

/* récupérer une liste de plusieurs personnages */
  public function getList($nom)
  {
    // Retourne la liste de tous les personnages dont le nom n'est pas $nom
    $persos = [];
    $req = $this->db->prepare('SELECT id, nom, degats, experience, niveau, nbCoups, dateDernierCoup, dateDerniereConnexion FROM personnages WHERE nom <> :nom ORDER BY nom');
    $req->execute([':nom' => $nom]);
    
    while ($donnees = $req->fetch(PDO::FETCH_ASSOC)){
      $persos[] = new Personnage($donnees);
    }
    return $persos;
  }

/*MODIFIER UN NOUVEAU PERSONNAGE */
  public function update(Personnage $perso)
  {
    // Prépare une requête de type UPDATE.
    $req = $this->db->prepare('UPDATE personnages SET nom = :nom, degats = :degats, experience = :experience, niveau = :niveau, nbCoups = :nbCoups, dateDernierCoup = :dateDernierCoup, dateDerniereConnexion = :dateDerniereConnexion  WHERE id = :id');
    //$req =  $this->db->prepare('UPDATE personnages SET degats = :degats, nbCoups = :nbCoups, dateDernierCoup = :dateDernierCoup, dateDerniereConnexion = :dateDerniereConnexion WHERE id = :id');
    // Assignation des valeurs à la requête.
    $req->bindValue(':nom', $perso->getNom());
    $req->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
    $req->bindValue(':experience',$perso->experience(), PDO::PARAM_INT);
    $req->bindValue(':niveau',$perso->niveau(), PDO::PARAM_INT);
    $req->bindValue(':nbCoups', $perso->nbCoups(), PDO::PARAM_INT);
    $req->bindValue(':dateDernierCoup', $perso->dateDernierCoup()->format('Y-m-d'), PDO::PARAM_STR);
    $req->bindValue(':dateDerniereConnexion',$perso->dateDerniereConnexion()->format('Y-m-d'), PDO::PARAM_STR);
    $req->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

    // Exécution de la requête.
    $req->execute();
  }

  public function setDb(PDO $db)
  {
    $this->db = $db;
  }

}