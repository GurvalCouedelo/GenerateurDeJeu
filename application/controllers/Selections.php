<?php

session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Selections extends CI_Controller {
    
	public function index()
	{
        if(isset($_SESSION['id']))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('parser');
            
            $sql = "SELECT * FROM selections";
            $listeSelections = $this->db->query($sql);
            
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
                $sql = "INSERT INTO selections (nom, utilisateurId) VALUES (?, ?)";
                $this->db->query($sql, array($_POST["nom"], $_SESSION['id']));
                
                $_SESSION["selection"] = $this->db->insert_id();
                    
                redirect('/fragment/liste/');
            }
            
            $this->load->view('courants/hautTeteClassique.php');
            $this->load->view('courants/tete.php');
            $this->load->view('selections/index.php', array("listeSelections" => $listeSelections->result()));
            $this->load->view('courants/pied.php');
        }
        
        else{
            show_404();
        }
	}
    
    public function modification()
	{
        if(isset($_SESSION['id']))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('form_validation');
            
            $erreurs = array();

            $this->form_validation->set_rules(array(
                array(
                    "field" => "nomSelection",
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
                $sql = "UPDATE selections SET nom = ? WHERE id = ? AND utilisateurId = ?";
                
                if(!$this->db->query($sql, array($_POST["nomSelection"], $_POST["numero"], $_SESSION['id']))){
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
    
    public function portail($id)
	{
        if(isset($_SESSION['id']))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('parser');
            
            $sql = "SELECT * FROM selections WHERE id = ? AND utilisateurId = ?";
            $listeSelections = $this->db->query($sql, array($id, $_SESSION['id']));
            
            if(!empty($listeSelections))
            {
                $_SESSION["selection"] = $id;
                redirect('/fragment/liste');
            }
            
            else{
                redirect('/selections');
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

            $sql = "SELECT * FROM selections WHERE utilisateurId = ?";
            $listeSelections = $this->db->query($sql, array($_SESSION['id']));
            
            header('Content-Type: application/json');
            echo json_encode($listeSelections->result());
        }
        
        else{
            show_404();
        }
	}
}