<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateurCartes{
    protected $possibilites = array(
        3 => 7,
        6 => 31
    );
    
    protected $plans = 
    array(
        7 => array(
            array(0, 3, 5),
            array(0, 4, 6),
            array(1, 3, 6),
            array(1, 4, 5),
            array(2, 5, 6),
            array(2, 3, 4),
            array(0, 1, 2),
        ),
        31 => array(
            array(0, 1, 2, 3, 4, 30),
            array(5, 6, 7, 8, 9, 30),
            array(10, 11, 12, 13, 14, 30),
            array(15, 16, 17, 18, 19, 30),
            array(20, 21, 22, 23, 24, 30),
            array(25, 26, 27, 28, 29, 30),
            array(0, 5, 10, 15, 20, 29),
            array(1, 6, 11, 16, 21, 29),
            array(2, 7, 12, 17, 22, 29),
            array(3, 8, 13, 18, 23, 29),
            array(4, 9, 14, 19, 24, 29),
            array(0, 5, 10, 15, 20, 28),
            array(1, 7, 13, 19, 20, 28),
            array(2, 8, 14, 15, 21, 28),
            array(3, 9, 10, 16, 22, 28),
            array(0, 5, 10, 15, 20, 28),
            array(0, 7, 14, 16, 23, 27),
            array(1, 8, 10, 17, 24, 27),
            array(2, 9, 11, 18, 20, 27),
            array(3, 5, 12, 19, 21, 27),
            array(4, 6, 13, 15, 22, 27),
            array(0, 8, 11, 19, 22, 26),
            array(1, 9, 12, 15, 23, 26),
            array(2, 5, 13, 16, 24, 26),
            array(3, 6, 14, 17, 20, 26),
            array(4, 7, 10, 18, 21, 26),
            array(0, 9, 13, 17, 21, 25),
            array(1, 5, 14, 18, 22, 25),
            array(2, 6, 10, 19, 23, 25),
            array(3, 7, 11, 15, 24, 25),
            array(4, 8, 12, 16, 20, 25),
        )
    );
    
    public function generer($plan, $fragmentsListe)
    {
        $listeCartes = array();
        
        foreach($this->plans[$plan] as $planBoucle){
            $carteTemp = array();
            
            foreach($planBoucle as $fragmentCarteBoucle){
                $i = 0;
                
                foreach($fragmentsListe as $fragmentUtilisateurBoucle){
                    if($i === $fragmentCarteBoucle){
                        array_push($carteTemp, $fragmentUtilisateurBoucle->fragmentId);
                    }
                    
                    $i++;
                }
            }
            
            array_push($listeCartes, $carteTemp);
        }
        
        return $listeCartes;
    }
    
    public function getPlans(){
        return $this->plans;
    }
    
    public function getPossibilites(){
        return $this->possibilites;
    }
}