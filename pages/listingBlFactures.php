<?php
include("../include/header.php");

$year = null;
$month =null;
$monthName =null;
$orders = null;

if(isset($_GET['month']) and isset($_GET['year'])){
    $year = htmlspecialchars($_GET['year']);
    $month = htmlspecialchars($_GET['month']);
    $monthName = monthName($month);
    $orders = $commandeMng->getCommandesOrderByClientsTerminateThisMonth($month,$year);
}

?>

    <body id="blFacturesPage">
        <section class="bg blFacturesBg">
        <?php
        $cree_par = "atelier";
        include(__DIR__."/../include/nav-users.php");        
        include(__DIR__."/../include/nav-menu.php");
        include(__DIR__."/../include/clock.php");        
        include(__DIR__."/../admin/forms/commandesForm.php");
        ?>
        <div class="tcs-container">
            <p id="monthChoice">
                <a href="listingBlFactures.php?<?= monthMoins($month,$year);?>"><i class="fas fa-chevron-left"></i></a>
                <a href="listingBlFactures.php?month=<?= $month;?>&year=<?= $year;?>" class="textBlue"><i class="fas fa-list-alt"></i> Mois de <?= $monthName.' '.$year;?></a>
                <a href="listingBlFactures.php?<?= monthPlus($month,$year);?>"> <i class="fas fa-chevron-right"></i></a>
                
                <input type="month" id="inputChangeMonth" name="inputChangeMonth" min="2019-01" value="<?=$year."-".$month;?>">
            </p>
            <?php 
            if(count($orders)<1){
                echo "Aucune commandes n'est terminées pour le mois de ".$monthName." ".$year;
            }else{                
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Ref TCS</th>
                        <th>Ref Client</th>
                        <th>Client</th>
                        <th>Date reception</th>
                        <th>Date reception</th>
                        <th>Listing pieces</th>
                        <th>Statut commande</th>
                        <th>Bon de livraison ?</th>
                        <th>Facturé ?</th>
                        <th>Envoyé ?</th>
                    </tr>
                </thead>
                
                <tbody> 
                    <?php
                    foreach($orders as $order): 
                        $client = $order->getClientInfos();
                        $pieces = $order->getPieces();                        
                        $blChecked = $order->getIsBonLivraion() == 1? "checked":"";
                        $factureChecked = $order->getIsFacture() == 1? "checked":"";
                        $envoyeChecked = $order->getIsEnvoye() == 1? "checked":"";
                
                        if($blChecked == "checked" and $factureChecked == "checked" and $envoyeChecked == "checked"){
                            $statutColor = "statutLivre";
                        }elseif($blChecked == "checked" or $factureChecked == "checked" or $envoyeChecked == "checked"){
                            $statutColor = "statutEnCours";
                        }elseif($blChecked == "" and $factureChecked == "" and $envoyeChecked == ""){
                            $statutColor = "statutRetard";
                        }
                    ?> 
                        <tr class="<?= $statutColor;?>">
                            <td>
                               <?php
                                $refTcs = $order->getRefTcs();
                                if(isAdmin()){
                                    echo '<a href="/'.$baseUrl.'admin.php?refTcs='.$refTcs.'" class="textBlack">'.$refTcs.'</a>';
                                }else{
                                    echo $refTcs;  
                                }
                                ?>                                
                            </td>
                            <td><?= $order->getRefClient();?></td>
                            <td><?= $client->getNom();?></td>
                            <td><?= date('d/m/Y', strtotime($order->getDateRecept()));?></td>
                            <td><?= date('d/m/Y', strtotime($order->getDateLivr()));?></td>
                            <td>
                                <ul class="listingPieces">
                                    <?php
                                    foreach($pieces as $piece): ?>
                                        <li>- <a href="<?= $root."pages/piece.php?idPieceCommande=".$piece->getId()."&idCommande=".$order->getId();?>" target="_blank"><?= $piece->getRef();?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><?= $order->getStatut();?></td>

                            <td>Bon Livraison <br>
                                <form action="" class="blFactureForm" data-idOrder="<?= $order->getId();?>">
                                    <input type="checkbox" name="blChecked" <?= $blChecked;?>>
                                </form>
                            </td>

                            <td>Facturé <br>
                                <form action="" class="blFactureForm" data-idOrder="<?= $order->getId();?>">
                                    <input type="checkbox" name="factureChecked" <?= $factureChecked;?>>
                                </form>
                            </td>

                            <td>Envoyé <br>
                                <form action="" class="blFactureForm" data-idOrder="<?= $order->getId();?>">
                                    <input type="checkbox" name="envoyeChecked" <?= $envoyeChecked;?>>
                                </form>
                            </td>
                        </tr>                       
                    <?php endforeach; ?>                
                </tbody>
            </table>
            <?php 
            }
            ?>
        </div>
                
                
                <?php include(__DIR__."/../include/footer.php") ?>
        </section>
        <?php include(__DIR__."/../include/footerJs.php") ?>
    </body>

    </html>