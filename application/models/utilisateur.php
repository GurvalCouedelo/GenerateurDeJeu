<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilisateur extends CI_Model {

    protected $pseudo;
    protected $passe;
    
    public function __construct()
    {
        parent::__construct();
    }
    
//    substr(hash("sha512", $motDePasse), 0, 124)
    
}