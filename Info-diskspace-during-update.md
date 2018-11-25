# Altes System:

    $ df
    Filesystem                Size      Used Available Use% Mounted on
    ubi0:rootfs             107.1M     80.3M     26.8M  75% /
    devtmpfs                108.7M         0    108.7M   0% /dev
    tmpfs                   116.9M         0    116.9M   0% /dev/shm
    tmpfs                   116.9M     57.4M     59.5M  49% /tmp
    tmpfs                   116.9M    124.0K    116.8M   0% /run
    none                    116.9M     13.4M    103.6M  11% /tmp/tmproot

# Kurz nach dem Umschalten auf RAM System:

    $ df
    Filesystem                Size      Used Available Use% Mounted on
    ubi0:rootfs             107.1M     25.1M     82.0M  23% /oldroot
    tmpfs                   116.9M     57.4M     59.5M  49% /tmp
    none                    116.9M     13.4M    103.5M  11% /

# Minimum frei während der Installation:

    $ df
    Filesystem                Size      Used Available Use% Mounted on
    ubi0:rootfs             107.1M    103.7M      3.4M  97% /oldroot
    tmpfs                   116.9M     57.4M     59.5M  49% /tmp
    none                    116.9M     13.4M    103.5M  11% /

Da zwischen den df Aufrufen etwa 1s liegt und es sich hier sehr schnell verändert, könnte der Rest auch deutlich kleiner als 3.4M sein.

# Nach Reboot

Während dem Update verwendete Dateien des alten Systems (busybox + screen + firmware update) wurden gelöscht.

    $ df
    Filesystem                Size      Used Available Use% Mounted on
    ubi0:rootfs             107.1M     80.4M     26.6M  75% /
    devtmpfs                108.7M         0    108.7M   0% /dev
    tmpfs                   116.9M         0    116.9M   0% /dev/shm
    tmpfs                   116.9M    148.0K    116.8M   0% /tmp
    tmpfs                   116.9M    128.0K    116.8M   0% /run

# Fazit

Während des Updates ist das Filesystem kurzzeitig nahezu voll.
Bei weiteren Systemerweiterungen ist äußerste Vorsicht geboten!