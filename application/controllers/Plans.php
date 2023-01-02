<?php

session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Plans extends CI_Controller {
    public function selectionner()
	{
        if(isset($_SESSION['id']) && $_SESSION['permission'] === "A")
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('generateurcartes');
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules(array(
                array(
                    "field" => "nombreFragments",
                    "label" => "nombre de symboles", 
                    "rules" => "required|numeric", 
                    "errors" => array(
                        "required" => "Vous n'avez pas rempli le champ nom!",
                        "numeric" => "Il y a eu une erreur dans le fonctionnement du formulaire."
                    )
                )
            ));
            
            if($this->form_validation->run()){
                redirect('/plans/modifier/' . $_POST["nombreFragments"]);
            }
            
            $this->load->view('courants/hautTeteClassique.php');
            $this->load->view('courants/tete.php');
            $this->load->view('plans/selectionner.php', array("possibilites" => $this->generateurcartes->getPossibilites()));
            $this->load->view('courants/pied.php');
        }
        
        else{
            show_404();
        }
	}
    
    public function modifier($nombreFragments)
	{
        if(isset($_SESSION['id']) && $_SESSION['permission'] === "A")
        {
            $this->load->view('courants/hautTeteGeneration.php');
            $this->load->view('courants/tete.php');
            $this->load->view('plans/modifier.php', array("nombreFragments" => $nombreFragments));
            $this->load->view('courants/pied.php');
        }
        
        else{
            show_404();
        }
	}
    
    public function obtenir()
	{
        if(isset($_SESSION['id']) && $_SESSION['permission'] === "A")
        {
            if(isset($_POST["nombreFragments"]) && is_numeric($_POST["nombreFragments"])){
                $this->load->database();
                $this->load->helper('url');

                $sql = "SELECT * FROM plancartes WHERE plancartes.nombreFragments = ?";
                $tableauPlans["plans"] = $this->db->query($sql, array($_POST["nombreFragments"]))->result();
                
                $sql = "SELECT fragmentdescription.*, plancartes.nombreFragments FROM fragmentdescription LEFT JOIN plancartes ON plancartes.id = fragmentdescription.planId WHERE plancartes.nombreFragments = ?";
                $tableauPlans["description"] = $this->db->query($sql, array($_POST["nombreFragments"]))->result();

                $i = 0;
                $j = 0;
                
                $tableauPlansFinal = array();

                foreach($tableauPlans["plans"] as $plan){
                    foreach($tableauPlans["description"] as $fragmentDescription){
                        if($fragmentDescription->planId === $plan->id){
                            foreach($fragmentDescription as $nomDeColonneDansBaseDeDonnees => $fragmentDescriptionDeFragment){
                                $tableauPlansFinal[$i][$j][$nomDeColonneDansBaseDeDonnees] = $fragmentDescriptionDeFragment;
                            }

                            $j++;
                        }
                    }

                    $i++;
                    $j = 0;
                    
                }

                echo json_encode($tableauPlansFinal);
            }
        }
        
        else{
            show_404();
        }
	}
    
    public function creation()
	{
        if(isset($_SESSION['id']) && $_SESSION['permission'] === "A")
        {
            $this->load->database();
            $this->load->library('generateurcartes');
            
            if(isset($_POST["nombreFragments"]) || is_numeric($_POST["nombreFragments"])){
                if(array_key_exists(intval($_POST["nombreFragments"]), $this->generateurcartes->getPossibilites())){
                    $sql = "INSERT plancartes (nombreFragments) VALUES (?)";
                    $this->db->query($sql, array($_POST["nombreFragments"]));
                    
                    $numero = $this->db->insert_id();
                    
                    for($i = 0; $i < $_POST["nombreFragments"]; $i++){
                        $sql = "INSERT fragmentdescription (planId, positionX, positionY, rotation, taille) VALUES (?, ?, ?, ?, ?)";
                        $this->db->query($sql, array($numero, 0, 0, 0, 0.1));
                    }
                }
            }
        }
	}
    
    public function modification()
	{
        if(isset($_SESSION['id']) && $_SESSION['permission'] === "A")
        {
            $this->load->database();
            
            $listeCriteres = array("id", "positionX", "positionY", "taille", "rotation");
            $valide = true;
            
            foreach($listeCriteres as $critereBoucle){
                if(!isset($_POST[$critereBoucle]) || !is_numeric($_POST[$critereBoucle])){
                    $valide = $critereBoucle;
                }
            }
            
            if($valide === true){
                $sql = "UPDATE fragmentdescription SET positionX = ?, positionY = ?, taille = ?, rotation = ? WHERE id = ?";
                $this->db->query($sql, array($_POST["positionX"], $_POST["positionY"], $_POST["taille"], $_POST["rotation"], $_POST["id"]));
            }
            
            else{
                echo $valide;
            }
        }
	}
}