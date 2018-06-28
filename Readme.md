
# FT-TXT : 

## Rootfs / Kernel / Apps

Erstellung mit Buildroot, Bootloader "Das U-Boot"

Voraussetzungen:

Sie benötigen zum Erstellen des BSB und des Bootlodaers ein Linux System mit Entwicklungsumgebung. (mit einem Intelk i7 und 16 GB Ram dauert das Übersetzen ca. eine Stunde !)

Das BSP wurde unter ubuntu mate 16.04 erstellt und getestet:

im Script ./Linux-Pakete-Required.sh sind alle notwendigen Pakete (von MSoegtrop getestet) enthalten.
im Script ./Linux-Pakete-Extra.sh sind einige zusätzliche Pakete (von RRussinger empfohlen) enthalten.

[Download ubuntu mate 16.04](http://cdimage.ubuntu.com/ubuntu-mate/releases/16.04.4/release/ubuntu-mate-16.04.4-desktop-amd64.iso) 

zum Installieren der Zusatzpakete:
  ```
  cd ./FT-TXT
  sudo ./Linux-Pakete-Required.sh
  ```
durchführen. Danach sollten Sie ein System zur Verfügung haben mit dem das BSP und der Bootloader wie angegeben übersetzt/erstellt werden kann.

Rootfilesystem
[BuildrootManual](https://buildroot.org/downloads/manual/manual.pdf) 

Allgemeines

Imageerstellung
[kpartx](https://robert.penz.name/73/kpartx-a-tool-for-mounting-partitions-within-an-image-file/) 

## Erstellen des Rootfs, Bootloader und Kernel

1. Erstellen eines Arbeitsverzeichnisses
  z.B 
  ```
  mkdir FT
  ```

2. Wechseln in das Verzeichnis
  ```
  cd FT
  ```

3. Clone des Script und Konfigurationsverzeichnisses
  ```
  git clone https://gogs.psg-bgh.de/fischertechnik/FT-TXT.git
  ```

4. Verzeichnis für Toolchain erstellen
  ```
  sudo mkdir /opt/FT
  sudo chmod a+rw /opt/FT
  ```

5. In Verzeichnis wechseln
  ```
  cd FT-TXT
  ```	

6. Bootloader bauen
  ```
  ./Make-TXT-Bootloader.sh
  ```
  erstellt die Bootloader Binaries in ../u-boot/bin

7. Buildroot klonen, konfigurieren und bauen
  ```
  ./Make-TXT-Buildroot-Clean.sh
  ```
  Damit wird *buildroot* gecloned und der richtige commit eingestellt,
  Patches eingespielt und Hilfsscripte kopiert.
  Anschließend wird buildroot gebaut.
  
  Die Ausgaben sind in 
  `FT-TXT/../buildroot/output/images`
  zu finden.

  Eine inkrementelle rekonfiguration mit inkrementellen bauen kann über das Script
  ```
  ./Make-TXT-Buildroot-Incremental.sh
  ```

8. Update scripts erzeugen
  Update scripts can be used to update the OS on a TXT without using a flash card.
  ```
  ./Make-TXT-UpdateScripts.sh
  ./Sign-Connect-Reader.sh
  ./Sign-TXT-UpdateScripts.sh 1
  ./Sign-TXT-UpdateScripts.sh 2
  ```
  The update scripts and signatures can be found in `FT-TXT/../update`.

## Zusatzscripte

### Make-TXT-Image.sh

Erstellt ein SD Kartenimage mit Bootsektion und Rootfilesystem

im Verzeichnis ./FT-TXT
```
sudo ./Make-TXT-Image.sh
```
es entstehen dann Dateien im übergeordneten Verzeichnis
```
ls -al ../ft*

-rw-r--r-- 1 root root 372244480 Apr  9 11:29 ../ft-TXT_Build_266.img
-rw-r--r-- 1 root root 106355379 Apr  9 11:29 ../ft-TXT_Build_266.img.zip

```
Dieses Image kann dan mit dem dd Befehl auf eine SD Karte kopiert werden.
```
sudo dd if=../ft-TXT_Build_266.img of=/dev/mmcblk0 bs=16M;sync
```
Wenn das host Linux nur standard disk devices kennt, kann man alternativ den Befehl
```
sudo ./Copy-TXT-Image-to-SD.sh /dev/sdX
```
verwenden. Dieses Script prüft ob das Zielgerät eine für SD Karten übliche Größe hat (die exakte größe muss ggf. in das Script eingetragen werden). Dadurch wird vermieden, dass man durch einen Tippfehler die Systemplatte löscht.

### Map-TXT-Image.sh

mittels MapImage.sh kann das erstellte Image "gemapped" werden.

```
sudo ./Map-TXT-Image.sh

>>> ../ft-TXT_Build_266.img <<<
>>>>>>>>>>>>>>>>>>
/dev/mapper/loop0p1
/dev/mapper/loop0p2
>>>>>>>>>>>>>>>>>>
./MapImage.sh: 17: ./MapImage.sh: usleep: not found
add map loop0p1 (253:0): 0 266240 linear 7:0 2048
add map loop0p2 (253:1): 0 458752 linear 7:0 268288
MAP IMAGE:root@NB-RU:/hdd2/ru/FT/BUILD/FT-TXT# 


```
Sie befinden sich dann in einer Shell 
das image bzw die Dateien) finden Sie unter

```
/tmp/boot
/tmp/rootfs
```
```
df -h

Filesystem           Size  Used Avail Use% Mounted on
udev                 7.8G     0  7.8G   0% /dev
tmpfs                1.6G   18M  1.6G   2% /run
/dev/sda1            466G  406G   37G  92% /
tmpfs                7.8G   17M  7.8G   1% /dev/shm
tmpfs                5.0M  4.0K  5.0M   1% /run/lock
tmpfs                7.8G     0  7.8G   0% /sys/fs/cgroup
/dev/sdb1            459G  401G   35G  93% /hdd2
tmpfs                1.6G   32K  1.6G   1% /run/user/1000
/dev/mapper/loop0p1  128M   70M   59M  55% /tmp/boot
/dev/mapper/loop0p2  213M  102M  100M  51% /tmp/rootfs

```
zum Verlassen:
```
exit
```

