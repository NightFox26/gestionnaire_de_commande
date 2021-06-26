<?php
include(__DIR__."/../include/header.php");
?>

<body id="piecePage">
    <section class="bg pieceBg">
        <?php
        $cree_par = "atelier";
        include(__DIR__."/../include/nav-users.php");        
        include(__DIR__."/../include/nav-menu.php");
        include(__DIR__."/../include/clock.php");        
        include(__DIR__."/../admin/forms/commandesForm.php");
        
        $iconFiltre = '<i class="fas fa-sort-down"></i>';
        $order = null;
        $orders = null;
        $cloturedPieceBorder = "";
        if(isset($_GET['idPieceCommande'])){
            $idPieceCommande = htmlspecialchars($_GET['idPieceCommande']);             
            $idOrder = htmlspecialchars($_GET['idCommande']); 
            $piece = $pieceCommandeMng->getPiece($idPieceCommande);     
            $inOtherOrders = null;
            if($piece){
                $idPiece = $piece->getIdPiece();
                $order = $commandeMng->getCommande($idOrder);
                $orderId = $order->getId();
                $client = $order->getClientInfos();
                $datePiece = date('d/m/Y', strtotime($order->getDateLivr()));
                
                $dateLivrPiece = $piece->getDateLivr();
                if($dateLivrPiece==null){
                    $dateLivrPiece = "Non renseigné";
                }else{
                    $dateLivrPiece = date('d/m/Y', strtotime($piece->getDateLivr()));
                }
                
                
                $dataId = 'data-idPieceCommande="'.$idPieceCommande.'"';
                $allTimes = $tempsMng->getTemps($idPieceCommande);
                $counters = $counterMng->getAllCompteursForPiece($idPieceCommande);
                $clientMails = $client->extractMultipleMails();
                $clientTels = $client->extractMultipletels();                
                $orders = $commandeMng->getCommandeWithPiece($idPiece); 
                $cloturedPieceBorder = $piece->getIsCloture()==1?"cloturedPieceBorder":"";               
            }
        }elseif(isset($_GET['idPiece'])){
            $idPiece = htmlspecialchars($_GET['idPiece']); 
            $piece = $pieceMng->getPiece($idPiece);
            $orderId = null;
            if($piece){
                $client = $piece->getClient();
                $orders = $commandeMng->getCommandeWithPiece($idPiece);  
                $dataId = 'data-idPiece="'.$idPiece.'"';
                $clientMails = $client->extractMultipleMails();
                $clientTels = $client->extractMultipletels();
                $inOtherOrders = $pieceCommandeMng->getPiecesCommandesByIdGloabalPiece($idPiece);                
				$allTimes = null;
            }
        }
        ?>

        <div class="tcs-container <?= $cloturedPieceBorder;?>">  
            <?php
            if($piece){                
                if($order): ?>                                
                    <div class="qtPiece">(<i class="fas fa-boxes"></i> <?= $piece->getQt();?> piece(s) dans la     <br>commande ref :                     
                        <a href='<?= $root."pages/commandes.php?id_commande=".$orderId;?>'><?= $order->getRefTcs(); ?></a> )
                    </div>                
                <?php endif; ?>            
                
            <h1 class="inline"><i class="fas fa-pallet"></i>
                Ref piece : <?= $piece->getRef();?>
            </h1>
            
            <div class="inline">
                <?php
                if(isAdmin()){
                    $classTime = "";
                    $classOtherOrders = "";
                    if($allTimes){
                        $classTime = " times";
                    }
                    if($inOtherOrders){
                        $classOtherOrders = " inOtherOrders";
                    }
                    
                    echo '<button id="deletePiece" class="btn btn_danger '.$classTime.$classOtherOrders.'" title="supprimer la piece" '.$dataId.'><i class="fas fa-trash-alt"></i></button>';
                }
                echo'<button id="savePiece" class="btn btn_valid" title="savegarder la piece" '.$dataId.' ><i class="fas fa-save"></i></button>';
                ?>
            </div>
            
            <?php
            if(isAdmin() and isset($_GET['idPieceCommande'])): 
                $iconLock = $piece->getIsCloture()==0?"fa-lock":"fa-unlock";
                $colorLock = $piece->getIsCloture()==0?"orange":"blue";
                $valueLock = $piece->getIsCloture()==0?"1":"0";
                ?>
                <form action="../admin/formsValidate/piecesOrdersFormsValidate.php" method="post" class="formLockPiece" enctype="multipart/form-data">
                    <input type="nombre" hidden name="idPieceCom" value="<?= $piece->getId();?>">
                    <input type="text" hidden name="clotured" value="<?= $valueLock;?>">
                    <button id="cloturePiece" type="submit" class="btn <?=$colorLock;?>"
                    title="cloture la piece pour facturation"><i class="fas <?=$iconLock;?>"></i></button>
                </form>
            <?php endif;?>
                
            <form action="" class="formDataPiece">            
            <table>
                <tbody>
                    <tr>
                        <td><i class="fas fa-list-ol"></i> Id piece BDD:</td>
                        <td><?= $idPiece!=null? $idPiece:"Pas d'ID (Piece sans reutilisation)";?></td>
                    </tr>

                    <tr>
                        <td><i class="fas fa-users-cog"></i> Client :</td>
                        <td>
                            <table>
                               <tr class="clientName">
                                   <td><i class="fas fa-address-card"></i></td>
                                   <td><?= $client->getNom()?></td>
                               </tr>
                               <tr>
                                   <td><i class="fas fa-envelope-square"></i></td>
                                   <td>
                                       <?php 
                                        if($clientMails):
                                            foreach($clientMails as $mail): ?>
                                                -<a href="mailto:<?= $mail;?>">
                                                <?= $mail;?></a><br>
                                        <?php endforeach; 
                                        endif;?> 
                                   </td>
                               </tr>
                               <tr>
                                   <td><i class="fas fa-phone-square"></i></td>
                                   <td>
                                       <?php 
                                        if($clientTels):
                                            foreach($clientTels as $tel): ?>
                                                - <?= str_replace(':',':<br>',ucfirst($tel));?><br>
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
                        </td>
                    </tr>

                    <tr>
                        <td><i class="fas fa-fill-drip"></i> RAL Peinture :</td>
                        <td>
                        <?php 
                            $color = !empty($piece->getRal())?$piece->getRal():"Aucun";
                            echo "<input type='text' name='ral' value='$color'>";
                        ?>
                        </td>
                    </tr>
                    
                    <?php 
                    if($orderId != null):
                    ?>
                    <tr>
                        <td><i class="fas fa-info-circle"></i> Infos temporaire:</td>
                        <td> 
                            <textarea name="infosUnique" placeholder="Ces informations sont uniquement persistées pour cette commande !"><?= str_replace('<br/>', "\n", $piece->getInfosUnique())?></textarea>
                       </td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td><i class="fas fa-file-invoice"></i> Infos annexes:</td>
                        <td> 
                            <textarea name="infos" placeholder="Ces informations remonteront pour les prochaines pieces !"><?= str_replace('<br/>', "\n", $piece->getInfos())?></textarea>
                       </td>
                    </tr>  

                    <?php if($order): ?>                                             
                        <tr>
                            <td><i class="far fa-calendar-alt"></i> Delais souhaité :</td>
                            <td><?= $datePiece;?></td>
                        </tr> 
                        
                        <tr>
                            <td><i class="fas fa-truck"></i> Date de livraison :</td>
                            <td><?= $dateLivrPiece==null?"":$dateLivrPiece;?></td>
                        </tr>                                                                                                                        

                        <?php if($piece->getUrgente() == 1): ?>    
                        <tr>
                            <td>
                                <i class="fas fa-tachometer-alt" title="Piece urgente"> Urgence ?</i>
                            </td>
                            <td style="color:red;text-decoration:underline;font-weight:bolder;">
                                Piece Urgente !
                            </td>                                   
                        </tr> 
                        <?php endif;?>                        

                        <tr>
                            <td><i class="fas fa-history"></i> Temps passé :</td>
                            <td><?= calcTotalTime($allTimes);?>h <i class="fas fa-long-arrow-alt-left"></i>
                            <ul>
                                <?php 
                                $i=0;
                                foreach($allTimes as $temps):
                                    $classArrow = $i == 0? "fa-long-arrow-alt-right":"fa-level-down-alt";
                
                                    $classHeuresSup = $temps->getHeuresSup() == 1? " heuresSup":"";
                                                
                                    echo "<li class='$classHeuresSup'><i class='fas $classArrow'></i> "
                                        .$temps->getUser().
                                        ": ".$temps->getTemps()."h</li>";
                               
                                    $i++;
                                endforeach;
                                
                                foreach($counters as $counter){
                                    if($counter->getStoped() == null){
                                        echo "<li class='textRed'><i class='fas fa-hourglass-half'></i> "
                                            .$counter->getUser().
                                            ": Compteur de temps activé depuis ".$counter->getStarted()."</li>";  
                                    }
                                }
                                
                                ?>
                            </ul>                            
                            </td>
                        </tr>

                        <tr>
                            <td><i class="fas fa-file-invoice"></i> Infos Fabrication :</td>
                            <td><?= nl2br($piece->getInfosFab());?>
                           </td>
                        </tr>                    
                    <?php endif; ?>

                    <?php
                    if(count($orders)>1 or ($orderId == null and count($orders)>0)):?>
                        <tr>
                            <td><i class="fas fa-file-invoice"></i> Piece connu dans commande(s) :</td>
                            <td>
                               <?php foreach($orders as $order): 
                                   if($order->getId() != $orderId):
                                   $idPieceCommande = $pieceCommandeMng->getPieceByCommande($idPiece,$order->getId())->getId();
                               ?>
                                   <a href="<?= $root."pages/piece.php?idPieceCommande=".$idPieceCommande."&idCommande=".$order->getId();?>"><?= $order->getRefTcs();?></a><span> ---</span>
                               <?php 
                                    endif;
                                endforeach; ?>
                           </td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td><i class="fas fa-file-pdf"></i> Plan :</td>

                        <?php
                        $plan = 'empty.jpg';
                        $title = "Aucun plan";
                        $iconePlan = $root.'image/plansIcons/'.$plan;
                        if(!empty($piece->getPlan())){                           
                            $plan = $piece->getPlan();                             
                            $title = $piece->getRef();
                            $iconePlan = $root.'plans/'.$plan;
                            if($piece->isPdfPlanType()){                                
                                $iconePlan = $root.'image/plansIcons/pdf.png';
                            }                           
                        }                        
                        ?>
                        <td>
                            <div id="fileDiv">
                                <a href="<?= $root.'plans/'.$plan;?>" target="_blank"><img src="<?= $iconePlan;?>" alt="Plan piece <?= $title;?>" title="Plan piece <?= $title;?>" height="200"></a>
                            </div>
                                                        
                            <input type="hidden" name="MAX_FILE_SIZE" value="10194304" /> 
                            <input type="file" name='piece_plan' accept="image/*,.pdf">

                            <button id="savePiece2" class="btn btn_valid fileBtn" title="savegarder la piece" <?= $dataId;?> ><i class="fas fa-save"></i></button>                       
                        
                        </td>
                    </tr>
                </tbody>
            </table>            
            </form>
        <?php
            }else{
                echo "Aucune piece ne porte cette reference !";
            }
        ?>
        </div>
        
         <?php include(__DIR__."/../include/footer.php") ?>
    </section>
    <?php include(__DIR__."/../include/footerJs.php") ?>
</body>
</html>