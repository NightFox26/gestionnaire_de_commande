<section id="modalEditClient">
    <form action="">
        <h2 class="textBlue">Edition des infos du client</h2>
        
        <div class="divBtn">
            <button type="submit" class="btn btn_valid"><i class="fas fa-save"></i></button>
            <button type="button" id="closeBox"><i class="fas fa-times"></i></button>
        </div>
        
        <input type="number" name="idClient" value="" hidden>
        
        <label for="">Nom : </label>
        <input type="text" name="nom" value="">
        <br>
        
        <label for="">Email : </label>
        <input type="text" name="mails" value="" title="si plusieurs mails, mettre un separeteur '/', ex : Mail_1 : mail1@free.fr / Mail_2 : mail2@free.fr">
        <br>
        
        <label for="">Telephones : </label>
        <input type="text" name="tels" value="" maxlength="500000" title="si plusieurs telephones, mettre un separeteur '/', ex : Num_1 : 04 44 44 44 44 / Num_2 : 05 05 05 05 05">
        <br>
        
        <br>
        <label for="type">Type : </label>
        <select name="type" id="type">
            <option value="pro">Professionel</option>
            <option value="particulier">Particulier</option>
        </select>
        <br>
        
        <br>
        <label for="">Adresse : </label>
        <textarea name="adresse" id="" cols="34" rows="6"></textarea>
            
    </form>
</section>