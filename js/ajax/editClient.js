$(function () {
    $("#modalEditClient").draggable({
        containment: "parent"
    });
    
    $("#closeBox").click(function () {
        $("#modalEditClient").hide(300);
    });
    
    //click sur le bouton pour ouvrir la fenetre de modification d'un client
    $(".editClient").click(function () {
        let idClient = $(this).attr("data-id");
        console.log("client select = "+idClient);
        $.ajax({
            type: "GET",
            url: baseUrl + "/admin/getDatasAjax/getClient.php",
            data: { idClient: idClient},
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    alert(data.error);
                }
                else {
                    console.log(data);
                    setHtmlInfosClient(data);
                    $("#modalEditClient").show(300);
                }
            },
            error: function (res, statut, e) {
                alert("Erreur sur la requete ajax 'edition d'un client : " + e);
                console.log(res);
            }
        });        
    });
    
    //click sur le bouton pour sauvegarder les modifications d'un client
    $("#modalEditClient form").submit(function (e) {
        e.preventDefault();        
        var formDatas = new FormData($("#modalEditClient form")[0]);
                
        $.ajax({
            type: "POST",
            url: baseUrl + "/admin/formsValidate/clientFormsValidate.php",
            data: formDatas,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.error) {
                    alert(data.error);
                }else {
                    console.log(data); 
                    $("#modalEditClient").hide(300);
                }
            },
            error: function (res, statut, e) {
                alert("Erreur sur la requete ajax edition d'un client : " + e);
                console.log(res);
            }
        });        
    });
    
    
    //remplie les infos dans les champs du formulaire lors du click sur editer un client
    function setHtmlInfosClient(datas){
        $("#modalEditClient form input[name='idClient']").val(datas.id);
        $("#modalEditClient form input[name='nom']").val(datas.nom);
        $("#modalEditClient form input[name='mails']").val(datas.mail);
        $("#modalEditClient form input[name='tels']").val(datas.tel);
        $("#modalEditClient form select[name='type']").val(datas.type);
        $("#modalEditClient form textarea").val(datas.adresse);
    }   
    
});