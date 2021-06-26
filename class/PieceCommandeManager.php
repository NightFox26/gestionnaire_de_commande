<?php

class PieceCommandeManager{
    
    private $db;
    
    public function __construct($bdd){
        $this->db = $bdd;
    }
    
    public function getPiecesCommandes($idOrder){             
        $pieces = $this->db->query('SELECT * FROM commandes_pieces 
                                    WHERE id_commande = "'.$idOrder.'" 
                                    ORDER BY statut');        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'PieceCommande'); 
        return $pieces;        
    }
    
    public function getPiecesCommandesByIdGloabalPiece($idPiece){             
        $pieces = $this->db->query('SELECT * FROM commandes_pieces 
                                    WHERE id_piece = "'.$idPiece.'" ');        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'PieceCommande'); 
        return $pieces;        
    }
    
    public function getCommandesPiecesSearch($word){             
        $pieces = $this->db->prepare("SELECT * FROM commandes_pieces 
                                       WHERE ref LIKE :word
                                       ORDER BY ref ASC");    
        $pieces->execute(array(
                   'word'    =>'%'.$word.'%'                  
        ));
        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'PieceCommande');
        
        return $pieces;        
    }
        
    public function getPiece($idPiece){             
        $piece = $this->db->query('SELECT * FROM commandes_pieces 
                                    WHERE id = "'.$idPiece.'" ');        
        return $piece->fetchObject('PieceCommande');              
    }
    
    public function getPieceByCommande($idPiece,$idOrder){
        $piece = $this->db->prepare('SELECT * FROM commandes_pieces 
                                    WHERE id_piece = :id_piece
                                    AND id_commande = :id_commande '); 
        $piece->execute(array(
            "id_piece"      => $idPiece,
            "id_commande"   => $idOrder
        ));
        return $piece->fetchObject('PieceCommande');   
    }
    
    public function getPiecesByCommandeWithUrgencePlus($idOrder){
        $piece = $this->db->prepare('SELECT * FROM commandes_pieces 
                                    WHERE urgencePlus = 1
                                    AND id_commande = :id_commande '); 
        $piece->execute(array(            
            "id_commande"   => $idOrder
        ));
        return $piece->fetchObject('PieceCommande');   
    }
    
    public function getPiecesNotClotured(){
        $pieces = $this->db->query('SELECT * FROM commandes_pieces 
                                    WHERE cloture = 0'); 
        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'PieceCommande'); 
        return $pieces;
    }
    
    public function getPiecesToPaint(){
        $pieces = $this->db->query('SELECT * FROM commandes_pieces 
                                    WHERE statut = "Pour peinture" '); 
        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'PieceCommande'); 
        return $pieces;
    }
    
    public function getPiecesInAcid(){
        $pieces = $this->db->query('SELECT * FROM commandes_pieces 
                                    WHERE statut = "a l\'acide" '); 
        
        $pieces = $pieces->fetchAll(PDO::FETCH_CLASS, 'PieceCommande'); 
        return $pieces;
    }
    
    public function insertPieceCommande($idOrder,$idPiece,$refPiece,$qtPiece,$ral,$infos,$plan,$urgente){         
        $pieceCommandeInsert = $this->db->prepare('INSERT INTO commandes_pieces(
                                            id_commande,
                                            id_piece,
                                            ref,
                                            qt,
                                            infos,
                                            infos_fab,
                                            ral,
                                            plan,
                                            urgente
                                            ) VALUES (
                                            :id_commande,
                                            :id_piece,
                                            :ref,
                                            :qt,
                                            :infos,
                                            :infos_fab,
                                            :ral,
                                            :plan,
                                            :urgente
                                            )');
        
        
        $pieceCommandeInsert->execute(array(            
            'id_commande'       =>$idOrder,
            'id_piece'          =>$idPiece,
            'ref'               =>$refPiece,
            'qt'                =>$qtPiece,
            'infos'             =>$infos,
            'infos_fab'         =>"",
            'ral'               =>$ral,
            'plan'              =>$plan,
            'urgente'           =>$urgente
        )); 
        
        return $this->db->lastInsertId();
    }
    
    public function updatePieceCommande($idPiece,$infos,$infosUnique,$plan,$ral){         
        $pieceCommandeStatUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                            ral         = :ral,
                                                            infos       = :infos, 
                                                            infos_unique= :infos_unique,
                                                            plan        = :plan
                                                        WHERE id_piece = :idpiece');
        
        
        $pieceCommandeStatUpdtate->execute(array(            
                    'idpiece'       =>$idPiece,
                    'ral'           =>$ral,
                    'infos'         =>$infos, 
                    'infos_unique'  =>$infosUnique,
                    'plan'          =>$plan,
        ));        
    }
    
    public function updatePieceCommandeById($id,$infos,$infosUnique,$plan,$infosFab,$ral){         
        $pieceCommandeStatUpdtate = $this->db->prepare('UPDATE commandes_pieces SET
                                                            ral         = :ral,
                                                            infos       = :infos,
                                                            infos_unique= :infos_unique,
                                                            infos_fab   = :infos_fab,
                                                            plan        = :plan
                                                        WHERE id = :idpiece');
        
        
        $pieceCommandeStatUpdtate->execute(array(            
                    'idpiece'       =>$id,
                    'ral'           =>$ral,
                    'infos'         =>$infos,
                    'infos_unique'  =>$infosUnique,
                    'infos_fab'     =>$infosFab,
                    'plan'          =>$plan,
        ));        
    }
    
    public function updateStatutPieceCommande($idPiece,$statut){ 
        if($statut == "Livre"){
            $pieceCommandeStatUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                        statut = :statut,
                                                        urgencePlus = 0,
                                                        date_livr = NOW()
                                                        WHERE id = :idpiece');
        
        
            $pieceCommandeStatUpdtate->execute(array(            
                        'idpiece'       =>$idPiece,
                        'statut'        =>$statut
            ));
        }else if($statut == "Termine"){
            $pieceCommandeStatUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                        statut = :statut,
                                                        urgencePlus = 0
                                                        WHERE id = :idpiece');
        
        
            $pieceCommandeStatUpdtate->execute(array(            
                        'idpiece'       =>$idPiece,
                        'statut'        =>$statut
            ));
        }else{
            $pieceCommandeStatUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                        statut = :statut
                                                        WHERE id = :idpiece');
        
        
            $pieceCommandeStatUpdtate->execute(array(            
                        'idpiece'       =>$idPiece,
                        'statut'        =>$statut
            ));      
        }
          
    }
    
    public function updateUrgencePlusPieceCommande($idPiece,$urgencePlus){         
        $pieceCommandeStatUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                        urgencePlus = :urgencePlus
                                                        WHERE id = :idpiece');
        
        
        $pieceCommandeStatUpdtate->execute(array(            
                    'idpiece'       =>$idPiece,
                    'urgencePlus'   =>$urgencePlus
        ));        
    }
    
    public function updateQantityPieceCommande($idPiece,$qtPiece){
        $pieceCommandeQtUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                        qt = :qt
                                                        WHERE id = :idpiece');
        
        
        $pieceCommandeQtUpdtate->execute(array(            
                    'idpiece'   =>$idPiece,
                    'qt'        =>$qtPiece
        ));
    }
    
    public function updateCloturedPieceCommande($idPiece,$cloture){
        $pieceClotureUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                        cloture = :cloture,
                                                        urgencePlus = 0
                                                        WHERE id = :idpiece');
        
        
        $pieceClotureUpdtate->execute(array(            
                    'idpiece'   =>$idPiece,
                    'cloture'   =>$cloture
        ));
    }
    
    public function updateClotureAllPiecesCommande($idOrder,$cloture){
        $piecesClotureUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                    cloture = :cloture,
                                                    urgencePlus = 0
                                                    WHERE id_commande = :idOrder');
        
        
        $piecesClotureUpdtate->execute(array(            
                    'idOrder'   =>$idOrder,
                    'cloture'   =>$cloture
        ));
    }
    
    public function updatePlanPieceCommande($idPiece,$plan){         
        $pieceCommandePlanUpdtate = $this->db->prepare('UPDATE commandes_pieces SET 
                                                        plan = :plan
                                                        WHERE id = :idpiece');
        
        
        $pieceCommandePlanUpdtate->execute(array(            
                    'idpiece'       =>$idPiece,
                    'plan'          =>$plan
        ));        
    }
    
    public function deletePieceCommande($idPieceCommande){         
        $pieceCommandeDelete = $this->db->prepare('DELETE FROM commandes_pieces
                                                   WHERE id=:id');
        
        $pieceCommandeDelete->execute(array(            
                    'id'       =>$idPieceCommande
        ));
    }
    
    public function deleteAllPiecesOfOrder($idCommande){
        $piecesCommandeDelete = $this->db->prepare('DELETE FROM commandes_pieces
                                                   WHERE id_commande=:idCommande');
        
        $piecesCommandeDelete->execute(array(            
                    'idCommande'       =>$idCommande
        ));
    }
    
    
}