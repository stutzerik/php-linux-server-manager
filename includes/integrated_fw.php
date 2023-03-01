<?php

//Tűzfal beállítások lekérdezése
$enabled = $db->query('SELECT status FROM firewall WHERE id = ?', 
array('1'))->fetchArray();
$fw_enabled = $enabled['status'];

//Ha az értéke 1 akkor meghívja a tűzfal osztályát
if($fw_enabled == 1)
{
    $waf = new WebAppFirewall();
    $waf->anti_proxy($enabled == TRUE);   
}

?>