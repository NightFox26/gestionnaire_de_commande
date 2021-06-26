$(function () {
    
    // click sur le changement d'utilisateur
    $("nav.users li").click(function(e){
        e.preventDefault();
        let userSelected = $(this);
        let idUser = $(this).attr("data-iduser");
        console.log(idUser);
        
        $.ajax({
            type: "GET",
            url: baseUrl+"/admin/formsValidate/usersFormsValidate.php",
            data: {idUser:idUser},
            dataType: 'json',
            success: function(data){
                console.log("l'utilisateur :"+data["nom"]+' est selectionné !');
                $("nav.users li").removeClass("selected");
                $(userSelected).addClass("selected");
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de selection de l'ouvrier : "+e);
                console.log(res);
            }
        });        
    });
    
    $(".ouvriersManage").on('submit','form.modifyUserForm',function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        //console.log(formData);
                        
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/usersFormsValidate.php",
            data: formData,
            dataType: 'json',
            success: function(data){
                console.log("l'utilisateur :"+data[1]+' a été mis a jour !');
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax du formulaire 'ouvrier' : "+e);
                console.log(res);
            }
        });        
    });
    
    $(".ouvriersManage").on('click','button.deleteUser',function(){        
        var id = $(this).attr("data-idUser");
                                
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/usersFormsValidate.php",
            data: {id:id,delete:true},
            dataType: 'json',
            success: function(data){
                console.log("l'utilisateur :"+data[0]+' a été effacé !');
                $('.ouvriersManage .user-'+data[0]).slideUp(200,function(){
                    $(this).remove();
                });
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax du formulaire 'desactivation ouvrier' : "+e);
                console.log(res);
            }
        });        
    });
    
    $(".ouvriersManage #addUserForm").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        //console.log(formData);
                        
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/usersFormsValidate.php",
            data: formData+"&ajout=1",
            dataType: 'json',
            success: function(data){
                console.log(data);
                console.log("l'utilisateur :"+data.nom+' a été ajouté !');
                $('.ouvriersManage ul').append(addHtmlUser(data.id,data.nom,data.statut));
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax du formulaire 'ajout ouvrier' : "+e);
                console.log(res);
            }
        });        
    });
    
    
    function addHtmlUser(id,nom,statut){
        var ouvrier = "",
        interim = "",
        cadre = "";
        if(statut == "ouvrier"){
            ouvrier = "selected";
        }else if(statut == "interimaire"){
            interim = "selected";
        }else if(statut == "cadre"){
            cadre = "selected";
        }                   
                        
        return '<li class="user-'+id+'"><form action="" method="post" class="inline modifyUserForm"><input type="number" name="id" value="'+id+'" hidden><input type="text" value="'+nom+'" name="nom">  <select name="statut" id=""><option value="ouvrier" '+ouvrier+'>Ouvrier</option><option value="interimaire" '+interim+'>Interimaire</option><option value="cadre" '+cadre+'>Cadre</option></select> <button type="submit" class="btn btn_done"><i class="fas fa-save"></i></button></form><button class="btn btn_danger float_right deleteUser" data-idUser="'+id+'"><i class="fas fa-trash-alt"></i></button></li>';        
    }   
    
    
    
})