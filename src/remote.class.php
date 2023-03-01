<?php

//Távoli szerverek kezelése SSH2-vel
class RemoteManager
{

    //Szerver pillanatnyi státuszának ellenőrzése
    public static function status($ip_address, $ssh_port)
    {

        //SSH kapcsolat létrehozása
        $ssh2_connection = ssh2_connect($ip_address, $ssh_port);

        //A kapcsolat ellenőrzése
        if (!$ssh2_connection) 
        {
            $status = "offline";
        } 
        else
        {
            $status = "online";
        }

        //String érték visszaadása
        return $status;
    }

    //Csatlakozás és parancs futtatása
    public static function ssh_connect($ip_address, $ssh_port, $hash_file, $command)
    {

        //.key fájl vissza meghívása, tartalmának visszafejtése
        $hash = file_get_contents("/var/www/ssh_keys/{$hash_file}.key");
        $password = base64_decode($hash);

        //SSH kapcsolat létrehozása
        $ssh2_connection = ssh2_connect($ip_address, $ssh_port);

        if (!$ssh2_connection) 
        {
            return 0;
        } 
        else
        {
            //Bejelentkezés a szerverre
            ssh2_auth_password($ssh2_connection, 'root', $password);

            //Parancs lefuttatása
            $stream = ssh2_exec($ssh2_connection, $command);
            stream_set_blocking($stream, true);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);

            return htmlspecialchars(stream_get_contents($stream_out));        

        }
    }

    //Kapcsolat lezárása
    public static function ssh_close()
    {
        $ssh2_connection = null; 
        unset($ssh2_connection);
    }
}

?>