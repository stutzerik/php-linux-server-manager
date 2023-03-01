<?php

//KVM virtualizációs osztály
//Lehetővé teszi a virtuális gépek kezelését

class KVM
{

    //Létrehozás
    //ISO fájlok alapértelmezett helye: /var/lib/libvirt/images
    public static function createVM($name, $vcpu, $memory, $disk_size, $iso)
    {
        $cmd = "/usr/bin/sudo virt-install --connect=qemu:///system --name={$name} --vcpus={$vcpus} --memory={$memory} --disk size={$disk_size} --cdrom=/var/lib/libvirt/images/{$iso} --network network=default --graphics vnc --noautoconsole > /dev/null 2>&1
        /usr/bin/sudo virsh start {$name} > /dev/null 2>&1";

        return shell_exec($cmd);
    }

    //Státusz lekérdezés
    public static function statusVM($name)
    {
        $cmd = "/usr/bin/sudo virsh domstate {$name}";
        return trim(shell_exec($cmd));
    }

    //Törlés
    public static function removeVM($name)
    {
        $cmd = "/usr/bin/sudo virsh shutdown {$name} > /dev/null 2>&1
        /usr/bin/sudo virsh destroy --domain {$name} > /dev/null 2>&1
        /usr/bin/sudo virsh undefine --domain {$name} > /dev/null 2>&1
        /usr/bin/sudo rm -rf /var/lib/libvirt/images/{$name}.qcow2 > /dev/null 2>&1";
        return shell_exec($cmd);    
    }

    //VM indítása
    public static function startVM($name)
    {
        $cmd = "/usr/bin/sudo virsh start {$name} > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //VM leállítása
    public static function stopVM($name)
    {
        $cmd = "/usr/bin/sudo virsh destroy {$name} > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //VM újraindítása
    public static function rebootVM($name)
    {
        $cmd = "/usr/bin/sudo virsh reboot {$name} > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //VM felfüggesztése
    public static function suspendVM($name)
    {
        $cmd = "/usr/bin/sudo virsh suspend {$name} > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //VM felfüggesztésének visszaállítása
    public static function resumeVM($name)
    {
        $cmd = "/usr/bin/sudo virsh resume {$name} > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //Biztonsági készítés a gépről
    public static function snapshotVM($name, $snapshot_name)
    {
        $cmd = "/usr/bin/sudo virsh snapshot-create-as --domain {$name} --name {$snapshot_name} > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //Biztonsági mentés törlése
    public static function delete_snapshot($name, $snapshot_name)
    {
        $cmd = "/usr/bin/sudo virsh snapshot-delete --domain {$name} --snapshotname {$snapshot_name} > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //Biztonsági mentés visszaállítása
    public static function restoreVM($name, $snapshot_name)
    {
        $cmd = "/usr/bin/sudo virsh shutdown --domain {$name} > /dev/null 2>&1
        /usr/bin/sudo virsh snapshot-revert --domain {$name} --snapshotname {$snapshot_name} --running > /dev/null 2>&1";
        return shell_exec($cmd);
    }

    //Memória bővítése
    public static function scaleVM($name, $memory)
    {
        $cmd = "/usr/bin/sudo virsh setmem {$name} {$memory} --config --live";
        return shell_exec($cmd);
    }
}

?>