<?php

//Osztály a Linux rendszer alapvető funkcióinak ellátására
class System
{

    //CPU használat
    public static function cpu_usage($usage)
    {
        $usage = shell_exec("/usr/bin/sudo grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'");
        print round($usage);
        return $usage;
    }

    //Memória használat
    public static function mem_usage($usage)
    {
        $usage = shell_exec("/usr/bin/sudo free | grep Mem | awk '{print $3/$2 * 100.0}'");
        print round($usage, 2);
        return $usage;
    }

    //Merevlemez használat
    public static function disk_usage($usage)
    {
        $usage = shell_exec("/usr/bin/sudo df --output=pcent / | tr -dc '0-9'");
        print round($usage, 2);
        return $usage; 
    }

    //Root jelszó
    public static function change_root_pwd($rootpwd)
    {
        $cmd = shell_exec("/usr/bin/sudo echo -e '{$rootpwd}\n{$rootpwd}' | passwd root > /dev/null 2>&1");
        return $cmd;
    }

    //Hostname váltás 
    public static function change_hostname($hostname)
    {
        $cmd = shell_exec("/usr/bin/sudo hostnamectl set-hostname {$hostname} > /dev/null 2>&1");
        return $cmd;
    }

    //Jelenlegi hostname
    public static function current_hostname()
    {
        $cmd = shell_exec("hostname");
        print $cmd;
        return $cmd;
    }

    //Szerver újraindítása
    public static function reboot()
    {
        $cmd = shell_exec('/usr/bin/sudo reboot > /dev/null 2>&1');
        return $cmd;
    }

}

?>