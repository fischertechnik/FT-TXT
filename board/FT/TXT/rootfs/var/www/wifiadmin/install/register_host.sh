#!/bin/bash
# Give APAU , user, host
# Host can be localhost

SCR_SSH="./register_mid_ssh.sh"
SCR_SUDO="./register_mid_sudo_templ.sh"

echo -e "Script to register the server (where wifiadmin runs on) with\n" \
"	the router(s). Register means provide host-based ssh access\n" \
"	You have to provide the *correct* apache user and the router_url\n"\
"	as well as username. router_url can be localhost for this host\n"\
"	Then ssh will not be used so username is not required.\n\n"\
"	Usage $0 apache_user router_url [username] \n"

[ -z "$1" ] || [ -z "$2" ] && { echo "Missing Parameters" && exit 1 ;}

[ ! -r "$SCR_SUDO" ] && { echo "SUDO subscrpipt $SCR_SUDO is not in path. Execute script from install dir Exiting."; exit 1;}


APAU="$1"
host="$2"
user="$APAU"
if [ -z "$3" ]; then
	echo "Unspecified Remote user set to $user"
else
	user="$3"
fi

apusr=$(grep ^$APAU: /etc/passwd | awk 'BEGIN{FS=":"}; {print $1}')
if [ "$apusr" != "$APAU" ] ;then
	echo "Specified apache user does not exist! Exiting."
	exit 1
fi

#### SKip Local
SSH="ssh root@${host} "
if [ "$host" = "localhost" ] ; then
	echo "Skipping Steps 2,3 for local host"
	SSH="su -c"
fi

echo -e "\n   [Step 1] Update Router (${host}) sudo configuration. You will be asked for ${host} 's root password for this"

$SSH "user=$user; modei=install; $(cat $SCR_SUDO)"

if [ $? -ne 0 ] ;then
	echo "* Error configurting sudo for Router $host. Exiting.."
	exit 1
fi

if [ "$SSH" = "su -c" ] ; then
	exit 0
fi

### The rest are for ssh registrations]
[ ! -x "$SCR_SSH" ] && chmod a+x "$SCR_SSH"
[ ! -x "$SCR_SSH" ] && { echo "SSH subscript $SCR_SSH is not in path or is not executable.chmod u+x $SCR_SSH Exiting." ; exit 1;}

### Apache user necesary config for ssh access
HOM=$(grep ^$APAU: /etc/passwd | awk 'BEGIN{FS=":"}; {print $6}')
if [ -z "$HOM" ] ; then
	echo "   [Step 0] Home directory creation for apache user $APAU"
	HOM="/home/$APAU"
	usermod -d "$HOM" $APAU || {
		echo "Can not update home dir fot user $APAU. Are you root?. Exiting" && exit 1 ;}
fi
if [ ! -d "$HOM" ] ; then
	# if dir does not exist
	mkdir "$HOM" || { echo "Can not create home directory. Are you root?" && exit 1 ;}
	chown $APAU "$HOM" || echo "Can not give ownership to apache user"
fi

echo "*Security Notice: Apache user $APAU home directory is $HOM. Make sure this is *NOT* your web path"
echo "Continue?(y/n)"
read -n 1 rep
echo 
if [ "$rep" != "y" ] ; then
	echo "Exiting.."
	exit 1;
fi
### Now login as apache user and perform tasks
echo "Logging in as apache user (on the local machine) $APAU to perform additional tasks"
#if [ $( who -m | awk '{print $1}') != "$APAU" ] ; then
su $APAU -c "$SCR_SSH install $user $host"
#fi
if [ $? -ne 0 ] ;then
	echo "* Error Registering Router $host via ssh. Exiting.."
	exit 1
fi
chmod a-x "$SCR_SSH"
exit 0



