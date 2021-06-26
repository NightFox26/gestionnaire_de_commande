<?php

class PieceTempsManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getTemps($idPiece){             
        $piecesTemps = $this->db->prepare('SELECT * FROM pieces_temps
                                           WHERE id_piece = :idPiece'); 
        $piecesTemps->execute(array(            
            'idPiece'       =>$idPiece
        )); 
        $piecesTemps = $piecesTemps->fetchAll(PDO::FETCH_CLASS, 'PieceTemps');        
        return $piecesTemps;        
    }
    
    public function insertPieceTemps($user,$temps,$heuresSup,$idPiece){         
        $piecesTempsInsert = $this->db->prepare('INSERT INTO pieces_temps(
                                                user,
                                                temps,
                                                heures_sup,
                                                id_piece
                                                )VALUES(
                                                :user,
                                                :temps,
                                                :heures_sup,
                                                :id_piece
                                            )');
        
        
        $piecesTempsInsert->execute(array(            
            'user'        =>$user,
            'temps'       =>$temps,
            'heures_sup'  =>$heuresSup,
            'id_piece'    =>$idPiece
        )); 
        return $this->db->lastInsertId();                 
    }
    
    
}