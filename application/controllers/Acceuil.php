<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Acceuil extends CI_Controller {
    
	public function index()
	{
        $this->load->helper('url');
//        $this->session->set_userdata("url", "http://127.0.0.1");
//        $this->session->set_userdata("url", "https://generateur.titann.fr/");
        
        if(!isset($_SESSION['id']))
        {
            $this->load->database();
            $this->load->helper('form');
            $this->load->library('form_validation');
            

            $this->form_validation->set_rules(array(
                array(
                    "field" => "pseudo",
                    "label" => "pseudo", 
                    "rules" => "required|max_length[255]", 
                    "errors" => array(
                        "required" => "Vous n'avez pas rempli le champ pseudo!",
                        "alpha_numeric" => "Vous devez envoyer un texte!",
                        "max_length" => "Votre pseudo est trop grand."
                    )
                ),
                array(
                    "field" => "passe",
                    "label" => "mot de passe", 
                    "rules" => "required|max_length[50]", 
                    "errors" => array(
                        "required" => "Vous n'avez pas rempli le champ mot de passe!",
                        "alpha_numeric" => "Vous devez envoyer un texte!",
                        "max_length" => "Votre mot de passe est trop grand."
                    )
                )
            ));
            
            $_SESSION["erreurs"] = array();

            if (!$this->form_validation->run() === FALSE)
            {
                $sql = "SELECT * FROM utilisateurs WHERE pseudo = ? AND passe = ?";
                $utilisateur = $this->db->query($sql, array($this->input->get_post('pseudo'), substr(hash("sha512", $this->input->get_post('passe')), 0, 124)));

                if(!empty($utilisateur->result()))
                {
                    foreach($utilisateur->result() as $ligne)
                    {
                        $_SESSION['id'] = $ligne->id;
                        $_SESSION['permission'] = $ligne->permission;
                    }
                    
                    redirect('/selections');
                }
                
                else{
                    array_push($_SESSION["erreurs"], "Vous n'avez pas du entrer le bon identifiant ou le bon mot de passe!");
                }
            }
            
            else{
                array_push($_SESSION["erreurs"], validation_errors());
            }

            $this->load->view('courants/hautTeteClassique.php');
            $this->load->view('courants/tete.php');
            $this->load->view('acceuil/acceuil');
            $this->load->view('courants/pied.php');
        }
        
        else{
            session_unset();
            redirect('/acceuil');
        }
	}
}
