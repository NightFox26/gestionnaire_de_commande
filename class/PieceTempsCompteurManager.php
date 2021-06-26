<?php

class PieceTempsCompteurManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getCompteur($idC){             
        $pieceTempsC = $this->db->prepare('SELECT * FROM pieces_temps_compteur
                                            WHERE id = :id'); 
        $pieceTempsC->execute(array(            
            'id'       =>$idC 
        )); 
        $pieceTempsC = $pieceTempsC->fetchObject('PieceTempsCompteur');
        return $pieceTempsC;        
    }
    
    public function getAllCompteursForPiece($idPiece){             
        $piecesTempsC = $this->db->prepare('SELECT * FROM pieces_temps_compteur
                                           WHERE id_piece = :idPiece'); 
        $piecesTempsC->execute(array(            
            'idPiece'       =>$idPiece
        )); 
        $piecesTempsC = $piecesTempsC->fetchAll(PDO::FETCH_CLASS, 'PieceTempsCompteur');
        return $piecesTempsC;        
    }
    
    public function isCompteursForOrder($idOrder){             
        $piecesTempsC = $this->db->prepare('SELECT pct.id, pct.id_piece 
                                            FROM pieces_temps_compteur AS pct
                                            INNER JOIN commandes_pieces as cp 
                                            ON cp.id = pct.id_piece
                                            WHERE cp.id_commande = :idOrder 
                                            AND  pct.stoped IS NULL
                                            LIMIT 1'); 
        $piecesTempsC->execute(array(            
            'idOrder'       =>$idOrder
        )); 
        $piecesTempsC = $piecesTempsC->fetchAll(PDO::FETCH_CLASS, 'PieceTempsCompteur');
        return $piecesTempsC;        
    }
    
    public function getCompteurForPieceAndUser($idPiece,$user){             
        $pieceTempsC = $this->db->prepare('SELECT * FROM pieces_temps_compteur
                                            WHERE id_piece = :idPiece
                                            AND user = :user
                                            AND stoped IS NULL'); 
        $pieceTempsC->execute(array(            
            'idPiece'       =>$idPiece,
            'user'          =>$user    
        )); 
        $pieceTempsC = $pieceTempsC->fetchObject('PieceTempsCompteur');
        return $pieceTempsC;        
    }
    
    public function insertPieceTempsCompteur($user,$idPiece){         
        $piecesTempsCInsert = $this->db->prepare('INSERT INTO pieces_temps_compteur(
                                                user,    
                                                id_piece,
                                                started
                                                )VALUES(
                                                :user,     
                                                :id_piece,
                                                NOW()
                                            )');
        
        
        $piecesTempsCInsert->execute(array(            
            'user'        =>$user,
            'id_piece'    =>$idPiece
        )); 
        return $this->db->lastInsertId();                 
    }
    
    public function stopPieceTempsCompteur($id){         
        $piecesTempsCStop = $this->db->prepare('UPDATE pieces_temps_compteur SET 
                                            stoped = NOW()
                                            WHERE id=:id');
        $piecesTempsCStop->execute(array(
            'id'        =>$id        
        ));        
    }
    
    
}