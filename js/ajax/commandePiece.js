//ajoute une ligne dans la liste des pieces
function addPieceRowToOrder(infosPiece){  
    console.log(infosPiece);
    var urgence = "";
    var infos = "";
    if(infosPiece.urgente == 1){
        urgence = '<i class="fas fa-tachometer-alt" title="Piece urgente"></i>  ';
    }
    if(infosPiece.infos){
        infos = '<i class="fas fa-exclamation-triangle" title="'+infosPiece.infos+'"></i>  ';
    }
    var planLinkParam = 'class="piecePLan"';
    if(infosPiece.plan != null){
        planLinkParam = 'href="'+baseUrl+'plans/'+infosPiece.plan+'" target="_blank" class="piecePLan textBlue"';
    }
    return '<tr data-idPiece="'+infosPiece.id+'" data-uniq="'+infosPiece.uniq+'"><form><td class="refPiece" data-ref="'+infosPiece.ref+'"><a href="'+baseUrl+'pages/piece.php?idPieceCommande='+infosPiece.id+'&idCommande='+infosPiece.idCommande+'" target="_blank" class="pieceRef">  '+urgence+infos+infosPiece.ref+'</a></td><td data-qt="'+infosPiece.qt+'"><input type="number" class="smallInput" value="'+infosPiece.qt+'" name="qt_piece"/> pcs</td><td data-ral="'+infosPiece.ral+'">'+infosPiece.ral+'</td><td data-plan="'+infosPiece.plan+'"><a '+planLinkParam+'><i class="fas fa-file-pdf"></i></a></td><td><button class="btn btn_danger '+infosPiece.temps+'"><i class="fas fa-times"></i></button></td></form></tr>';
}

function emptyFieldRefPiece(){
    $("#addOrderForm form#insertPieceCommande input[name='piece_ref'").val("");
    $("#addOrderForm form#insertPieceCommande input[name='piece_infos'").val("");
    $("#addOrderForm form#insertPieceCommande input[name='piece_plan'").val("");
    $("#addOrderForm form#insertPieceCommande input[name='piece_ral'").val("");
    $("#addOrderForm form#insertPieceCommande input[name='piece_qt'").val("1");
    $("#addOrderForm form#insertPieceCommande")
            .find(".notVisibleExisting")
            .show(200);
}

