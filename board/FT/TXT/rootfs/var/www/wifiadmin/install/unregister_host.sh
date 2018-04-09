#!/bin/bash

SCR_SSH="./unregister_mid_ssh.sh"
SCR_SUDO="./register_mid_sudo_templ.sh"

### Unregister a host from local server
echo -e "Script to unregister the server (where wifiadmin runs on) with\n" \
"	the router(s). This will remove sudo access from remote user and\n" \
"	ssh no password access from local apache user to remote@router_url\n" \
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


#### SKip Local
SSH="ssh root@${host} "
if [ "$host" = "localhost" ] ; then
	echo "local host actions"
	SSH="su -c"
fi

echo -e "\n Update Router (${host}) sudo configuration. You will be asked for ${host} 's root password for this"

$SSH "user=$user; modei=uninstall; $(cat $SCR_SUDO)"

if [ $? -ne 0 ] ;then
	echo "* Error deconfigurting sudo for Router $host. Exiting.."
	exit 1
fi

if [ "$SSH" = "su -c" ] ; then
	exit 0
fi

### The rest are for ssh registrations]
[ ! -x "$SCR_SSH" ] && chmod a+x "$SCR_SSH"
[ ! -x "$SCR_SSH" ] && { echo "SSH subscript $SCR_SSH is not in path or is not executable.chmod u+x $SCR_SSH Exiting." ; exit 1;}

### Apache user necesary config for ssh access

### Now login as apache user and perform tasks
echo "Logging in as apache user $APAU to perform additional tasks"
#if [ $( who -m | awk '{print $1}') != "$APAU" ] ; then
su $APAU -c "$SCR_SSH uninstall $user $host"
#fi
if [ $? -ne 0 ] ;then
	echo "* Error UnRegistering Router $host via ssh. Exiting.."
	exit 1
fi
chmod a-x "$SCR_SSH"



