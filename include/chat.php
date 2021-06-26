<section id="chatBox" class="backg_third b_shadow">    
    <h2 class="handleDrag">Boite de dialogue</h2>
    <button id="closeChat" class="closeBtn"><i class="fas fa-times"></i></button>
    
    <hr>
    
    <div class="messageListe">        
       <ul>
           <?php
                $messages = $messageMng->getMessages();
                foreach($messages as $message):
                $date = $message->getDateM();
           ?>
                   <li>
                   <date><?= date('d/m/Y à H:i', strtotime($date));?></date> <br>
                   <i class="fas fa-user"></i>
                   <span class="user"><?= $message->getNom();?> : </span>
                   <?= nl2br($message->getText());?>           
                   </li>
           <?php endforeach; ?>
       </ul>       
    </div>
    
    <hr>
    
    <form action="" method="post">        
        <input type="text" name="message" >
        <input type="submit" value="Envoyer">
    </form>
</section>