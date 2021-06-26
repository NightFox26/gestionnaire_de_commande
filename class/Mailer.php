<?php
header('Content-Type: text/html; charset=utf-8');

class Mailer {    
    private $from = "fabrication.tcs26@gmail.com";
    private $confirmTo = "contact.tcs26@gmail.com";
    private $headers;
    private $title = "TCS26 commande terminée";
    private $signature;
    
    
    public function __construct(){
        $passage_ligne = "\n";
        $boundary = "-----=".md5(rand());
        
        $this->headers = "From: \"Tcs26\"<fabrication.tcs26@gmail.com>".$passage_ligne;
        $this->headers.= "Reply-to: \"Tcs26\" <contact.tcs26@gmail.com>".$passage_ligne;
        $this->headers.= "MIME-Version: 1.0".$passage_ligne;
        $this->headers.= "Content-Type: text/html; charset=utf-8".$passage_ligne;
        //$this->headers.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
        $this->signature = $this->signatureMail();
    }
        
    public function sendMail($clientName, $clientMail, $refOrder){
        $msg = "Votre commande ref : ".$refOrder. " est terminée.<br> Cordialement, <br> M. Fossat".$this->signature;
        $msgConfirm = "Le mail de confirmation pour le client -".$clientName."- concernant la commande **".$refOrder."** à bien était envoyé a l'adresse ".$clientMail." !";
        
        $title = '=?UTF-8?B?'.base64_encode($this->title).'?=';
        
        mail($clientMail,$title,$msg,$this->headers);
        mail($this->confirmTo,"Confirmation logiciel TCS26",$msgConfirm,$this->headers);
    }   
    
    private function signatureMail(){
        return '<br>------<br>
        
        <!DOCTYPE html>
        <html>
          <head>

            <meta http-equiv="content-type" content="text/html; charset=utf-8">
            <title></title>
          </head>
          <body bgcolor="#FFFFFF" text="#000000">	
            <section style="display:flex; flex-wrap:wrap ; justify-content: space-between; max-width:750px; border:2px black solid; border-radius:25px 0px 25px 0px; 
                    padding:20px 20px 10px 20px; 
                    background: lightgrey; 
                    background: -webkit-linear-gradient(to bottom right, rgb(68,68,68), lightgrey); 
                    background: -o-linear-gradient(to bottom right, rgb(68,68,68), lightgrey); 
                    background: linear-gradient(to bottom right, rgb(68,68,68), lightgrey);
                    background: -moz-linear-gradient(to bottom right, rgb(68,68,68), lightgrey);	
                    box-shadow:5px 5px 10px black;">	
                <div>
                    <img  style="box-shadow:5px 5px 15px dodgerblue; border-radius:10px;" alt="logo tcs" src="https://www.tcs26.fr/signature%20mail/logo.png" height="90" width="225">
                </div>

                <div style="margin-left:15px; margin-top:10px; ">
                    <address style="font-size:0.9em; color:black;">
                        <span style="color: dodgerblue; font-size: 1.6em; text-shadow:1px 1px 1px black;">Jean-Sébastien Fossat</span><br>
                        125 Rue Marie Curie<br>
                        Z.A. Les Monts du Matin<br>
                        26750 Génissieux 
                    </address>
                </div>

                <div style="margin-left:15px; color:black;">
                    <p>
                    <img alt="tel" style="position:relative; top:-3px; margin-right:5px;"
                        src="https://www.tcs26.fr/signature%20mail/tel.png"
                        width="25" height="25" align="top" width="29">04.75.70.12.96<br/>
                    <img alt="mail" style="position:relative; top:-3px; margin-right:5px;" src="https://www.tcs26.fr/signature%20mail/mail.png" height="25"
                        align="top" width="25">contact.tcs26@gmail.com<br/>
                    <img alt="web" style="position:relative; top:-3px; margin-right:5px;"
                          src="https://www.tcs26.fr/signature%20mail/web.png" height="25"
                          align="top" border="0" vspace="0" width="25"><a
                        style="color: dodgerblue; text-decoration: none;"
                        href="https://www.tcs26.fr">www.tcs26.fr</a>
                    </p>
                </div>		
            </section>

          </body>
        </html>
        ';
    }
}