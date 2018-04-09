### REGISTER sudo configuration
### Do not use as stand alone

[ -z "$modei" ] && { echo "\$modei variable not set. Exiting..."; exit 1;}
[ -z "$user" ] && { echo "\$user variable not set. Exiting..." ; exit 1;}
#sudo -h 1>/dev/null 2>&1 || { echo "Sudo is not installed. Please install it and retry. Exiting" && exit 1 ;}
if [ "$UID" != 0 ] ;then
	echo "** Error Not loged in as root. Things Will Fail.. ";
fi
[ ! -w /tmp/ ] && echo "* Error: /tmp/ not writable .Exiting" && exit 1
[ ! -w /etc/sudoers ] && echo "* Error: /etc/sudoers not writable. Are you root? Exiting" && exit 1

[ "$modei" = "uninstall" ] && modei=1
[ "$modei" = "install" ] && modei=0
[ $modei -lt 0 -o $modei -gt 1 ] && { echo "Invalid mode speicified. Exiting..."; exit 1;}

# Check user existance 
if [ $modei -eq 0 ]; then
USR=$(grep ^$user: /etc/passwd | awk 'BEGIN{FS=":"}; {print $1}')
if [ "$USR" != "$user" ] ;then
	echo -n "User $user does not exist. Create? (y/n)."
	read -n 1 rep
	echo 
	if [ "$rep" = "y" -o "$rep" = "Y" ] ; then
		HOM="/home/$user"
		useradd -d "$HOM" $user || { echo "Error: Cannot Create user $user. Exiting" && exit 1 ;}
		if [ ! -d "$HOM" ] ; then
			# if dir doed not exist
			mkdir "$HOM" || { echo "Error: Can not create home directory" && exit 1 ;}
			chown $user "$HOM" || echo "Can not change home dir ownerwhip"
		fi
                echo "Please give the new password for $user."
                echo "Note: This could be a temporary password. After successful registration you can lock the password (passwd -l $user)."
                rez="y"
                while [ "$rez" = "y" -o "$rez" = "Y" ] ; do
                    rez="n"
                    err="0"
                    passwd $user 
                    if [ $? != 0 ] ;then
                        echo "Could not setup a password for $user. Try again? (y/n)"
                        read -n 1 rez
                        err="1"
                    fi
                done
                if [ "$err" = "1" ] ; then
                     echo "Could not setup a password for $user on the remote host. Set it up manually (passwd $user) and rerun the script. Accound will be disabled. Installation procedure *will* stall later." 
                    exit 1
                fi
		echo "Created user $user"
	else
		echo "Not creating user. Exiting..."
		exit 1
	fi # yes
fi # if not exists user
fi # modei 0

#strip sudo data
cp /etc/sudoers /tmp/ 
if (grep -q WIFIADMIN /etc/sudoers) ;then
	grep -v WIFIADMIN /etc/sudoers > /tmp/sudoers
fi

if [ $modei -eq 0 ] ;then
	echo -ne "\nCmnd_Alias      WIFIADMIN = /sbin/iwconfig, /sbin/ifconfig, /sbin/iwlist, /sbin/iwpriv,  /sbin/route, /sbin/dhclient\n" \
		"${user}	 ALL=(ALL) NOPASSWD: WIFIADMIN " >> /tmp/sudoers
	echo "Adding wifiadmin sudo commands for $user"
fi
visudo -cf /tmp/sudoers
if [ $? != 0 ] ;then
	echo "** Error updating /etc/sudoers.Temp file @ /tmp/sudoers Read the README and try to fix it up. Giving up"
	exit 1;
fi
mv /tmp/sudoers /etc/
if [ $? = 0 ]; then
	echo -n "Succesfully " ; [ $modei -eq 0 ] && echo "registered"; [ $modei -eq 1 ] && echo "unregistered"
else
	exit 1
fi

exit 0
