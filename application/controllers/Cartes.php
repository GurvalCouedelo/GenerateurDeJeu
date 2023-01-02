<?php

session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Cartes extends CI_Controller {
    public function enregistrer()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]) && isset($_SESSION["cartes"]))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('form_validation');
            
//                Actualisation du nombre de fragments du jeu
            
            $sql = "SELECT * FROM jeux WHERE id = ? AND utilisateurId = ?";
            $jeu = $this->db->query($sql, array($_SESSION["jeuId"], $_SESSION['id']))->row_array();
            
            
            
            if(!empty($jeu)){
                
                $nbFragments = $jeu["nbFragments"];
                
                for($i = 0; $i < $nbFragments; $i++)
                {
                    if(!isset($_POST["fragment" . $i]) || !is_numeric($_POST["fragment" . $i])){
                        break;
                    }
                }
                
                
                if($i === intval($nbFragments)){
                    
    //                Récupération des plans

                    $sql = "SELECT * FROM plancartes WHERE nombreFragments = ?";
                    $planListe = $this->db->query($sql, array($nbFragments))->result();
                    $plan =  $planListe[mt_rand(0, count($planListe) - 1)];

    //                Insertion de la carte

                    $sql = "INSERT INTO cartes (jeuId) VALUES (" . $_SESSION["jeuId"] . ")";
                    $this->db->query($sql);
                    $idCarte = $this->db->insert_id();

    //                Récupération des descriptions de fragments

                    $sql = "SELECT * FROM fragmentdescription WHERE planId = ?";

                    $i = 0;

                    foreach($this->db->query($sql, array($plan->id))->result() as $fragmentDescriptionBoucle)
                    {  
                        $sql = "INSERT INTO erreur (message) Values (?)";
                        $this->db->query($sql, "Le fragment " . $idCarte . " a été enregistrée.");
                                     
                        
                        $sql = "INSERT INTO carte_fragment (carteId, fragmentId, positionX, positionY, rotation, taille) VALUES (?, ?, ?, ?, ?, ?)";
                        $this->db->query($sql, 
                            array(
                                $idCarte, 
                                $_POST["fragment" . $i], 
                                $fragmentDescriptionBoucle->positionX, 
                                $fragmentDescriptionBoucle->positionY, 
                                $fragmentDescriptionBoucle->rotation, 
                                $fragmentDescriptionBoucle->taille
                            ));
                        $i++;
                    }
                    
                    
                    
                }
            }
            else{
                var_dump($jeu);
            }
            
            
        }
        
        else{
            show_404();
        }
	}
    
    public function obtenir()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["jeuId"]))
        {
            $this->load->database();
            $this->load->helper('url');
            
            
            $sql = "SELECT * FROM cartes c LEFT JOIN jeux j ON c.jeuId = j.id WHERE j.id = ? AND j.utilisateurId = ? ";
            $jeu = $this->db->query($sql, array($_SESSION["jeuId"], $_SESSION['id']))->result();
            
            if(!empty($jeu)){
                $sql = "SELECT * FROM cartes WHERE jeuId = ?";
                $tableauCartes["cartes"] = $this->db->query($sql, array($_SESSION["jeuId"]))->result();
                
                

                $sql = "SELECT * FROM fragments f LEFT JOIN carte_fragment cF ON f.id = cF.fragmentId LEFT JOIN cartes c ON cF.carteId = c.id WHERE c.jeuId = ?";
                $tableauCartes["fragments"] = $this->db->query($sql, array($_SESSION["jeuId"]))->result();

                $i = 0;
                $j = 0;
                

                foreach($tableauCartes["cartes"] as $carte){
                    foreach($tableauCartes["fragments"] as $fragment){
                        if($fragment->carteId === $carte->id){
                            foreach($fragment as $nomDeColonneDansBaseDeDonnees => $proprieteDuFragment){
                                $tableauCartesFinal[$i][$j][$nomDeColonneDansBaseDeDonnees] = $proprieteDuFragment;
                            }

                            $j++;
                        }
                    }

                    $i++;
                    $j = 0;
                }
                
                echo json_encode($tableauCartesFinal);
            }
            
        }
        
        else{
            show_404();
        }
	}
    
    public function modifier()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["jeuId"]))
        {
            $this->load->database();
            
            $listeCriteres = array("fragmentId", "carteId", "positionX", "positionY", "taille", "rotation");
            $valide = true;
            
            foreach($listeCriteres as $critereBoucle){
                if(!isset($_POST[$critereBoucle]) || !is_numeric($_POST[$critereBoucle])){
                    $valide = $_POST[$critereBoucle];
                }
            }
            
            if($valide === true){
                
                $sql = "SELECT * FROM carte_fragment cF LEFT JOIN cartes c ON cf.carteId = c.id WHERE c.jeuId = ?";
                $jeu = $this->db->query($sql, array($_SESSION["jeuId"]));
                
                if(!empty($jeu)){
                    $sql = "UPDATE carte_fragment SET positionX = ?, positionY = ?, taille = ?, rotation = ? WHERE carteId = ? AND fragmentId = ?";
                    $this->db->query($sql, array($_POST["positionX"], $_POST["positionY"], $_POST["taille"], $_POST["rotation"], $_POST["carteId"], $_POST["fragmentId"]));
                }
            }
            
            else{
                echo $valide;
            }
        }
	}
}