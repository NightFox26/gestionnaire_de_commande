<?php
$ipAdmins = ['192.168.1.51','192.168.1.146','127.0.0.1','::1'];
$ipOuvriers = ['192.168.1.21','192.168.1.128'];
$baseUrl = 'tcs26/';
$root = 'http://'.$baseUrl;

$thisYear = date('Y');
$thisMonth = date('m');
$thisDay = date('d');

$mdpJs = "695a26750";

function getAutoloader(){
    /* Auto loader de class */    
    spl_autoload_register(function ($class_name) {
        global $baseUrl;
        include __DIR__."/../class/".$class_name . '.php';
    });
}

function monthMoins($month,$year){
    if($month == 1){
        return "month=12&year=".($year-1);
    }else{
        if($month<11){
            return "month=0".($month-1)."&year=".$year;
        }
        return "month=".($month-1)."&year=".$year;
    }
}

function monthPlus($month,$year){
    if($month == 12){
        return "month=01&year=".($year+1);
    }else{
        if($month<9){
            return "month=0".($month+1)."&year=".$year;
        }
        return "month=".($month+1)."&year=".$year;
    }
}

function monthName($month){
    switch($month){
        case "1":
            return "Janvier";
            break;
        case "2":
            return "Fevrier";
            break;
        case "3":
            return "Mars";
            break;
        case "4":
            return "Avril";
            break;
        case "5":
            return "Mai";
            break;
        case "6":
            return "Juin";
            break;
        case "7":
            return "Juillet";
            break;
        case "8":
            return "Aout";
            break;
        case "9":
            return "Septembre";
            break;
        case "10":
            return "Octobre";
            break;
        case "11":
            return "Novembre";
            break;
        case "12":
            return "Decembre";
            break;
        
    }
}

function get_client_ip_server() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

function isAdmin(){
    global $ipAdmins;
    if(in_array(get_client_ip_server(),$ipAdmins)){
        return true;
    }
    return false;
}

function isOuvrier(){
    global $ipOuvriers;
    if(in_array(get_client_ip_server(),$ipOuvriers)){
        return true;
    }
    return false;
}

function isTrustedIp(){
    global $ipAdmins;
    global $ipOuvriers;
    
    $allIps = array_merge($ipAdmins, $ipOuvriers);
    
    if(in_array(get_client_ip_server(),$allIps)){
        return true;
    }
    return false;
}

function calcTotalTime($alltimes){
    $total = 0;
    foreach($alltimes as $temps):
        $total += $temps->getTemps();
    endforeach;
    return $total;
}

function verifIpAccesIntranet(){
    if(!isTrustedIp()){
        header('Content-Type: text/html; charset=utf-8');
        die("Vous n'êtes pas authorisé a accéder a ce site !!!<br>Pour les droits d'accés merci de voir avec Jean Sebastien !");
        exit();
    }
}


