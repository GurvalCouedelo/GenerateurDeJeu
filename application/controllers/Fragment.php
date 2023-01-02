<?php

session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Fragment extends CI_Controller {
    
	public function liste()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            $this->load->database();
            $this->load->helper('url');
            
            $this->load->view('courants/hautTeteClassique.php');
            $this->load->view('courants/tete.php');
            $this->load->view('fragment/liste.php');
            $this->load->view('courants/pied.php');
        }
        
        else{
            show_404();
        }
	}
    
    public function obtenir()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            $this->load->database();

            $sql = "SELECT * FROM fragments LEFT JOIN fragments_selections ON fragments_selections.fragmentId = fragments.id WHERE selectionId = '" . $_SESSION["selection"] . "'";
            $listeFragments = $this->db->query($sql);
            
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "listeFragments" => $listeFragments->result(),
            ));
        }
        
        else{
            show_404();
        }
	}
    
    public function creation()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            header('Content-Type: application/json');
            
            $this->load->database();
            $this->load->helper('form');
            $this->load->helper('url');
            $this->load->library('form_validation');
            
            echo $_SESSION["selection"] . "eeee";
            
            if(!empty($_POST)){
                $config['upload_path'] = './uploads/' . $_SESSION['id'] . "/";
                $config['allowed_types'] = 'png|jpg|gif';
                $config['max_size'] = 450;
                $config['max_width'] = 1024;
                $config['max_height'] = 1024;
                $config['file_name'] = substr(md5(uniqid()), 0, 25);
                    
                $this->load->library('upload', $config);

                if($this->upload->do_upload("image"))
                {
                    $data = $this->upload->data();
                    $requete = "INSERT INTO fragments (nomFichier, largeurDeBase, hauteurDeBase) VALUES (?, ?, ?);";

                    $this->db->query($requete, array($data['file_name'], $data['image_width'], $data['image_height']));
                    
                    $idFragment = $this->db->insert_id();
                    $requete = "INSERT INTO fragments_selections (fragmentId, selectionId) VALUES ('" . $idFragment . "', '" . $_SESSION["selection"] . "');";
                    
                    $this->db->simple_query($requete);
                }
            }
            
           
        }
        
        else{
            show_404();
        }
	}
    
    public function selectionner()
	{
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            $this->load->database();
            $this->load->helper('url');
            $this->load->library('form_validation');
            
            $erreurs = array();

            $this->form_validation->set_rules(array(
                array(
                    "field" => "numero",
                    "label" => "mon label", 
                    "rules" => "required|numeric", 
                    "errors" => array(
                        "required" => "Il y a eu une erreur dans le fonctionnement du formulaire.",
                        "numeric" => "Il y a eu une erreur dans le fonctionnement du formulaire."
                    )
                ),
                array(
                    "field" => "selectionne",
                    "label" => "mon label", 
                    "rules" => "required|max_length[5]", 
                    "errors" => array(
                        "required" => "Il y a eu une erreur dans le fonctionnement du formulaire.",
                        "max_length" => "Il y a eu une erreur dans le fonctionnement du formulaire."
                    )
                )
                
            ));

            if (!$this->form_validation->run() === FALSE)
            {
                $sql = "UPDATE fragments_selections SET selectionne = ? WHERE id = ?";
                
                $caseACocher = $_POST["selectionne"] === "true" ? 1 : 0;
                
                $this->db->query($sql, array($caseACocher, $_POST["numero"]));
            }
        }
        
        else{
            show_404();
        }
	}
    
    public function uploadZip(){
        if(isset($_SESSION['id']) && isset($_SESSION["selection"]))
        {
            header('Content-Type: application/json');
            
            $this->load->database();
            $this->load->helper('form');
            $this->load->library('form_validation');
            
            if(!empty($_POST)){
                $config['upload_path'] = './uploads/' . $_SESSION['id'] . "/temp/";
                $config['allowed_types'] = 'zip';
                $config['max_size'] = 500 * 100;
                    
                $this->load->library('upload', $config);

                if($this->upload->do_upload("image")){
                    $data = $this->upload->data();
                    
                    $zip = new ZipArchive;
                    
                    if ($zip->open("./uploads/" . $_SESSION['id'] . "/temp/" . $data["file_name"]) === TRUE) {
                        $zip->extractTo("./uploads/" . $_SESSION['id'] . "/temp/");
                        $zip->close();
                        unlink("./uploads/" . $_SESSION['id'] . "/temp/" . $data["file_name"]);
                        
                        $cheminDuDossier = "./uploads/" . $_SESSION['id'] . "/temp/" . $data["raw_name"] . "/";
                        $dossier = scandir($cheminDuDossier);
                        
                        if(count($dossier) <= 102){
                            foreach($dossier as $nomFichier){
                                if(preg_match("#\.png|\.jpg|\.gif#", $nomFichier)){
                                    $dimensions = getimagesize($cheminDuDossier . $nomFichier);

                                    if($dimensions[0] < 1024 && $dimensions[1] < 1024 && filesize($cheminDuDossier . $nomFichier)){
                                        $nouveauNom = substr(md5(uniqid("", true)), 0, 25) . "." . pathinfo($cheminDuDossier . $nomFichier)["extension"];
                                        copy($cheminDuDossier . $nomFichier, "./uploads/" . $_SESSION['id'] . "/" . $nouveauNom);
                                        
                                        $requete = "INSERT INTO fragments (nomFichier, largeurDeBase, hauteurDeBase) VALUES (?, ?, ?);";

                                        $this->db->query($requete, array($nouveauNom, $dimensions[0], $dimensions[1]));
                                        echo uniqid() . " ";
                                        sleep(0.1);

                                        $idFragment = $this->db->insert_id();
                                        $requete = "INSERT INTO fragments_selections (fragmentId, selectionId) VALUES ('" . $idFragment . "', '" . $_SESSION["selection"] . "');";

                                        $this->db->simple_query($requete);
                                    }
                                }
                                
                                if($nomFichier !== "." && $nomFichier !== ".."){
                                    unlink($cheminDuDossier . $nomFichier);
                                }
                            }
                            
                            rmdir($cheminDuDossier);
                        }
                    }
                }
            }
        }
        
        else{
            show_404();
        }
    }
}