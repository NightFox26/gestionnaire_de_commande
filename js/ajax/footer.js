$(function(){
   
    $("#admin .footerManage form").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        console.log(formData);
                        
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/footerFormsValidate.php",
            data: formData,
            dataType: 'json',
            success: function(data){
                console.log(data);
                console.log("la configuration du footer a changé !");
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax du formulaire 'modif footer config' : "+e);
                console.log(res);
            }
        });        
    });
            
});