<?php 

use Dapphp\TorUtils\TorDNSEL;

//Alkalmazás tűzfal
class WebAppFirewall
{

    //Opcionálisan proxy és VPN blokkolása
    //Ha a bool = true, a funkció meghívásra 
    public static function anti_proxy($enabled)
    {
        if($enabled == TRUE)
        {
            //Tiltott kapcsolatok listája
            $proxy_list = array(
                'Proxy-Connection',
                'PROXY-AGENT',
                'X-PROXY-ID',
                'MT-PROXY-ID',
                'HTTP_PROXY_CONNECTION',
                'HTTP_X_FORWARDED_FOR',  
                'HTTP_FORWARDED_FOR_IP',
                'X-TINYPROXY',
                'X_FORWARDED_FOR',
                'FORWARDED_FOR',
                'X_FORWARDED',
                'FORWARDED',
                'FORWARDED_FOR_IP',
            );
        
            //Megvizsgálja, hogy a tömb tartalmazza-e a tiltott kapcsolat formáját
            foreach($proxy_list as $connection)
            {
                if (isset($_SERVER[$connection]) && !empty($_SERVER[$connection])) 
                {
                    //Átirányítás és kiiratás
                    header('Location: /blocked?msg=proxy');
                } 
            } 
        } 
    }

    //Tor kapcsolat blokkolása
    //TorUtils framework a megvalósításhoz
    public static function anti_tor()
    {

        require '../vendor/dapphp/torutils/src/TorDNSEL.php';
        try 
        {
            if (TorDNSEL::isTor($_SERVER['REMOTE_ADDR'])) 
            {
                //Átirányítás
                header('Location: /blocked?msg=tor');
            }
        } 
        catch (\Exception $error) 
        {
            print("DNSEL hiba lépett fel: " . $error->getMessage());
        }
        
    } 

}