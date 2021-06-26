// fonction qui verifie si la commande est deja facturé en fonction de son id
function isOrderFactured(idOrder){
    var factured = "false";
    var refCommande = "";
    $.ajax({
        type: "get",
        url: baseUrl+"/admin/getDatasAjax/getOrder.php",
        data:{idOrder:idOrder,
              facturedOrderCheck:true,
             },
        dataType: 'json',
        async:false,
        success:function(data){
            console.log("1");
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

$(function(){   
    
    // reset le form commande
    $("#addOrderForm #clearCommandeForm").click(function(){        
        $(this).parents("#addOrderForm").find("form")[0].reset();
        $("#addOrderForm #addPieceExistante").hide();
        $("#listPiecesOrder tbody tr").remove();
        $("#listPiecesOrder").hide(200);
        $("#addOrderForm .nomClient").text("");
        $("#formAddPieces").hide(200);
        $("#formAddPieces form")[0].reset();
        $("#addOrderForm #infoSaveOrder").text("");
        window.location = window.location.pathname;
    });    
    
    // efface les donnée du form
    function resetForm(){
        setTimeout(function(){
             $("#addOrderForm #clearCommandeForm").trigger("click");
        },1500);
    }    
       
    /* creation de la commande */
    $("#addOrderForm form#commandeForm").submit(function(e){
        e.preventDefault();
        var clientPro = $.trim($(this).find("input[name='client_nom_pro']").val());
        var clientPart = $.trim($(this).find("input[name='client_nom_part']").val());
        
        var idClientPro = $(this).find("#client_pro option[value='"+clientPro+"']").attr('data-id');
        var idClientPart = $(this).find("#client_part option[value='"+clientPart+"']").attr('data-id');
                
        var formData = $(this).serialize();
        formData += "&id_client_pro="+idClientPro+"&id_client_part="+idClientPart
        console.log(formData); 
        
        emptyFieldRefPiece();
        
        var idOrder = $(this).find("input[name='id']").val();
        if(idOrder){            
            var orderCheck = isOrderFactured(idOrder);
            if(orderCheck.factured == "1"){
                $(".modal.alert").slideDown(100).css("display","flex");
                return;
            }
        }
        
        if(clientPro || clientPart){
            $.ajax({
                type: "POST",
                url: baseUrl+"/admin/formsValidate/ordersFormsValidate.php",
                data: formData,
                dataType: 'json',
                success: function(data){                         
                    if(data.error){
                        alert(data.error)
                    }else{
                        $("#addOrderForm input[name='id']").val(data.id);
                        if(data.inserted>0){  
                            $("#addOrderForm #infoSaveOrder").text("enregistrée !");
                            
                            completePiecesListingRef(data.piecesClient);
                            $("#formAddPieces").show(200);
                            
                            if(data.typeClient == "pro"){
                                $("#addOrderForm form#commandeForm").find("#client_pro").append("<option data-id='"+data.clientId+"' value='"+data.nomClientPro+"'></option>");
                            }else if(data.typeClient == "particulier"){
                                $("#addOrderForm form#commandeForm").find("#client_part").append("<option data-id='"+data.clientId+"' value='"+data.nomClientPart+"'></option>");
                            }
                            
                            $('#addOrderForm').animate({
                                scrollTop:$('#addOrderForm').offset().top + 150,
                            }, 'slow');
                        }else if(data.updated>0){                            
                            $("#addOrderForm #infoSaveOrder").text("mis a jour !");
                            completePiecesListingRef(data.piecesClient);
                            
                            if(data.typeClient == "pro"){
                                $("#addOrderForm form#commandeForm").find("#client_pro").append("<option data-id='"+data.idclient+"' value='"+data.nomClient+"'></option>");
                            }else if(data.typeClient == "particulier"){
                                $("#addOrderForm form#commandeForm").find("#client_part").append("<option data-id='"+data.idclient+"' value='"+data.nomClient+"'></option>");
                            }
                        }else if(data.modalAlert){
                            $(".modal.alert").slideDown(100).css("display","flex");
                        }
                        setTimeout(function(){
                            $("#addOrderForm #infoSaveOrder").text("");
                        },3000);
                    }
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax du formulaire 'ajout de commande' : "+e);
                    console.log(res);
                }
            });
        }else{
            alert("Une commande doit avoir un client !!!");
        }
    });
    
    /* supprime la commande */
    $("#addOrderForm #deleteCommande").click(function(){
        var idOrder = $("#addOrderForm input[name='id']").val();
        var listOrderPiecesWithTime = $("#listPiecesOrder tr .btn.times");
        var needConfirm = false;
        var mdp = "";
        
        if(idOrder){            
            var orderCheck = isOrderFactured(idOrder);
            if(orderCheck.factured == "1"){
                $(".modal.alert").slideDown(100).css("display","flex");
                return;
            }
        }
        
        if(idOrder > 0){            
            var deleteOrder = confirm("Etes vous sur de vouloir supprimer la commande ? \n ATTENTION cette operation est irreversible !!!");
            
            if(listOrderPiecesWithTime.length>0){
                needConfirm = true;
                mdp = prompt("Cette commande contient des pieces avec du temps d'enregistré !!!\nQuel est votre mot de passe ?");
            }
            
            if(deleteOrder){
                $.ajax({
                    type: "POST",
                    url: baseUrl+"/admin/formsValidate/ordersFormsValidate.php",
                    data: {idOrderDelete:idOrder,
                           needConfirm: needConfirm,
                           mdp:mdp
                          },
                    dataType: 'json',
                    success: function(data){
                        if(data.error){
                            alert(data.error);
                        }else{
                            console.log(data); 
                            $("#addOrderForm #infoSaveOrder").text("Commande supprimée !");
                            resetForm();
                        }
                    },
                    error: function(res,statut,e){
                        alert("Erreur sur la requete ajax 'suppression de commande' : "+e);
                        console.log(res);
                    }
                });            
            } 
        }
    });
    
    /* affichage dynamique des infos de la commande lors de la recherche ref_tcs */
    $("#addOrderForm #searchOrder").click(function(e){ 
        var ref_tcs = $("#addOrderForm form input[name='ref_tcs']").val(); 
                
        if(ref_tcs){
            console.log("order search : " + ref_tcs);
            $.ajax({
                type: "GET",
                url: baseUrl+"/admin/getDatasAjax/getOrder.php",
                data: {ref_tcs:ref_tcs},
                dataType: 'json',
                success: function(data){
                    console.log(data); 
                    $("#formAddPieces").show(200);
                    if(data.no_order){
                        alert(data.no_order);
                        return;
                    }
                    
                    completePiecesListingRef(data.piecesClient);
                            
                    remplieInfosCommande(data.infos_commande);
                    remplieInfosClient(data.infos_client);
                    remplieListePieces(data.pieces);
                    $("#addOrderForm .nomClient").text(data.infos_client.nom);
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax 'recherche de commande' : "+e);
                    console.log(res);
                }
            });            
        } 
    });
    
    /* affichage dynamique des infos client lors de la selection client */
    $("#addOrderForm form input[name='client_nom_pro'], #addOrderForm form input[name='client_nom_part']").change(function(e){        
        console.log("client change");
        emptyInfosClient(); 
        
        if($(this).is("input[name='client_nom_pro']")){            
            var nameClient = $("#addOrderForm form input[name='client_nom_pro']").val();
            var idClient = $("#addOrderForm form #client_pro option[value='"+nameClient+"']").attr('data-id');
            $("#addOrderForm form input[name='client_nom_part']").val("");
        }else if($(this).is("input[name='client_nom_part']")){ 
            var nameClient = $("#addOrderForm form input[name='client_nom_part']").val();
            var idClient = $("#addOrderForm form #client_part option[value='"+nameClient+"']").attr('data-id');
            $("#addOrderForm form input[name='client_nom_pro']").val("");
        }
        
        if(idClient > 0){
            $.ajax({
                type: "GET",
                url: baseUrl+"/admin/getDatasAjax/getClient.php",
                data: {idClient:idClient},
                dataType: 'json',
                success: function(data){
                    //console.log(data); 
                    remplieInfosClient(data);
                    $("#addOrderForm .nomClient").text(data.nom);
                    if(data.error){
                        alert(data.error)
                    }
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax du formulaire 'ajout de commande' : "+e);
                    console.log(res);
                }
            });            
        } 
    });
    
    
    // changement de statut pour Bl, facture, envoye
    $("#blFacturesPage input[type='checkbox']").change(function(e){
        e.preventDefault();
        var witchBox = $(this).attr('name');
        var statutBox = $(this).is(':checked');
        
        var dataForms = new FormData();
        dataForms.append("idOrder",$(this).parents("form").attr('data-idOrder'));
        dataForms.append("checkBlFact",'true');
        
        if(witchBox == "blChecked"){            
            dataForms.append("blChecked",statutBox);
        }else if(witchBox == "factureChecked"){
            dataForms.append("factureChecked",statutBox);
        }else if(witchBox == "envoyeChecked"){
            dataForms.append("envoyeChecked",statutBox);
        }
        
        for (var pair of dataForms.entries()) {
            console.log(pair[0]+ ', ' + pair[1]); 
        }
                        
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/ordersFormsValidate.php",
            data: dataForms,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(data){
                if(data.error){
                    alert(data.error);
                }else{
                    console.log(data);
                }
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax modification statut BL, facture ou envoyé "+e);
                console.log(res);
            }
        });
    });
    
    //Cloture toutes les pieces de la commande
    $("#commandesPage .clotureAllPieces").click(function(){ 
        var idOrder = $(this).attr("data-orderId");
        var lockedMode = $(this).attr("data-lockedMode");
        
        if(lockedMode == "1"){
            var lockedAll = confirm("Etes vous sur de vouloir tout cloturer ?");            
        }else{
            var lockedAll = confirm("Etes vous sur de vouloir retirer toutes les clotures sur cette commande ?");
        }
        
        if(lockedAll == true){
            $.ajax({
                type: "POST",
                url: baseUrl+"/admin/formsValidate/ordersFormsValidate.php",
                data: {idOrder : idOrder,
                      clotureAllPieces : lockedMode},
                dataType: 'json',            
                success: function(data){
                    if(data.error){
                        alert(data.error);
                    }else{
                        console.log(data);
                        //location.reload();
                    }
                },
                error: function(res,statut,e){
                    alert("Erreur sur la requete ajax pour cloturer toutes les pieces de la commande "+idOrder+"..."+e);
                    console.log(res);
                }
            });
        }
    });
    
    //gestion du btn calendrier sur la page listing des commandes
    $("#commandesPage h1 i.fa-list-alt").click(function(e){
        e.stopImmediatePropagation();
        $("#commandesPage h1 #inputChangeMonth").slideToggle(200);
        return false;
    });
    
    $("#commandesPage h1 #inputChangeMonth").focusout(function(){
        let date = $(this).val();
        let year = date.split("-")[0];
        let month = date.split("-")[1];        
        location.href = baseUrl+"/pages/commandes.php?month="+month+"&year="+year;
    });
    
    //gestion du btn calendrier sur la page bl et facture
    $("#blFacturesPage #monthChoice i.fa-list-alt").click(function(e){
        e.stopImmediatePropagation();
        $("#blFacturesPage #inputChangeMonth").slideToggle(200);
        return false;
    });
    
    $("#blFacturesPage #inputChangeMonth").focusout(function(){
        let date = $(this).val();
        let year = date.split("-")[0];
        let month = date.split("-")[1];        
        location.href = baseUrl+"/pages/listingBlFactures.php?month="+month+"&year="+year;
    });
    
    
    //remplie les champs de la commande
    function remplieInfosCommande(infosC){
        $("#addOrderForm form input[name='id']").val(infosC.id);
        $("#addOrderForm form input[name='statut']").val(infosC.statut);
        $("#addOrderForm form input[name='ref_tcs']").val(infosC.ref_tcs);
        $("#addOrderForm form input[name='ref_client']").val(infosC.ref_client);
        $("#addOrderForm form input[name='date_recept']").val(infosC.date_recept);
        $("#addOrderForm form input[name='date_livr']").val(infosC.date_livr);
    }
    
    //remplie les champs du client
    function remplieInfosClient(infosCl){ 
        emptyInfosClient();
        $("#addOrderForm form input[name='client_mail']").val(infosCl.mail);
        $("#addOrderForm form input[name='client_tel']").val(infosCl.tel);
        $("#addOrderForm form textarea[name='client_adresse']").val(infosCl.adresse);        
        
        if(infosCl.type == "pro"){
            $("#addOrderForm form input[name='client_nom_pro']").val(infosCl.nom);
            $("#addOrderForm form input[name='client_nom_part']").val("");
        }else if(infosCl.type == "particulier"){
            $("#addOrderForm form input[name='client_nom_part']").val(infosCl.nom);
            $("#addOrderForm form input[name='client_nom_pro']").val("");
        }
    }
    
    /* complete la liste de toutes les pieces du client selectionné dans le champ de saisi de la ref_piece */
    function completePiecesListingRef(allPieces){
        $("#formAddPieces #pieces_client option").remove();
        $.each(allPieces, function(i,val){
            $("#formAddPieces #pieces_client").append('<option data-id="'+allPieces[i].id+'" value="'+allPieces[i].ref+'">');
        });
    }
    
    
    /* remplie liste des pieces dans la commande */
    function remplieListePieces(infosP){
        $("#listPiecesOrder table tbody tr").remove();
        $.each(infosP,function(i,val){
             $("#listPiecesOrder").show(200);             
             $("#listPiecesOrder table tbody").append(addPieceRowToOrder(val));       
        });
    }
    
    //vide les champs du client
    function emptyInfosClient(){        
        $("#addOrderForm form input[name='client_mail']").val("");
        $("#addOrderForm form input[name='client_tel']").val("");
        $("#addOrderForm form textarea[name='client_adresse']").val("");
    }
    
    
    
    
    
    
});