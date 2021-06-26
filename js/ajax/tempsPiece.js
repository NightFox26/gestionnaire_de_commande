$(function () {
    
    //Cacher la modal d'alert facturation
    $(".modal.alert").click(function(){
        $(this).slideUp(200);
        location.reload();
    });    
            
    //click sur la sauvegarde d'un temps et de la matiere d'une piece
    $("#commandesPage .addTimeForm").submit(function(e){
                    
        e.preventDefault();
        var idPiece = $(this).parents("li").attr("data-idPiece"); 
        var userName = $.trim($("nav.users li.selected").text());
        var form = $(this)[0];
                
        var formDatas = new FormData($(this)[0]);
        formDatas.append('addPiece',true);
        formDatas.append('piece_id',idPiece);
        formDatas.append('user',userName);
                        
        //si la commande est cloturé on interdit l'ajout de temps
        let datasPiece = dataPiece(idPiece);        
        if(datasPiece.clotured == "1"){
            pieceIsClotured(datasPiece.ref);
            return;
        }
        
        //si la commande est deja facturé on interdit de noter du temps ou de la matiere
        let verifFactured = isOrderFacturedByPiece(idPiece);
        if(verifFactured.factured == 1){
            orderIsFactured(verifFactured.refCommande);
            return;
        }
       
        if($(form).find("input[name='addTime']").val() != "" ||
          $(form).find("textarea[name='infosPiece']").val() !=""){ 
            $.ajax({
                type: "POST",
                url: baseUrl+"/admin/formsValidate/piecesTimesFormsValidate.php",
                data: formDatas,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(dataPiece){
                    console.log(dataPiece);
                    if(dataPiece.error){
                        alert(dataPiece.error);
                    }else{
                        alert("Le temps et infos fabrication de '"+userName+"' ont bien été sauvegarder !");
                        $(form).find("input[name='addTime']").val('');
                        $(form).find("textarea").val('');
                        $(form).find("textarea[name='infosPiece']").css('display','none');
                    } 
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax de l'ajout de temps sur la piece : "+e);
                    console.log(res);
                }
            }); 
        }
    });
    
    
    //click sur le bouton start pour compter le temps
    $("#commandesPage .StartTimer").click(function(){
        var btnStart = $(this);
        var statutPieceSelect = $(btnStart).nextAll("form.statusPieceForm").find("select")
        var userName = $.trim($("nav.users li.selected").text());
        var piece_id = $(this).parents("li").attr("data-idPiece");
        var starting = confirm("Voulez vous vraiment demarrer un chronometre pour '"+userName+"' sur cette piece ?");
        
        //si la commande est cloturé on interdit l'ajout de temps
        let datasPiece = dataPiece(piece_id);        
        if(datasPiece.clotured == "1"){
            pieceIsClotured(datasPiece.ref);
            return;
        }
        
        let verifFactured = isOrderFacturedByPiece(piece_id);
        if(verifFactured.factured == 1){
            orderIsFactured(verifFactured.refCommande);
            return;
        }
        
        if(starting){
            $.ajax({
                type: "POST",
                url: baseUrl+"/admin/formsValidate/piecesTimesCompteurFormsValidate.php",
                data: {piece_id:piece_id,
                       user:userName,
                       startCounter:true
                      },
                dataType: 'json',
                success: function(data){
                    if(data.error){
                        alert(data.error);
                    }else{
                        console.log(data);
                        $(btnStart).addClass("startCountTime");
                        $(statutPieceSelect).val("En cours").change();
                        location.reload();
                    }
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax du demarrage compteur de temps : "+e);
                    console.log(res);
                }
            });
        }
    });
    
    //click sur le bouton stop du compteur de temps
    $("#commandesPage .StopTimer").click(function(){
        var btnStop = $(this);
        var idC = $(btnStop).attr('data-idCounteur');
        var idPiece = $(this).parents("li").attr("data-idPiece");
        var user = $.trim($("nav.users li.selected").text());
        
        let verifFactured = isOrderFacturedByPiece(idPiece);
        if(verifFactured.factured == 1){
            orderIsFactured(verifFactured.refCommande);
            return;
        }
        
        if(idC){
            $.ajax({
                type: "POST",
                url: baseUrl+"/admin/formsValidate/piecesTimesCompteurFormsValidate.php",
                data: {idCounter:idC,
                       stopCounter:true,
                       user:user,
                       piece_id:idPiece
                      },
                dataType: 'json',
                success: function(data){
                    if(data.error){
                        alert(data.error);
                    }else{
                        console.log(data); $(btnStop).prev().removeClass("startCountTime")
                        $(btnStop).attr('data-idCounteur','');
                    }
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax de l'arret compteur de temps : "+e);
                    console.log(res);
                }
            });            
        }else{
            alert("Aucun compteur actif pour cette piece et cet utilisateur !");
        }
    });
    
    // fonction qui verifie si la commande est deja facturé en fonction d'une piece
    function isOrderFacturedByPiece(pieceId){
        var factured = "false";
        var refCommande = "";
        $.ajax({
            type: "get",
            url: baseUrl+"/admin/getDatasAjax/getOrder.php",
            data:{pieceId:pieceId,
                  facturedOrderCheck:true,
                 },
            dataType: 'json',
            async:false,
            success:function(data){                
                factured =  data.factured;
                refCommande = data.refCommande;
            },
            error:function(res,statut,e){
                alert("Erreur sur la requete ajax pour verifié si la commande est deja facturé : "+e);
                console.log(res);
            }
        });
        return {factured:factured,refCommande:refCommande};
    }
    
    // fonction qui verifie si la piece est cloturée
    function dataPiece(pieceId){
        var datas = "false";        
        $.ajax({
            type: "get",
            url: baseUrl+"/admin/getDatasAjax/getPiece.php",
            data:{idPiece:pieceId},
            dataType: 'json',
            async:false,
            success:function(data){                
                datas = data;
            },
            error:function(res,statut,e){
                alert("Erreur sur la requete ajax pour verifié si la piece est cloturé : "+e);
                console.log(res);
            }
        });
        return datas;
    }
            
    function orderIsFactured(ref_tcs){
        $(".modal").slideDown(100).css("display","flex");
        $("#chatBox form input[name='message']").val("!!! MESSAGE SYSTEM !!!\
        Tentative d'ajout de temps ou matiere apres facturation sur la commande "+ref_tcs+" !!!");
        $("#chatBox form input[type='submit']").trigger('click');
    }
    
    function pieceIsClotured(ref_piece){
        $(".modal").slideDown(100).css("display","flex");
        $("#chatBox form input[name='message']").val("!!! MESSAGE SYSTEM !!!\
        Tentative d'ajout de temps ou matiere sur une piece cloturée :  "+ref_piece+" !!!");
        $("#chatBox form input[type='submit']").trigger('click');        
    }
    
    
})