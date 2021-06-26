<?php
include(__DIR__."/../include/header.php");
?>

<body id="searchPage">
    <section class="bg searchBg">
        <?php
        $cree_par = "atelier";
        include(__DIR__."/../include/nav-users.php");        
        include(__DIR__."/../include/nav-menu.php");
        include(__DIR__."/../include/clock.php");
        include(__DIR__."/../admin/forms/commandesForm.php");
        
        include(__DIR__."/../include/editClient.php");
        
        $word = htmlspecialchars($_POST['search']);
        $commandes = null;
        $clients = null;
        $pieces = null;        
        if(!empty($word)){            
            $commandes = $commandeMng->getCommandesSearch($word);
            $clients = $clientMng->getClientsSearch($word);
            
            $pieces = $pieceMng->getPiecesSearch($word); 
            $commandesPieces = $pieceCommandeMng->getCommandesPiecesSearch($word);
            $pieces = array_merge($pieces, $commandesPieces);
        }
        ?>
        
        <div class="tcs-container b_shadow">
            <img src='<?= $root."/image/logotcs.png";?>' class="b_shadow tcsLogo" alt="logo tcs">
            <h1 class="t_shadow">
                <i class="fab fa-searchengin"></i> Recherche du mot : <span><?= $word;?></span>
            </h1>
            
            <div class="inline">
                <h2 class="t_shadow"><i class="fas fa-file-signature"></i> Commande(s) : </h2>
                <ul>
                    <?php 
                    if($commandes):
                        foreach($commandes as $order): ?>
                            <li><a href='<?= $root."pages/commandes.php?id_commande=".$order->getId();?>' target="_blank"><?= $order->getRefTcs();?></a></li>
                        <?php 
                        endforeach;
                    else:
                        echo 'Aucune commande trouvée !';
                    endif;
                    ?>
                </ul>
            </div>
              
            <div class="inline">  
                <h2 class="t_shadow"><i class="fas fa-pallet"></i> Piece(s) : </h2>
                <ul>
                   <?php 
                    if($pieces):
                        foreach($pieces as $piece): 
                            if(is_a($piece,'PieceCommande')): 
                                $order = $commandeMng->getCommande($piece->getIdCommande());
                            ?>
                                <li><a href="<?= $root."pages/piece.php?idPieceCommande=".$piece->getId()."&idCommande=".$piece->getIdCommande();?>" target="_blank"><?=$piece->getRef();?></a> (Commande : <?= $order->getRefTcs();?>)</li>                            
                        <?php else: ?>
                                <li><a href="<?= $root."pages/piece.php?idPiece=".$piece->getId();?>" target="_blank"><?=$piece->getRef();?></a></li>
                        <?php endif;
                        endforeach;
                    else:
                        echo 'Aucune piece trouvée !';
                    endif;
                    ?>                                        
                </ul>
            </div>
                
            <h2 class="t_shadow"><i class="fas fa-users"></i> Client(s) :</h2>
                <ul class="listClient">
                    <?php 
                        if($clients):
                            foreach($clients as $client): 
                                $clientMails = $client->extractMultipleMails();
                                $clientTels = $client->extractMultipletels();
                            ?>                            
                                <li class="capitalise clientName inline">
                                   <a href="<?= $root."pages/clientOrdersPieces.php?clientId=".$client->getId();?>" class="nameClient" title="Vers le listing des commandes et pieces de ce client"><i class="fas fa-folder-open"></i> <?= $client->getNom();?></a>
                                   
                                   <button class="editClient textGreen" data-id="<?= $client->getId();?>"><i class="fas fa-user-edit"></i></button>
                                   
                                    <table class="tableInfosClient">  
                                        <tr>
                                            <td><i class="fas fa-envelope-square"></i></td>
                                            <td>
                                            <?php 
                                                if($clientMails):
                                                    foreach($clientMails as $mail): ?>
                                                        -<a href="mailto:<?= $mail;?>">
                                                        <?= $mail;?></a><br>
                                            <?php   endforeach; 
                                                endif;?>
                                            </td>
                                        </tr>                                    
                                        <tr>
                                            <td><i class="fas fa-phone-square"></i></td>
                                            <td class="capitalise">
                                            <?php 
                                                if($clientTels):
                                                    foreach($clientTels as $tel): ?>
                                                        - <?= ucfirst($tel);?><br>
                                            <?php   endforeach; 
                                                endif;?>
                                            </td>                                    
                                        </tr>                                    
                                        <tr>
                                            <td><i class="fas fa-map-marked-alt"></i></td>
                                            <td><?= nl2br($client->getAdresse());?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-user-tag"></i></td>
                                            <td><?= $client->getType();?></td>                                    
                                        </tr>
                                    </table>
                                </li>                            
                        <?php endforeach;
                    else:
                        echo 'Aucun client trouvé !';
                    endif;
                    ?>
                </ul>
        </div>
        
        
         <?php include(__DIR__."/../include/footer.php") ?>
    </section>
    <?php include(__DIR__."/../include/footerJs.php") ?>
</body>
</html>