<?php

class Personnage 
{
  // attributs 
  private $id,
          $nom,
          $degats;

  private $nbCoups,
          $dateDernierCoup,
          $dateDerniereConnexion;

  private $experience,
          $niveau;

  /*les constanteson on met self pour la constante comme on this pour l'objet. 
  Par contre lorsqu'on est dans l'objet on utilise object::CONSTANTE */
  const CEST_MOI = 1;
  const PERSONNAGE_TUER = 2;
  const PERSONNAGE_FRAPPE = 3;
  const PAS_AUJOURDHUI = 4;
  
  //les méthodes

  //le constructeur
  public function __construct(array $donnees)
  {
    $this->hydrate($donnees);
  }

  public function hydrate(array $donnees)
  {
    foreach ($donnees as $key => $value) {

      // On récupère le nom du setter correspondant à l'attribut. ucfirst permet de mettre la premier lettre en Maj
      $method = 'set' . ucfirst($key);

      // Si le setter correspondant existe.
      if (method_exists($this, $method)) {
        // On appelle le setter.
        $this->$method($value);
      }
    }
  }

  public function frapper( Personnage $perso)
  {
    if ($perso->getid() == $this->getid()) {
      return self::CEST_MOI;
    }

    $now = new DateTime('NOW');
    $diff = $this->dateDernierCoup()->diff($now);

    if ($this->nbCoups() >= 5 && $diff->h + 24 * $diff->d < 24) {
            return self::PAS_AUJOURDHUI;
        }
    
    if ($diff->h + 24 * $diff->d < 24){
       $this->setNbCoups($this->nbCoups() + 1);
    } else {
        $this->setNbCoups(1);
     }
       
    $this->setDateDernierCoup($now->format('Y-m-d'));

     return $perso->recevoirDegats($this->niveau() * 5);
  }
  
  public function recevoirDegats($force)
  {
    $this->setDegats($this->getDegats() + $force);

      if ($this->degats >= 100) {
          return self::PERSONNAGE_TUER;
      }
      else {
          return self::PERSONNAGE_FRAPPE;
      }
  }

  public function gagnerExperience()
  {
    $this->setExperience($this->experience() + $this->niveau() * 5);
     
    if ($this->experience() >= 100){
        $this->setNiveau($this->niveau() + 1);
        $this->setExperience(0);
    }
  }

  //Getters 
  public function getId()
  {
    return $this->id;
  }
  public function getNom()
  {
    return $this->nom;
  }
  public function getDegats()
  {
    return $this->degats;
  }
  public function nbCoups()
    {
        return $this->nbCoups;
    }
  
  public function dateDernierCoup()
  {
    return $this->dateDernierCoup;
  }

  public function experience()
  {
    return $this->experience;
  }
   
  public function niveau()
  {
    return $this->niveau;
  }

    public function dateDerniereConnexion()
    {
        return $this->dateDerniereConnexion;
    }
  
  //setter
  public function setDegats(int $degats)
  {
    
    if ($degats >=0 && $degats<=100){
      $this->degats=$degats;
    }
  }

  public function setExperience(int $experience)
  {
    $this->experience = $experience;
  }
   
  public function setNiveau(int $niveau)
  {
    if ($niveau >= 0 && $niveau <= 100) {
      $this->niveau = $niveau;
    }
  }

  public function setId(int $id)
  {
    //$id = (int) $id;
    if ($id>0){
      $this->id = $id;
    }
  }

  public function nomValide()
  {
    return !(empty($this->nom));
  }

  public function setNom($nom)
  {
    if (is_String($nom)){
      $this->nom = $nom;  
    }
  }

  public function setNbCoups($nbCoups)
  {
      $nbCoups = (int) $nbCoups;
      if ($nbCoups >= 0 && $nbCoups <= 100) {
          $this->nbCoups = $nbCoups;
      }
  }
   
  public function setDateDernierCoup($dateDernierCoup)
  {
      $dateDernierCoup = DateTime::createFromFormat("Y-m-d", $dateDernierCoup);
      $this->dateDernierCoup = $dateDernierCoup;
  }

  public function setDateDerniereConnexion($dateDerniereConnexion)
    {
        $dateDerniereConnexion = DateTime::createFromFormat("Y-m-d", $dateDerniereConnexion);
        $this->dateDerniereConnexion = $dateDerniereConnexion;
    }

}