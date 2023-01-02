<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Jeux extends CI_Controller{
	public function index()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('parser');
            
            $sql = "SELECT * FROM jeux";
            $listeJeux = $this->db->query($sql);
            
            $this->load->library('form_validation');

            $this->form_validation->set_rules(array(
                array(
                    "field" => "nom",
                    "label" => "nom", 
                    "rules" => "required|max_length[256]", 
                    "errors" => array(
                        "required" => "Vous n'avez pas rempli le champ nom!",
                        "max_length" => "Le nom de votre sélection est trop grand."
                    )
                )
            ));

            if (!$this->form_validation->run() === FALSE)
            {
                $sql = "INSERT INTO jeux (nom, creation, utilisateurId) VALUES (?, now(), ?)";
                $this->db->query($sql, array($_POST["nom"], $_SESSION['id']));
                
                redirect('/jeux/generer/' . $this->db->insert_id());
            }
            
            $this->load->view('courants/hautTeteClassique.php');
            $this->load->view('courants/tete.php');
            $this->load->view('jeux/index.php', array("listeJeux" => $listeJeux->result()));
            $this->load->view('courants/pied.php');
        }
        
        else{
            show_404();
        }
	}
    
    public function modification()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('form_validation');

            $this->form_validation->set_rules(array(
                array(
                    "field" => "nomJeu",
                    "label" => "nom de la sélection", 
                    "rules" => "required|max_length[256]", 
                    "errors" => array(
                        "required" => "Vous n'avez pas rempli le champ nom!",
                        "max_length" => "Le nom de votre sélection est trop grand."
                    )
                ), 
                array(
                    "field" => "numero",
                    "label" => "mon label", 
                    "rules" => "required|numeric", 
                    "errors" => array(
                        "required" => "Il y a eu une erreur dans le fonctionnement du formulaire.",
                        "numeric" => "Il y a eu une erreur dans le fonctionnement du formulaire. "
                    )
                )
            ));

            if (!$this->form_validation->run() === FALSE)
            {
                $sql = "UPDATE jeux SET nom = ? WHERE id = ? AND utilisateurId = ?";
                
                if(!$this->db->query($sql, array($_POST["nomJeu"], $_POST["numero"], $_SESSION['id']))){
                     $_SESSION["erreurs"]["sql"] = $this->db->errors();
                }
            }
            
            else{
                $_SESSION["erreurs"]["formulaire"] = validation_errors();
            }
        }
        
        else{
            show_404();
        }
	}
    
    public function obtenir()
	{
        if(isset($_SESSION['id']))
        {
            $this->load->database();

            $sql = "SELECT * FROM jeux WHERE utilisateurId = ?";
            $listeSelections = $this->db->query($sql, array($_SESSION['id']));
            
            header('Content-Type: application/json');
            echo json_encode($listeSelections->result());
        }
        
        else{
            show_404();
        }
	}
    
    public function generer($id)
	{
        
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('generateurcartes');
            $this->load->library('form_validation');
            
            $sql = "SELECT * FROM jeux WHERE id = ? AND utilisateurId = ?";
            $jeu = $this->db->query($sql, array($id, $_SESSION['id']))->row_array();
            
            if(!empty($jeu)){
                $sql = "SELECT count(id) FROM cartes WHERE jeuId = ? ";
                $nombreCartes = intval($this->db->query($sql, array($id))->row_array()["count(id)"]);

                if($nombreCartes > 0){
                    $_SESSION["jeuId"] = $id;
                    redirect('/jeux/generation/');
                }

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

                if (!$this->form_validation->run() === FALSE)
                {
                    $sql = "SELECT count(id) FROM fragments_selections WHERE selectionId = ? AND selectionne = TRUE";

                    $nombreFragments = $this->db->query($sql, array($_SESSION["selection"]));

                    if(in_array($nombreFragments->row_array()["count(id)"], $this->generateurcartes->getPossibilites())){
                        $sql = "SELECT * FROM fragments LEFT JOIN fragments_selections ON fragments.id = fragments_selections.fragmentId WHERE selectionId = ? AND selectionne = TRUE";
                        $fragmentsListe = $this->db->query($sql, array($_SESSION["selection"]));

                        $_SESSION["cartes"] = $this->generateurcartes->generer($this->generateurcartes->getPossibilites()[$_POST["nombreFragments"]], $fragmentsListe->result());
                        $_SESSION["nombreCartes"] = $_POST["nombreFragments"];
                        $_SESSION["jeuId"] = $id;

                        $sql = "DELETE cF FROM carte_fragment cF LEFT JOIN cartes c ON cF.carteId = c.id WHERE c.jeuId = " . $_SESSION["jeuId"] . "";
                        $this->db->query($sql);

                        $sql = "DELETE FROM cartes WHERE jeuId = '" . $_SESSION["jeuId"] . "'";
                        $this->db->query($sql);

                        $sql = "UPDATE jeux SET nbFragments = ? WHERE id = ?";
                        $this->db->query($sql, array(array_keys($this->generateurcartes->getPossibilites(), $nombreFragments->row_array()["count(id)"]), strval($_SESSION["jeuId"])));

                        redirect('/jeux/generation');
                    }

                    else{
                        $_SESSION["erreurs"]["nombreFragments"] = "Vous devez avoir sélectionné " . $this->generateurcartes->getPossibilites()[$_POST["nombreFragments"]] 
                            . " fragments pour avoir un jeu de " . $this->generateurcartes->getPossibilites()[$_POST["nombreFragments"]] . " cartes.";
                    }
                }

                else{
                    $_SESSION["erreurs"]["formulaire"] = validation_errors();
                }

                $this->load->view('courants/hautTeteClassique.php');
                $this->load->view('courants/tete.php');
                $this->load->view('jeux/generer.php', array("possibilites" => $this->generateurcartes->getPossibilites(), "idJeu" => $id));
                $this->load->view('courants/pied.php');
            }
        }
        
        else{
            show_404();
        }
	}
    
    public function generation()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]) && isset($_SESSION["jeuId"]))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('generateurcartes');
           
            $sql = "SELECT count(id) FROM cartes WHERE jeuId = ?";
            $nombreCartes = $this->db->query($sql, array($_SESSION["jeuId"]));
            
            if(intval($nombreCartes->row_array()["count(id)"]) !== 0){
                $_SESSION["cartes"] = null;
            }
            
            $this->load->view('courants/hautTeteGeneration.php');
            $this->load->view('courants/tete.php');
            $this->load->view('jeux/generation.php', array("cartes" => $_SESSION["cartes"]));
            $this->load->view('courants/pied.php');
        }
        
        else{
            show_404();
        }
	}
}
