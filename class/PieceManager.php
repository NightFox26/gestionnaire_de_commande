<?php

class PieceManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getPieces(){             
        $pieces = $this->db->query('SELECT * FROM pieces');        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'Piece');        
        return $pieces;        
    }
    
    public function getPiece($id){         
        $piece = $this->db->query('SELECT * FROM pieces WHERE id ="'.$id.'" ');        
        return $piece->fetchObject('Piece');
    }
    
    public function getPieceByRef($ref){         
        $piece = $this->db->query('SELECT * FROM pieces WHERE ref ="'.$ref.'" ');        
        return $piece->fetchObject('Piece');
    }
    
    public function getPiecesByClient($id_client){         
        $piece = $this->db->prepare('SELECT * FROM pieces
                                     WHERE id_client = :id_client
                                     ORDER BY ref ASC');
        $piece->execute(array(
            'id_client'=>$id_client,
        ));
        return $piece->fetchAll(PDO::FETCH_CLASS, 'Piece');
    }
    
    public function getPieceByRefAndClient($ref, $idClient){             
        $pieces = $this->db->query('SELECT * FROM pieces 
                                    WHERE ref ="'.$ref.'" 
                                    AND id_client ="'.$idClient.'"');        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'Piece');        
        return $pieces;        
    }
    
    public function getPiecesSearch($word){             
        $pieces = $this->db->prepare("SELECT * FROM pieces 
                                       WHERE ref LIKE :word
                                       ORDER BY ref ASC");    
        $pieces->execute(array(
                   'word'    =>'%'.$word.'%'                  
        ));
        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'Piece');
        
        return $pieces;        
    }
    
    
    public function insertPiece($ref,$ral,$plan,$infos,$clientId){         
        $pieceInsert = $this->db->prepare('INSERT INTO pieces(
                                                ref,
                                                ral,
                                                plan,
                                                infos,
                                                id_client
                                                )VALUES(
                                                :ref,
                                                :ral,
                                                :plan,
                                                :infos,
                                                :id_client 
                                            )');
        
        
        $pieceInsert->execute(array(            
            'ref'       =>$ref,
            'ral'       =>$ral,
            'plan'      =>$plan,
            'infos'     =>$infos,
            'id_client' =>$clientId
        )); 
        return $this->db->lastInsertId();                 
    }
    
    public function updatePiece($id,$ref,$ral,$plan,$infos,$clientId){         
        $pieceUpdate = $this->db->prepare('UPDATE pieces SET 
                                             ref        = :ref_tcs,
                                             ral        = :ral,
                                             plan       = :plan,
                                             infos      = :infos,
                                             id_client  = :id_client 
                                             WHERE id = :id');
        
        $pieceUpdate->execute(array(
            'id'        =>$id,
            'ref_tcs'   =>$ref,
            'ral'       =>$ral,
            'plan'      =>$plan,
            'infos'     =>$infos,
            'id_client' =>$clientId
        ));        
    }
    
    public function deletePiece($id){
        $pieceDel = $this->db->prepare('DELETE FROM pieces WHERE id=:id');
        
        $pieceDel->execute(array(
            'id'=>$id
        ));
    }
    
}