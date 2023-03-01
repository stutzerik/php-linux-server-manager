<?php

//A látogató IP címének megállapítása
function real_ip()
{
    //Kapcsolat detektálásának módjai
    //Látogató áltanos IP címe:
    $remote = $_SERVER['REMOTE_ADDR'];
    //Amennyiben Proxy-t használ:
    $http_client = $_SERVER['HTTP_CLIENT_IP'];
    $shared = $_SERVER['HTTP_X_FORWARDED_FOR'];
    
    //IP cím megállapítása CloudFlare kapcsolat esetén
    $cf_visitor = $_SERVER["HTTP_CF_CONNECTING_IP"];

    if (isset($cf_visitor)) 
    {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    //Amennyiben a látogató Proxy-t használ
    if(filter_var($http_client, FILTER_VALIDATE_IP))
    {
        $ip_address = $http_client;
    }

    elseif(filter_var($shared, FILTER_VALIDATE_IP))
    {
        $ip_address = $shared;
    }
    //Ha nem használ módosított kapcsolatot, alapértelmezési szerint a REMOTE_ADDR függvényt használja
    else
    {
        $ip_address = $remote;
    }

    return $ip_address;
    
}