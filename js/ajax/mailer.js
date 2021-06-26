var mailer = $(".modal.mailer");

//Cacher la modal d'envoi des mails
$(".modal.mailer #closeModal").click(function (e) {
    closeModalMailer();
});

//Cacher la modal d'envoi des mails
$(".modal.mailer form").submit(function (e) {
    e.preventDefault();
    sendMails();
    closeModalMailer();
});

//rempli la modal mailer avec  les infos client
function fillMailerInfos(mailsClient, refClient, nomClient) {
    closeModalMailer();
    if (mailsClient) {        
        $(mailer).find("form input[name='refClient']").attr("value", refClient);
        $(mailer).find("form input[name='nomClient']").attr("value", nomClient);
        for (var i = 0; i < mailsClient.length; i++) {
            var checked = "";
            if (i == 0) {
                var checked = "checked";
            }
            var li = '<li><input type="checkbox" id="mail' + i + '" name="mailToSend" ' + checked + ' value="' + mailsClient[i] + '"><label for="mail' + i + '"> ' + mailsClient[i] + '</label></li>';
            $(mailer).find("ul").append(li);
        }
        $(".modal.mailer").show(200);
    }
}

function closeModalMailer(){    
    $(".modal.mailer").hide(200);
    $(".modal.mailer form")[0].reset();
    $(mailer).find("ul").empty();
}

//envoi le(s) mail
function sendMails() {
    var allMailsSelected = [];
    var nomClient = $(mailer).find("form input[name='nomClient']").attr("value");
    var refClient = $(mailer).find("form input[name='refClient']").attr("value");
    
    $(mailer).find("form input[name='mailToSend']:checked").map(function() {
        allMailsSelected.push($(this).val());
    });
    
    if(allMailsSelected.length>0){
        $.ajax({
            type: "POST",
            url: baseUrl+"/admin/formsValidate/formMailer.php",
            data: { sendMail:true,
                    allMailsSelected:allMailsSelected,
                    nomClient:nomClient,
                    refClient:refClient
                  },
            dataType: 'json',            
            success: function(dataMail){
                console.log(dataMail);

            },
            error: function(res,statut,e){
                alert("Erreur sur la requete ajax de l'envoie des emails "+e);
                console.log(res);
            }
        });
    }
}