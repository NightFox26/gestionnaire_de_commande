<?php
include(__DIR__."/../include/header.php");
?>

<body id="ordersPiecesClientPage">
    <section class="bg searchBg">
        <?php
        $cree_par = "atelier";
        include(__DIR__."/../include/nav-users.php");        
        include(__DIR__."/../include/nav-menu.php");
        include(__DIR__."/../include/clock.php");        
        include(__DIR__."/../admin/forms/commandesForm.php");
        
        
        
        ?>
        
        <div class="tcs-container">
            <?php
                if(isset($_GET['clientId']) && $_GET['clientId']>0){
                    $idClient = htmlspecialchars($_GET['clientId']);
                    $client = $clientMng->getClient($idClient); 
                    $orders = $commandeMng->getCommandesByClient($idClient);
                    $pieces = $pieceMng->getPiecesByClient($idClient);
                }else{
                    die("Ce client n'existe pas !!!");
                }
            ?>
            
            <section class="b_shadow ordersList">
                <h1><i class="fas fa-folder-open"></i>
                 Listing des commandes de "<?= ucfirst($client->getNom());?>"
                </h1>
                
                <?php 
                $lastOrderYear = "";
                $lastOrderMonth = "";
                $i = 0;
                foreach($orders as $order):
                    $orderDate = strtotime($order->getDateRecept());
                    $orderYear = date("Y", $orderDate); 
                    $orderMonth = date("m", $orderDate); 
                    
                    if($orderYear != $lastOrderYear or $orderMonth != $lastOrderMonth){
                        if($i>0){echo '</ul><hr>';}
                        $lastOrderYear = $orderYear;
                        $lastOrderMonth = $orderMonth;
                        $i++;
                        echo '<h2><i class="far fa-calendar-alt"></i> '.monthName($orderMonth).' '.$orderYear.'</h2>';
                        echo '<ul>';
                    }?>
                    <li class="order">
                        <a href="<?= $root."pages/commandes.php?id_commande=".$order->getId();?>" target="_blank"><i class="fas fa-file-invoice"></i> <?= $order->getRefTcs(); ?></a>
                    </li>
                <?php endforeach;?>   
            </section>
            
            
            <section class="b_shadow piecesList">
                <h1><i class="fas fa-folder-open"></i> 
                Listing des pieces de "<?= ucfirst($client->getNom());?>"
                </h1> 
                <ul>
            
                <?php 
                $lastOrderYear = "";
                $lastOrderMonth = "";
                $i = 0;
                foreach($pieces as $piece):
                ?>
                        <li class="piece">
                            <a href="<?=$root.'pages/piece.php?idPiece='.$piece->getId()?>" target="_blank"><i class="fas fa-file-pdf"></i> <?= $piece->getRef(); ?></a>
                        </li>
                <?php endforeach;?>
                </ul>
            </section>
            
        </div>
        
        
        
        
        <?php include(__DIR__."/../include/footer.php") ?>
    </section>
    <?php include(__DIR__."/../include/footerJs.php") ?>
</body>
</html>