$(function () {
    
    /* supprime la piece dans la commande coté bdd */
    $("#listPiecesOrder tbody").on("click",".btn_danger",function(){
        var idPiece = $(this).parents("tr")
                        .attr("data-idPiece");
        var idRow = $(this).parents("tr").index();
        var pieceHasTime = $(this).hasClass("times");
        var needConfirm = false;
        var mdp = "";
        
        var idOrder = $("#addOrderForm form#commandeForm").find("input[name='id']").val();
        if(idOrder){            
            var orderCheck = isOrderFactured(idOrder);
            if(orderCheck.factured == "1"){
                $(".modal.alert").slideDown(100).css("display","flex");
                return;
            }
        }
        
        if(pieceHasTime){
            needConfirm = true;
            mdp = prompt("Cette piece a deja du temps d'inscrit !!!\nQuel est votre mot de passe ?");            
        }
                    
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/piecesOrdersFormsValidate.php",
            data: {idPieceOrderDel:idPiece,
                   needConfirm: needConfirm,
                   mdp:mdp
                  },
            dataType: 'json',
            async:false,
            global:false,
            success: function(data){
                if(data.error){
                        alert(data.error);
                }else{
                    $("#listPiecesOrder table tbody tr").eq(idRow).remove();  
                }
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de recherche de la piece dans ajout piece a commande : "+e);
                console.log(res);
            }
        });
    });
    
    
    /* verification si piece existe en bdd */
    function verifPieceExist(refPiece,clientId){
        var temp = null;
        $.ajax({
            type: "GET",
            url: baseUrl+"/admin/getDatasAjax/getPiece.php",
            data: {ref:refPiece,idClient:clientId},
            dataType: 'json',
            async:false,
            global:false,
            success: function(data){
                temp = data;
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de recherche de la piece dans ajout piece a commande : "+e);
                console.log(res);
            }
        });
        return temp;
    };  
    
    /*Cacher les champs de saisi inutiles si l'on selectionne une piece existante dans la liste des ref */
    $("#addOrderForm form#insertPieceCommande input[name='piece_ref'").change(function(){
        var pieceRef = $(this).val();        
        var idPiece = $(this).parent().find("#pieces_client option[value='"+pieceRef+"']").attr('data-id');
        console.log(pieceRef);
        console.log(idPiece);
        
        if(idPiece){
            $("#addOrderForm form#insertPieceCommande #pGarder").prop("checked","true");
            $("#addOrderForm form#insertPieceCommande")
                .find(".notVisibleExisting")
                .hide(200);
        }else{
            $("#addOrderForm form#insertPieceCommande")
                .find(".notVisibleExisting")
                .show(200);
        }
    });
    
    
    /* ajout piece a la liste des pieces commande */
    $("#addOrderForm form#insertPieceCommande").submit(function(e){
        e.preventDefault();
        var idOrder = $("#addOrderForm input[name='id']").val();
        
        //On a idPiece si on selectionne une piece dans la liste des pieces deja existante
        var pieceRef = $(this).find("input[name='piece_ref']").val().replace("'", "\\'"); 
        
        var idPiece = $(this).find("#pieces_client option[value='"+pieceRef+"']").attr('data-id');
                        
        var formDatas = new FormData($(this)[0]);
        formDatas.append('addPiece',true);
        formDatas.append('order_id',idOrder);
        formDatas.append('pieceExistante_id',idPiece);
        
        //pour debug formDatas
        /*
        for (var pair of formDatas.entries()) {
            console.log(pair[0]+ ', ' + pair[1]); 
        }
        */        
        
        if(idOrder){            
            var orderCheck = isOrderFactured(idOrder);
            if(orderCheck.factured == "1"){
                $(".modal.alert").slideDown(100).css("display","flex");
                return;
            }
        }        
        
        emptyFieldRefPiece();
                                    
         $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/piecesOrdersFormsValidate.php",
            data: formDatas,
            dataType: 'json', 
            processData: false,
            contentType: false,
            success: function(dataPiece){
                console.log(dataPiece)
                if(dataPiece.error){
                    alert(dataPiece.error)
                }else{
                    $("#listPiecesOrder").show(300);
                    $("#listPiecesOrder table tbody").append(addPieceRowToOrder(dataPiece));
                } 
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de recherche de la piece dans ajout piece a commande : "+e);
                console.log(res);
            }
        });
    });   
    
    /* change le statut d'une piece dans la page commande */
    $("#commandesPage .statusPieceForm select").change(function(e){
        e.preventDefault();
        
        var idPiece = $(this).parents("li").attr("data-idPiece");
        var idOrder = $(this).parents("tr").attr("data-id");
        var statutPiece = $(this).val();
        
         $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/piecesOrdersFormsValidate.php",
            data: { changeStatut:true,
                    statutPiece:statutPiece,
                    idPiece:idPiece,
                    idOrder:idOrder
                  },
            dataType: 'json',            
            success: function(dataPiece){
                console.log(dataPiece);
                if(dataPiece.statutCommande == "terminé"){
                    fillMailerInfos(dataPiece.mailsClient,dataPiece.refClient,dataPiece.nomClient);
                }
                if(dataPiece.error){
                    alert(dataPiece.error);
                }
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de changement de statut de la piece : "+e);
                console.log(res);
            }
        });        
    });
    
        
    /* change la quantité d'une piece dans la liste des pieces d'une commande */     $("#listPiecesOrder").on("change","input[name='qt_piece']",function(e){
        e.preventDefault();
        var newQt = $(this).val();
        var idPiece = $(this).parents("tr").attr("data-idpiece");
        var idOrder = $("#addOrderForm input[name='id']").val();
        
        if(idOrder){            
            var orderCheck = isOrderFactured(idOrder);
            if(orderCheck.factured == "1"){
                $(".modal.alert").slideDown(100).css("display","flex");
                return;
            }
        }
        
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/piecesOrdersFormsValidate.php",
            data: { changeQt:true,
                    idPiece:idPiece,
                    idOrder: idOrder,
                    qt:newQt
                  },
            dataType: 'json',            
            success: function(dataPiece){
                console.log(dataPiece);
                if(dataPiece.error){
                    alert(dataPiece.error);
                }else{
                    alert("La quantité a bien été enregistré !")
                }
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax du changement de quantité de la piece : "+e);
                console.log(res);
            }
        });         
    });
    
        
    //click sur un btn urgencePlus
    $("button.urgencePlus").click(function(){
        var idPiece = $(this).parents("li").attr("data-idpiece");
        var urgencePlusVal = $(this).attr("data-urgencePlus"); 
        var urgencePlus = urgencePlusVal == 0? 1:0;
        
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/piecesOrdersFormsValidate.php",
            data: { updateUrgencePlus:true,
                    urgencePlus:urgencePlus,
                    idPieceCom:idPiece
                  },
            dataType: 'text',            
            success: function(dataPiece){
                location.reload();            
            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de l'update de l'urgencePlus sur piece : "+e);
                console.log(res);
            }
        });
    })
    
    
    
    
    
})