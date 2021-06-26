<?php

class PieceTempsCompteur {
    
    private $id;
    private $user;
    private $id_piece;
    private $started;
    private $stoped;
        
    public function getId(){
        return $this->id;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function getIdPiece(){
        return $this->id_piece;
    } 
    
    public function getStarted(){
        return $this->started;
    }
    
    public function getStoped(){
        return $this->stoped;
    }
    
    public function getWorkingTime(){
        if($this->stoped){            
            return $this->calcRealTime();
        }
        return "Compteur non stopé !!!";
    }
    
    private function calcRealTime(){
        $dateStarted = new DateTime($this->getStarted());
        $dateStoped = new DateTime($this->getStoped());        
        
        $dateStartedDayDate = new DateTime($dateStarted->format('d-m-Y')
                                           ,new DateTimeZone("Europe/Amsterdam")); 
        $dateStopedDayDate = new DateTime($dateStoped->format('d-m-Y')
                                          ,new DateTimeZone("Europe/Amsterdam"));            
        $dateStartedTimestamp = strtotime($dateStarted->format('d-m-Y H:i')); 
        $dateStopedTimestamp = strtotime($dateStoped->format('d-m-Y H:i'));
        $daysDiff = $dateStartedDayDate->diff($dateStopedDayDate)->days;
        
        $hoursReal = 0;
        if($daysDiff>=0){
            if($daysDiff == 0){
                $hoursWork = ($dateStopedTimestamp - $dateStartedTimestamp)/3600;
                if($this->isBeforeMidi($dateStartedDayDate,
                                $dateStartedTimestamp,
                                $dateStopedTimestamp)){
                    $hoursWork -=1;
                }
                //echo "<br>heures = ".$hoursWork."<br><br>";
                $hoursReal += $hoursWork;
            }else{                    
                for($i=0;$i<=$daysDiff;$i++){
                    $hoursWork ;
                    if($i == 0){
                        //echo "<br>premier jour";
                        $hoursWork = $this->getHoursWork($dateStartedDayDate,
                                                  $dateStartedTimestamp, $dateStopedTimestamp,"first");
                        //echo "<br>heures = ".$hoursWork."<br><br>";
                        $hoursReal += $hoursWork;
                    }elseif($i == $daysDiff){
                        //echo "dernier jour";
                        $hoursWork = $this->getHoursWork($dateStopedDayDate,
                                                  $dateStartedTimestamp,
                                                  $dateStopedTimestamp, "last");
                        //echo "<br>heures = ".$hoursWork."<br><br>";
                        $hoursReal += $hoursWork;
                    }else{
                        //echo ($i+1)."eme jour";                            
                        $hoursWork = $this->getHoursWork($dateStartedDayDate->modify('+1 day'),
                                                  null,null, "other");
                        //echo "<br>heures = ".$hoursWork."<br><br>";
                        $hoursReal += $hoursWork;
                    }
                }
            }            
        }else{
            $hoursReal = "Impossible de calculer le temps !";
        }  

        $hoursReal = number_format($hoursReal,2);
        $times = explode('.',$hoursReal);            
        if($times[1]){
            $realMin = $this->convertMinute($times[1]);                
            $hoursReal = $times[0]+$realMin;
        }
        return $hoursReal;
    }
    
    
    private function isBeforeMidi($date,$hoursBeginTimestamp,$hoursFinishTimestamp){
        $date = $date->format('d-m-Y').' 12:00';
        $midiTimestamp = strtotime($date);                
        if($hoursBeginTimestamp <= $midiTimestamp and
          $midiTimestamp < $hoursFinishTimestamp){
            //echo "<br> avant midi";
            return true;
        }
        return false;
    }

    private function getHoursWork($date, $hoursStarted, $hoursStoped, $dayOrder){
        $whatDayItIs = $this->whatDayItIs($date);                 
        if($whatDayItIs == "weekend"){                    
            return 0;                    
        }elseif($whatDayItIs == "vendredi"){
            if($dayOrder == "first"){
                $hoursSpent = ($this->dateHorairesTimestamp($date,"vendredi_soir") - $hoursStarted)/(60*60);
            }elseif($dayOrder == "last"){
                $hoursSpent = ($hoursStoped - $this->dateHorairesTimestamp($date,"vendredi_matin"))/(60*60);
                if($hoursSpent>5){
                    $hoursSpent = 5;
                }
            }elseif($dayOrder == "other"){
                $hoursSpent = 5;
            }                    
            return $hoursSpent > 0? $hoursSpent : 0;

        }elseif($whatDayItIs == "semaine"){
            if($dayOrder == "first"){
                $hoursSpent = ($this->dateHorairesTimestamp($date,"semaine_soir") - $hoursStarted)/(60*60);
                if($this->isBeforeMidi($date,$hoursStarted,$hoursStoped)){
                    $hoursSpent -=1;
                }
            }elseif($dayOrder == "last"){
                $hoursSpent = ($hoursStoped - $this->dateHorairesTimestamp($date,"semaine_matin"))/(60*60);
                if($this->isBeforeMidi($date,$hoursStarted,$hoursStoped)){
                    $hoursSpent -=1;
                }
            }elseif($dayOrder == "other"){
                $hoursSpent = 8.5;
            }
            return $hoursSpent > 0? $hoursSpent : 0;
        }
    } 

    private function dateHorairesTimestamp($date,$moment){
        $horaires = array(
            "semaine_matin"=>' 7:30',
            "semaine_soir"=>' 17:00',
            "vendredi_matin"=>' 7:00',
            "vendredi_soir"=>' 12:00',
        );                
        $date = $date->format('d-m-Y').$horaires[$moment];
        return strtotime($date);
    }

    private function convertMinute($min){                 
        if($min>=76){
            return 1;
        }elseif($min>=51){
            return 0.75; 
        }elseif($min>=26){
            return 0.50; 
        }elseif($min>=1){
            return 0.25; 
        }elseif($min = 0){
            return 0; 
        }
    }

    private function whatDayItIs($date){                
        if($date->format('N') >= 6){
            return "weekend";
        }elseif($date->format('N') == 5){
            return "vendredi";            
        }else{
            return "semaine";
        }
    }
        
}