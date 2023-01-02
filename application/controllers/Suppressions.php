<?php

session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppressions extends CI_Controller {
	public function index()
	{
        if(isset($_SESSION['id']))
        {
            if(isset($_POST["table"]) && isset($_POST["id"])){
                if(is_string($_POST["table"]) && is_numeric($_POST["id"])){
                    $this->load->database();
                    
                    if($_POST["table"] === "selections"){
                        $sql = "SELECT * FROM selections WHERE id = ? AND utilisateurId = ?";
                        $selection = $this->db->query($sql, array($_POST["id"], $_SESSION['id']))->row_array();
                        
                        if(!empty($selection)){
                            $sql = "DELETE j FROM jeux j LEFT JOIN cartes c ON j.id = c.jeuId LEFT JOIN carte_fragment cF ON c.id = cF.carteId LEFT JOIN fragments f ON cF.fragmentId = f.id LEFT JOIN fragments_selections fS ON f.id = fS.fragmentId LEFT JOIN selections s ON fS.selectionId = s.id WHERE s.id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "DELETE c FROM cartes c LEFT JOIN carte_fragment cF ON c.id = cF.carteId LEFT JOIN fragments f ON cF.fragmentId = f.id LEFT JOIN fragments_selections fS ON f.id = fS.fragmentId LEFT JOIN selections s ON fS.selectionId = s.id WHERE s.id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "DELETE cF FROM carte_fragment cF LEFT JOIN fragments f ON cF.fragmentId = f.id LEFT JOIN fragments_selections fS ON f.id = fS.fragmentId LEFT JOIN selections s ON fS.selectionId = s.id WHERE s.id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "DELETE fS FROM fragments_selections fS LEFT JOIN selections s ON fS.selectionId = s.id WHERE s.id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "DELETE FROM selections WHERE id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "SELECT * FROM fragments f LEFT JOIN fragments_selections fS ON f.id = fS.fragmentId WHERE fS.fragmentId IS NULL";
                            $fragmentsSupprimes = $this->db->query($sql)->result();

                            $sql = "DELETE f FROM fragments f LEFT JOIN fragments_selections fS ON f.id = fS.fragmentId WHERE fS.fragmentId IS NULL";
                            $this->db->query($sql);

                            foreach($fragmentsSupprimes as $fragmentBoucle){
                                unlink("./uploads/" . $_SESSION['id'] . "/" . $fragmentBoucle->nomFichier);
                            }

                            $_SESSION["selection"] = null;
                            $_SESSION["jeuId"] = null;
                        }
                    }
                    
                    if($_POST["table"] === "fragments"){
                        $sql = "SELECT * FROM fragments_selections fs LEFT JOIN selections s ON fs.selectionId = s.id WHERE fs.id = ? AND s.utilisateurId = ?";
                        $jeu = $this->db->query($sql, array($_POST["id"], $_SESSION['id']))->row_array();
                        
                        if(!empty($jeu)){
                            $sql = "DELETE FROM fragments_selections WHERE id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "SELECT * FROM fragments f LEFT JOIN fragments_selections fS ON f.id = fS.fragmentId WHERE fS.fragmentId IS NULL";
                            $fragmentsSupprimes = $this->db->query($sql)->result();

                            $sql = "DELETE f FROM fragments f LEFT JOIN fragments_selections fS ON f.id = fS.fragmentId WHERE fS.fragmentId IS NULL";
                            $this->db->query($sql);

                            foreach($fragmentsSupprimes as $fragmentBoucle){
                                unlink("./uploads/" . $_SESSION['id'] . "/" . $fragmentBoucle->nomFichier);
                            }
                        }
                    }
                    
                    if($_POST["table"] === "jeux"){
                        $sql = "SELECT * FROM jeux WHERE id = ? AND utilisateurId = ?";
                        $jeu = $this->db->query($sql, array($_POST["id"], $_SESSION['id']))->row_array();
                        
                        if(!empty($jeu)){
                            $sql = "DELETE cF FROM carte_fragment cF LEFT JOIN cartes c ON cF.carteId = c.id LEFT JOIN jeux j ON c.jeuId = j.id WHERE j.id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "DELETE c FROM cartes c LEFT JOIN jeux j ON c.jeuId = j.id WHERE j.id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "DELETE FROM jeux WHERE id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $_SESSION["jeuId"] = null;
                        }
                    }
                    
                    if($_POST["table"] === "plancartes"){
                        if($_SESSION['permission'] === "A"){
                            $sql = "DELETE FROM plancartes WHERE id = ?";
                            $this->db->query($sql, array($_POST["id"]));

                            $sql = "DELETE FROM fragmentdescription WHERE planId = ?";
                            $this->db->query($sql, array($_POST["id"]));
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