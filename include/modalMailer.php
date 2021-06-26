<div class="modal mailer">
    <div class="modalHeader"><img src="<?= $root;?>/image/thunderbird.png" alt="thunderbird icon"></div>
    <div class="modalBody">
        <h2>Souhaitez vous avertir le client ?</h2>
        <p>Cette commande est terminée, <u><b>cocher l'adresse email</b></u> correspondante pour envoyer un mail automatiquement au client (<b>plusieurs adresses email peuvent etre cochées</b>)</p>
        <form action="#" method="post">
            <input type="text" name="refClient" value="" hidden>
            <input type="text" name="nomClient" value="" hidden>
            <ul>
                
            </ul>
            <div class="modalFooter">
                <button id="closeModal" class="btn_shadow" type="reset">Ne pas envoyer</button>
                <button class="statutUrgent btn_shadow" type="submit">Envoyer</button>
            </div>
        </form>
    </div>
</div>