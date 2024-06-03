
 █████╗ ██████╗ ██╗    ██████╗  ██████╗ ████████╗███████╗███████╗██████╗  ██████╗ 
██╔══██╗██╔══██╗██║    ██╔══██╗██╔═══██╗╚══██╔══╝╚══███╔╝██╔════╝██╔══██╗██╔═══██╗
███████║██████╔╝██║    ██████╔╝██║   ██║   ██║     ███╔╝ █████╗  ██████╔╝██║   ██║
██╔══██║██╔═══╝ ██║    ██╔══██╗██║   ██║   ██║    ███╔╝  ██╔══╝  ██╔══██╗██║   ██║
██║  ██║██║     ██║    ██████╔╝╚██████╔╝   ██║   ███████╗███████╗██║  ██║╚██████╔╝
╚═╝  ╚═╝╚═╝     ╚═╝    ╚═════╝  ╚═════╝    ╚═╝   ╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ 
                                                                                  
## Servidor 
Ubuntu 20.04.6 LTS (GNU/Linux 5.4.0 x86_64)

## Instalar y Prender Apache 

root@tcargobr:~# sudo apt-get update
root@tcargobr:~# sudo apt-get upgrade
root@tcargobr:~# sudo apt-get install apache2
root@tcargobr:~# sudo systemctl start apache2

## Instalar PHP (8.2 o mayor)

root@tcargobr:~# sudo apt update
root@tcargobr:~# sudo add-apt-repository ppa:ondrej/php -y

### IF sudo: add-apt-repository: command not found

    root@tcargobr:~# sudo apt install ca-certificates apt-transport-https software-properties-common lsb-release -y
    root@tcargobr:~# sudo add-apt-repository ppa:ondrej/php -y

### END IF ###

root@tcargobr:~# sudo apt update
root@tcargobr:~# sudo apt upgrade

root@tcargobr:~# sudo apt install php8.2 libapache2-mod-php8.2

root@tcargobr:~# sudo systemctl restart apache2

root@tcargobr:~# php -v

### FEEDBACK SUCCESS ###

PHP 8.2.15 (cli) (built: Jan 20 2024 14:16:39) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.15, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.15, Copyright (c), by Zend Technologies

###

root@tcargobr:~# sudo apt install curl git unzip -y

root@tcargobr:~# php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
root@tcargobr:~# php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
Installer verified
root@tcargobr:~# php composer-setup.php
All settings correct for using Composer
Downloading...

Composer (version 2.7.1) successfully installed to: /root/composer.phar
Use it: php composer.phar

root@tcargobr:~# php -r "unlink('composer-setup.php');"

root@tcargobr:~# composer
:::::: IF :::::::::: -bash: composer: command not found
root@tcargobr:~# curl -sS https://getcomposer.org/installer -o composer-setup.php
root@tcargobr:~# sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
All settings correct for using Composer
Downloading...

Composer (version 2.7.1) successfully installed to: /usr/local/bin/composer
Use it: php /usr/local/bin/composer
:::::::: ENDIF :::::::::

root@tcargobr:~# composer


::::: FEEDBACK SUCCESS::::::

Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? yes
   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 2.7.1 2024-02-09 15:26:28

Usage:
  command [options] [arguments]

Options:
  -h, --help                     Display help for the given command. When no command is given display help for the list command
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi|--no-ansi           Force (or disable --no-ansi) ANSI output
  -n, --no-interaction           Do not ask any interactive question
      --profile                  Display timing and memory usage information
      --no-plugins               Whether to disable plugins.
      --no-scripts               Skips the execution of all scripts defined in composer.json file.
  -d, --working-dir=WORKING-DIR  If specified, use the given directory as working directory.
      --no-cache                 Prevent use of the cache
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  about                Shows a short information about Composer
  archive              Creates an archive of this composer package
  audit                Checks for security vulnerability advisories for installed packages
  browse               [home] Opens the package's repository URL or homepage in your browser
  bump                 Increases the lower limit of your composer.json requirements to the currently installed versions
  check-platform-reqs  Check that platform requirements are satisfied
  clear-cache          [clearcache|cc] Clears composer's internal package cache
  completion           Dump the shell completion script
  config               Sets config options
  create-project       Creates new project from a package into given directory
  depends              [why] Shows which packages cause the given package to be installed
  diagnose             Diagnoses the system to identify common errors
  dump-autoload        [dumpautoload] Dumps the autoloader
  exec                 Executes a vendored binary/script
  fund                 Discover how to help fund the maintenance of your dependencies
  global               Allows running commands in the global composer dir ($COMPOSER_HOME)
  help                 Display help for a command
  init                 Creates a basic composer.json file in current directory
  install              [i] Installs the project dependencies from the composer.lock file if present, or falls back on the composer.json
  licenses             Shows information about licenses of dependencies
  list                 List commands
  outdated             Shows a list of installed packages that have updates available, including their latest version
  prohibits            [why-not] Shows which packages prevent the given package from being installed
  reinstall            Uninstalls and reinstalls the given package names
  remove               [rm] Removes a package from the require or require-dev
  require              [r] Adds required packages to your composer.json and installs them
  run-script           [run] Runs the scripts defined in composer.json
  search               Searches for packages
  self-update          [selfupdate] Updates composer.phar to the latest version
  show                 [info] Shows information about packages
  status               Shows a list of locally modified packages
  suggests             Shows package suggestions
  update               [u|upgrade] Updates your dependencies to the latest version according to composer.json, and updates the composer.lock file
  validate             Validates a composer.json and composer.lock

:::::::::::::::::::::::::::::::

root@tcargobr:~# sudo apt install npm

root@tcargobr:~# cd /var/www


## Duplicar Repositorio de API

root@tcargobr:/var/www# git clone --bare https://github.com/BOTzerorepo/api-BTZ.git
Cloning into bare repository 'api-BTZ.git'...
remote: Enumerating objects: 1743, done.
remote: Counting objects: 100% (653/653), done.
remote: Compressing objects: 100% (357/357), done.
remote: Total 1743 (delta 349), reused 513 (delta 294), pack-reused 1090 
Receiving objects: 100% (1743/1743), 5.75 MiB | 3.94 MiB/s, done.
Resolving deltas: 100% (1068/1068), done.

root@tcargobr:/var/www# cd api-BTZ.git/

root@tcargobr:/var/www/api-BTZ.git# git push --mirror https://github.com/BOTzerorepo/api-btz-tcargo.git

Username for 'https://github.com': pachimanok
Password for 'https://pachimanok@github.com': 
Enumerating objects: 1743, done.
Counting objects: 100% (1743/1743), done.
Delta compression using up to 2 threads
Compressing objects: 100% (633/633), done.
Writing objects: 100% (1743/1743), 5.75 MiB | 3.24 MiB/s, done.
Total 1743 (delta 1068), reused 1743 (delta 1068)
remote: Resolving deltas: 100% (1068/1068), done.
To https://github.com/BOTzerorepo/api-btz-tcargo.git
 * [new branch]      QA -> QA
 * [new branch]      demoMain -> demoMain
 * [new branch]      juaniok -> juaniok
 * [new branch]      main -> main
 * [new branch]      pachimanok -> pachimanok
 * [new branch]      sandbox -> sandbox

root@tcargobr:/var/www/api-BTZ.git# cd ..

root@tcargobr:/var/www# rm -rf api-BTZ.git/

root@tcargobr:/var/www# ls

html

root@tcargobr:/var/www# git clone https://github.com/BOTzerorepo/api-btz-tcargo.git

Cloning into 'api-btz-tcargo'...
Username for 'https://github.com': pachimanok
Password for 'https://pachimanok@github.com': 

root@tcargobr:/var/www# ls
api-btz-tcargo  html

root@tcargobr:/var/www/api-btz-tcargo# ls
README.md  app  artisan  bootstrap  composer.json  composer.lock  config  database  default.php  default.php.old.php  lang  leeme.txt  mail  package.json  phpunit.xml  prueba.txt  public  resources  routes  storage  tests  webpack.mix.js

root@tcargobr:/var/www/api-btz-tcargo# nano .env

root@tcargobr:/var/www/api-btz-tcargo# composer install

## Versiones a instaler 

root@tcargobr:~# php -v
PHP 8.0.25 (cli) (built: Oct 28 2022 18:02:32) ( NTS )
Copyright (c) The PHP Group
Zend Engine v4.0.25, Copyright (c) Zend Technologies
    with Zend OPcache v8.0.25, Copyright (c), by Zend Technologies

root@tcargobr:~# git --version
git version 2.17.1

root@tcargobr:~# composer
-bash: composer: command not found

root@tcargobr:~# php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
root@tcargobr:~# php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
Installer verified
root@tcargobr:~# php composer-setup.php
All settings correct for using Composer
Downloading...

Composer (version 2.7.1) successfully installed to: /root/composer.phar
Use it: php composer.phar

root@tcargobr:~# php -r "unlink('composer-setup.php');"
root@tcargobr:~# composer 
-bash: composer: command not found
root@tcargobr:~# composer --version
-bash: composer: command not found
root@tcargobr:~# php composer-setup.php
Could not open input file: composer-setup.php
root@tcargobr:~# composer self-update
-bash: composer: command not found
root@tcargobr:~# composer sudo apt update
-bash: composer: command not found
root@tcargobr:~# sudo apt update
Hit:1 http://archive.ubuntu.com/ubuntu bionic InRelease                                                                                                                                                                                                                        
Get:2 http://security.ubuntu.com/ubuntu bionic-security InRelease [88.7 kB]                                                                                                                                                                                                    
Hit:3 http://archive.canonical.com/ubuntu bionic InRelease                                                                                                                                                                           
Get:4 http://ppa.launchpad.net/ondrej/apache2/ubuntu bionic InRelease [20.8 kB]                                                                                                                                                                                            
Get:5 http://archive.ubuntu.com/ubuntu bionic-updates InRelease [88.7 kB]                                                                                                                                                                                                      
Get:6 http://ppa.launchpad.net/ondrej/php/ubuntu bionic InRelease [20.8 kB]                                                                                                                                                                                                    
Get:7 https://dlm.mariadb.com/repo/mariadb-server/10.6/repo/ubuntu bionic InRelease [6265 B]                                                                 
Get:8 http://security.ubuntu.com/ubuntu bionic-security/main amd64 Packages [2717 kB]                                
Get:9 https://nginx.org/packages/mainline/ubuntu bionic InRelease [2862 B]                               
Get:10 https://apt.hestiacp.com bionic InRelease [81.4 kB]                                               
Get:11 http://archive.ubuntu.com/ubuntu bionic-updates/main amd64 Packages [3045 kB]                                 
Get:12 https://dlm.mariadb.com/repo/mariadb-server/10.6/repo/ubuntu bionic/main amd64 Packages [18.0 kB]                                           
Get:13 https://nginx.org/packages/mainline/ubuntu bionic/nginx amd64 Packages [97.4 kB]                                                                   
Get:14 http://security.ubuntu.com/ubuntu bionic-security/main Translation-en [467 kB]                                                     
Get:15 http://security.ubuntu.com/ubuntu bionic-security/restricted amd64 Packages [1317 kB]                                                             
Get:16 https://apt.hestiacp.com bionic/main amd64 Packages [21.8 kB]                                                                                     
Get:17 http://security.ubuntu.com/ubuntu bionic-security/restricted Translation-en [182 kB]                                   
Get:18 http://security.ubuntu.com/ubuntu bionic-security/universe amd64 Packages [1303 kB]
Get:19 http://archive.ubuntu.com/ubuntu bionic-updates/main Translation-en [554 kB]
Get:20 http://archive.ubuntu.com/ubuntu bionic-updates/restricted amd64 Packages [1347 kB]       
Get:21 http://archive.ubuntu.com/ubuntu bionic-updates/restricted Translation-en [187 kB]
Get:22 http://archive.ubuntu.com/ubuntu bionic-updates/universe amd64 Packages [1915 kB]      
Get:23 http://security.ubuntu.com/ubuntu bionic-security/universe Translation-en [308 kB]           
Get:24 http://security.ubuntu.com/ubuntu bionic-security/multiverse amd64 Packages [19.8 kB]        
Get:25 http://security.ubuntu.com/ubuntu bionic-security/multiverse Translation-en [3928 B]         
Get:26 http://archive.ubuntu.com/ubuntu bionic-updates/universe Translation-en [421 kB]             
Fetched 14.2 MB in 8s (1785 kB/s)                                                                                                                                                                                                                                              
Reading package lists... Done
Building dependency tree       
Reading state information... Done
143 packages can be upgraded. Run 'apt list --upgradable' to see them.
root@tcargobr:~# sudo apt install curl php-cli php-mbstring git unzip
Reading package lists... Done
Building dependency tree       
Reading state information... Done
unzip is already the newest version (6.0-21ubuntu1.2).
Suggested packages:
  git-daemon-run | git-daemon-sysvinit git-doc git-el git-email git-gui gitk gitweb git-cvs git-mediawiki git-svn
The following NEW packages will be installed:
  php-cli php-mbstring
The following packages will be upgraded:
  curl git libcurl4
3 upgraded, 2 newly installed, 0 to remove and 140 not upgraded.
Need to get 4374 kB of archives.
After this operation, 313 kB of additional disk space will be used.
Get:1 http://archive.ubuntu.com/ubuntu bionic-updates/main amd64 curl amd64 7.58.0-2ubuntu3.24 [159 kB]
Get:2 http://archive.ubuntu.com/ubuntu bionic-updates/main amd64 libcurl4 amd64 7.58.0-2ubuntu3.24 [221 kB]
Get:3 http://archive.ubuntu.com/ubuntu bionic-updates/main amd64 git amd64 1:2.17.1-1ubuntu0.18 [3990 kB]
Get:4 http://archive.ubuntu.com/ubuntu bionic/main amd64 php-cli all 1:7.2+60ubuntu1 [3160 B]
Get:5 http://archive.ubuntu.com/ubuntu bionic/universe amd64 php-mbstring all 1:7.2+60ubuntu1 [2008 B]
Fetched 4374 kB in 4s (1101 kB/s) 
(Reading database ... 44807 files and directories currently installed.)
Preparing to unpack .../curl_7.58.0-2ubuntu3.24_amd64.deb ...
Unpacking curl (7.58.0-2ubuntu3.24) over (7.58.0-2ubuntu3.21) ...
Preparing to unpack .../libcurl4_7.58.0-2ubuntu3.24_amd64.deb ...
Unpacking libcurl4:amd64 (7.58.0-2ubuntu3.24) over (7.58.0-2ubuntu3.21) ...
Preparing to unpack .../git_1%3a2.17.1-1ubuntu0.18_amd64.deb ...
Unpacking git (1:2.17.1-1ubuntu0.18) over (1:2.17.1-1ubuntu0.13) ...
Selecting previously unselected package php-cli.
Preparing to unpack .../php-cli_1%3a7.2+60ubuntu1_all.deb ...
Unpacking php-cli (1:7.2+60ubuntu1) ...
Selecting previously unselected package php-mbstring.
Preparing to unpack .../php-mbstring_1%3a7.2+60ubuntu1_all.deb ...
Unpacking php-mbstring (1:7.2+60ubuntu1) ...
Setting up libcurl4:amd64 (7.58.0-2ubuntu3.24) ...
Setting up php-cli (1:7.2+60ubuntu1) ...
Setting up php-mbstring (1:7.2+60ubuntu1) ...
Setting up git (1:2.17.1-1ubuntu0.18) ...
Setting up curl (7.58.0-2ubuntu3.24) ...
Processing triggers for man-db (2.8.3-2ubuntu0.1) ...
Processing triggers for libc-bin (2.27-3ubuntu1.6) ...
root@tcargobr:~# cd ~
root@tcargobr:~# curl -sS https://getcomposer.org/installer -o composer-setup.php

root@tcargobr:~# 
root@tcargobr:~# cd ~
root@tcargobr:~# curl -sS https://getcomposer.org/installer -o composer-setup.php
root@tcargobr:~# HASH="$(curl -sS https://composer.github.io/installer.sig)"
root@tcargobr:~# sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
All settings correct for using Composer
Downloading...

Composer (version 2.7.1) successfully installed to: /usr/local/bin/composer
Use it: php /usr/local/bin/composer

root@tcargobr:~# sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
All settings correct for using Composer
Downloading...

Composer (version 2.7.1) successfully installed to: /usr/local/bin/composer
Use it: php /usr/local/bin/composer

root@tcargobr:~# composer --version
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? 
Composer version 2.7.1 2024-02-09 15:26:28
root@tcargobr:~# client_loop: send disconnect: Broken pipe
pablorio@MacBook-Air-de-Pablo ~ % ssh root@185.137.92.229
root@185.137.92.229's password: 
Welcome to Ubuntu 18.04.6 LTS (GNU/Linux 4.15.0 x86_64)

 * Documentation:  https://help.ubuntu.com
 * Management:     https://landscape.canonical.com
 * Support:        https://ubuntu.com/advantage
Last login: Mon Feb 26 16:10:19 2024 from 38.51.27.9
root@tcargobr:~# ls
composer-setup.php  composer.phar  hst-install-ubuntu.sh  hst_install_backups
root@tcargobr:~# sudo nano /etc/nginx/conf.d/api-btz-tcargo
root@tcargobr:~# sudo systemctl restart nginx
root@tcargobr:~# cd /root
root@tcargobr:~# ls
composer-setup.php  composer.phar  hst-install-ubuntu.sh  hst_install_backups
root@tcargobr:~# git clone https://github.com/BOTzerorepo/api-btz-tcargo.git api-btz-tcargo
Cloning into 'api-btz-tcargo'...
Username for 'https://github.com': pachimanok
Password for 'https://pachimanok@github.com': 
remote: Enumerating objects: 1743, done.
remote: Counting objects: 100% (1743/1743), done.
remote: Compressing objects: 100% (633/633), done.
remote: Total 1743 (delta 1068), reused 1743 (delta 1068), pack-reused 0
Receiving objects: 100% (1743/1743), 5.75 MiB | 2.22 MiB/s, done.
Resolving deltas: 100% (1068/1068), done.
root@tcargobr:~# ls
api-btz-tcargo  composer-setup.php  composer.phar  hst-install-ubuntu.sh  hst_install_backups
root@tcargobr:~# ls
api-btz-tcargo  composer-setup.php  composer.phar  hst-install-ubuntu.sh  hst_install_backups
root@tcargobr:~# api-btz-tcargo/
-bash: api-btz-tcargo/: Is a directory
root@tcargobr:~# ls
api-btz-tcargo  composer-setup.php  composer.phar  hst-install-ubuntu.sh  hst_install_backups
root@tcargobr:~# cd api-btz-tcargo/
root@tcargobr:~/api-btz-tcargo# ls
README.md  app  artisan  bootstrap  composer.json  composer.lock  config  database  default.php  default.php.old.php  lang  leeme.txt  mail  package.json  phpunit.xml  prueba.txt  public  resources  routes  storage  tests  webpack.mix.js
root@tcargobr:~/api-btz-tcargo# sudo systemctl restart nginx
root@tcargobr:~/api-btz-tcargo# curl ifconfig.me

2a02:4780:14:e8f9::1root@tcargobr:~/api-btz-tcargo# 
root@tcargobr:~/api-btz-tcargo# curl ifconfig.me
2a02:4780:14:e8f9::1root@tcargobr:~/api-btz-tcargo# sudo iptables -A INPUT -p tcp --dport 3001 -j ACCEPT
root@tcargobr:~/api-btz-tcargo# sudo iptables-save > /etc/iptables/rules.v4
-bash: /etc/iptables/rules.v4: No such file or directory
root@tcargobr:~/api-btz-tcargo# sudo firewall-cmd --zone=public --add-port=3001/tcp --permanent
sudo: firewall-cmd: command not found
root@tcargobr:~/api-btz-tcargo# sudo firewall-cmd --reload
sudo: firewall-cmd: command not found
root@tcargobr:~/api-btz-tcargo# sudo iptables -L
Chain INPUT (policy DROP)
target     prot opt source               destination         
fail2ban-HESTIA  tcp  --  anywhere             anywhere             tcp dpt:8083
fail2ban-MAIL  tcp  --  anywhere             anywhere             multiport dports smtp,urd,submission,pop3,pop3s,imap2,imaps
fail2ban-SSH  tcp  --  anywhere             anywhere             tcp dpt:ssh
fail2ban-RECIDIVE  tcp  --  anywhere             anywhere             multiport dports tcpmux:65535
ACCEPT     all  --  anywhere             anywhere             state RELATED,ESTABLISHED
ACCEPT     all  --  tcargobr.vps         anywhere            
ACCEPT     all  --  localhost            anywhere            
ACCEPT     tcp  --  anywhere             anywhere             tcp dpt:ssh
ACCEPT     tcp  --  anywhere             anywhere             multiport dports http,https
ACCEPT     tcp  --  anywhere             anywhere             multiport dports ftp,12000:12100
ACCEPT     udp  --  anywhere             anywhere             udp dpt:domain
ACCEPT     tcp  --  anywhere             anywhere             tcp dpt:domain
ACCEPT     tcp  --  anywhere             anywhere             multiport dports smtp,urd,submission
ACCEPT     tcp  --  anywhere             anywhere             multiport dports pop3,pop3s
ACCEPT     tcp  --  anywhere             anywhere             multiport dports imap2,imaps
ACCEPT     tcp  --  anywhere             anywhere             tcp dpt:8083
ACCEPT     icmp --  anywhere             anywhere            
ACCEPT     tcp  --  anywhere             anywhere             tcp dpt:3001

Chain FORWARD (policy ACCEPT)
target     prot opt source               destination         

Chain OUTPUT (policy ACCEPT)
target     prot opt source               destination         

Chain fail2ban-HESTIA (1 references)
target     prot opt source               destination         
RETURN     all  --  anywhere             anywhere            

Chain fail2ban-MAIL (1 references)
target     prot opt source               destination         
RETURN     all  --  anywhere             anywhere            

Chain fail2ban-RECIDIVE (1 references)
target     prot opt source               destination         
REJECT     all  --  170.64.217.222       anywhere             reject-with icmp-port-unreachable
REJECT     all  --  170.64.143.179       anywhere             reject-with icmp-port-unreachable
REJECT     all  --  170.64.157.246       anywhere             reject-with icmp-port-unreachable
REJECT     all  --  124.123.34.205       anywhere             reject-with icmp-port-unreachable
REJECT     all  --  170.64.139.104       anywhere             reject-with icmp-port-unreachable
REJECT     all  --  167.71.187.81        anywhere             reject-with icmp-port-unreachable
REJECT     all  --  170.64.217.121       anywhere             reject-with icmp-port-unreachable
RETURN     all  --  anywhere             anywhere            

Chain fail2ban-SSH (1 references)
target     prot opt source               destination         
REJECT     all  --  64.23.168.69         anywhere             reject-with icmp-port-unreachable
REJECT     all  --  64.23.168.69         anywhere             reject-with icmp-port-unreachable
RETURN     all  --  anywhere             anywhere            

Chain hestia (0 references)
target     prot opt source               destination         
root@tcargobr:~/api-btz-tcargo# sudo netstat -tuln
Active Internet connections (only servers)
Proto Recv-Q Send-Q Local Address           Foreign Address         State      
tcp        0      0 127.0.0.1:9000          0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:3306          0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:587             0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:110             0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:143             0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:783           0.0.0.0:*               LISTEN     
tcp        0      0 185.137.92.229:80       0.0.0.0:*               LISTEN     
tcp        0      0 185.137.92.229:8080     0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:465             0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:8081          0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:9970          0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:8083            0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:9971          0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:8084          0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:9972          0.0.0.0:*               LISTEN     
tcp        0      0 185.137.92.229:53       0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:53            0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:9973          0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:9974          0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:22              0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:25              0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:953           0.0.0.0:*               LISTEN     
tcp        0      0 185.137.92.229:443      0.0.0.0:*               LISTEN     
tcp        0      0 185.137.92.229:8443     0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:9980          0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:993             0.0.0.0:*               LISTEN     
tcp        0      0 0.0.0.0:995             0.0.0.0:*               LISTEN     
tcp        0      0 127.0.0.1:9956          0.0.0.0:*               LISTEN     
tcp6       0      0 :::110                  :::*                    LISTEN     
tcp6       0      0 :::143                  :::*                    LISTEN     
tcp6       0      0 ::1:783                 :::*                    LISTEN     
tcp6       0      0 :::53                   :::*                    LISTEN     
tcp6       0      0 :::21                   :::*                    LISTEN     
tcp6       0      0 :::22                   :::*                    LISTEN     
tcp6       0      0 ::1:953                 :::*                    LISTEN     
tcp6       0      0 :::993                  :::*                    LISTEN     
tcp6       0      0 :::995                  :::*                    LISTEN     
udp        0      0 185.137.92.229:53       0.0.0.0:*                          
udp        0      0 127.0.0.1:53            0.0.0.0:*                          
udp6       0      0 :::53                   :::*                               
root@tcargobr:~/api-btz-tcargo# ls
README.md  app  artisan  bootstrap  composer.json  composer.lock  config  database  default.php  default.php.old.php  lang  leeme.txt  mail  package.json  phpunit.xml  prueba.txt  public  resources  routes  storage  tests  webpack.mix.js
root@tcargobr:~/api-btz-tcargo# composer install
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? yes
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Your lock file does not contain a compatible set of packages. Please run composer update.

  Problem 1
    - lcobucci/clock is locked to version 3.2.0 and an update of this package was not requested.
    - lcobucci/clock 3.2.0 requires php ~8.2.0 || ~8.3.0 -> your php version (8.1.12) does not satisfy that requirement.
  Problem 2
    - symfony/event-dispatcher is locked to version v7.0.0 and an update of this package was not requested.
    - symfony/event-dispatcher v7.0.0 requires php >=8.2 -> your php version (8.1.12) does not satisfy that requirement.
  Problem 3
    - symfony/string is locked to version v7.0.0 and an update of this package was not requested.
    - symfony/string v7.0.0 requires php >=8.2 -> your php version (8.1.12) does not satisfy that requirement.
  Problem 4
    - symfony/string v7.0.0 requires php >=8.2 -> your php version (8.1.12) does not satisfy that requirement.
    - symfony/console v6.4.1 requires symfony/string ^5.4|^6.0|^7.0 -> satisfiable by symfony/string[v7.0.0].
    - symfony/console is locked to version v6.4.1 and an update of this package was not requested.

root@tcargobr:~/api-btz-tcargo# php8.2-fpm x86 installation
-bash: php8.2-fpm: command not found
root@tcargobr:~/api-btz-tcargo# apt install php8.2
Reading package lists... Done
Building dependency tree       
Reading state information... Done
E: Unable to locate package php8.2
E: Couldn't find any package by glob 'php8.2'
E: Couldn't find any package by regex 'php8.2'
root@tcargobr:~/api-btz-tcargo# php -v
PHP 8.1.12 (cli) (built: Oct 28 2022 17:39:18) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.12, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.12, Copyright (c), by Zend Technologies
root@tcargobr:~/api-btz-tcargo# sudo add-apt-repository ppa:ondrej/php
 Co-installable PHP versions: PHP 5.6, PHP 7.x, PHP 8.x and most requested extensions are included. Only Supported Versions of PHP (http://php.net/supported-versions.php) for Supported Ubuntu Releases (https://wiki.ubuntu.com/Releases) are provided. Don't ask for end-of-life PHP versions or Ubuntu release, they won't be provided.

Debian oldstable and stable packages are provided as well: https://deb.sury.org/#debian-dpa

You can get more information about the packages at https://deb.sury.org

IMPORTANT: The <foo>-backports is now required on older Ubuntu releases.

BUGS&FEATURES: This PPA now has a issue tracker:
https://deb.sury.org/#bug-reporting

CAVEATS:
1. If you are using php-gearman, you need to add ppa:ondrej/pkg-gearman
2. If you are using apache2, you are advised to add ppa:ondrej/apache2
3. If you are using nginx, you are advised to add ppa:ondrej/nginx-mainline
   or ppa:ondrej/nginx

PLEASE READ: If you like my work and want to give me a little motivation, please consider donating regularly: https://donate.sury.org/

WARNING: add-apt-repository is broken with non-UTF-8 locales, see
https://github.com/oerdnj/deb.sury.org/issues/56 for workaround:

# LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
 More info: https://launchpad.net/~ondrej/+archive/ubuntu/php
Press [ENTER] to continue or Ctrl-c to cancel adding it.

Hit:1 http://security.ubuntu.com/ubuntu bionic-security InRelease                                                                                                                                                                                                              
Hit:2 http://archive.canonical.com/ubuntu bionic InRelease                                                                                                                                                                                                                     
Hit:3 http://ppa.launchpad.net/ondrej/apache2/ubuntu bionic InRelease                                                                                                                                                                                                          
Hit:4 http://archive.ubuntu.com/ubuntu bionic InRelease                                                                                                                                                                                   
Hit:5 http://ppa.launchpad.net/ondrej/php/ubuntu bionic InRelease                                                                                                                                                                         
Hit:6 http://archive.ubuntu.com/ubuntu bionic-updates InRelease                                                                                                       
Hit:7 https://apt.hestiacp.com bionic InRelease                                                                                                 
Get:8 https://dlm.mariadb.com/repo/mariadb-server/10.6/repo/ubuntu bionic InRelease [6265 B]  
Hit:9 https://nginx.org/packages/mainline/ubuntu bionic InRelease       
Fetched 6265 B in 5s (1357 B/s)                   
Reading package lists... Done
root@tcargobr:~/api-btz-tcargo# sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml
Reading package lists... Done
Building dependency tree       
Reading state information... Done
E: Unable to locate package php8.2
E: Couldn't find any package by glob 'php8.2'
E: Couldn't find any package by regex 'php8.2'
E: Unable to locate package php8.2-cli
E: Couldn't find any package by glob 'php8.2-cli'
E: Couldn't find any package by regex 'php8.2-cli'
E: Unable to locate package php8.2-fpm
E: Couldn't find any package by glob 'php8.2-fpm'
E: Couldn't find any package by regex 'php8.2-fpm'
E: Unable to locate package php8.2-mysql
E: Couldn't find any package by glob 'php8.2-mysql'
E: Couldn't find any package by regex 'php8.2-mysql'
E: Unable to locate package php8.2-curl
E: Couldn't find any package by glob 'php8.2-curl'
E: Couldn't find any package by regex 'php8.2-curl'
E: Unable to locate package php8.2-gd
E: Couldn't find any package by glob 'php8.2-gd'
E: Couldn't find any package by regex 'php8.2-gd'
E: Unable to locate package php8.2-mbstring
E: Couldn't find any package by glob 'php8.2-mbstring'
E: Couldn't find any package by regex 'php8.2-mbstring'
E: Unable to locate package php8.2-xml
E: Couldn't find any package by glob 'php8.2-xml'
E: Couldn't find any package by regex 'php8.2-xml'
root@tcargobr:~/api-btz-tcargo# php -v
PHP 8.1.12 (cli) (built: Oct 28 2022 17:39:18) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.12, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.12, Copyright (c), by Zend Technologies
root@tcargobr:~/api-btz-tcargo# sudo add-apt-repository ppa:ondrej/php
 Co-installable PHP versions: PHP 5.6, PHP 7.x, PHP 8.x and most requested extensions are included. Only Supported Versions of PHP (http://php.net/supported-versions.php) for Supported Ubuntu Releases (https://wiki.ubuntu.com/Releases) are provided. Don't ask for end-of-life PHP versions or Ubuntu release, they won't be provided.

Debian oldstable and stable packages are provided as well: https://deb.sury.org/#debian-dpa

You can get more information about the packages at https://deb.sury.org

IMPORTANT: The <foo>-backports is now required on older Ubuntu releases.

BUGS&FEATURES: This PPA now has a issue tracker:
https://deb.sury.org/#bug-reporting

CAVEATS:
1. If you are using php-gearman, you need to add ppa:ondrej/pkg-gearman
2. If you are using apache2, you are advised to add ppa:ondrej/apache2
3. If you are using nginx, you are advised to add ppa:ondrej/nginx-mainline
   or ppa:ondrej/nginx

PLEASE READ: If you like my work and want to give me a little motivation, please consider donating regularly: https://donate.sury.org/

WARNING: add-apt-repository is broken with non-UTF-8 locales, see
https://github.com/oerdnj/deb.sury.org/issues/56 for workaround:

# LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
 More info: https://launchpad.net/~ondrej/+archive/ubuntu/php
Press [ENTER] to continue or Ctrl-c to cancel adding it.
^C

root@tcargobr:~/api-btz-tcargo# sudo apt update
Hit:1 http://archive.ubuntu.com/ubuntu bionic InRelease                                                                                                                                                                                                                        
Hit:2 http://security.ubuntu.com/ubuntu bionic-security InRelease                                                                                                                                                                                                              
Hit:3 http://archive.ubuntu.com/ubuntu bionic-updates InRelease                                                                                                                                                                                                                
Hit:4 http://archive.canonical.com/ubuntu bionic InRelease                                                                                                                                                                                                    
Hit:5 http://ppa.launchpad.net/ondrej/apache2/ubuntu bionic InRelease                                                                                                                                                                   
Hit:6 http://ppa.launchpad.net/ondrej/php/ubuntu bionic InRelease                                                                                                                                                 
Get:7 https://dlm.mariadb.com/repo/mariadb-server/10.6/repo/ubuntu bionic InRelease [6265 B]                                                    
Hit:8 https://apt.hestiacp.com bionic InRelease                                                                                      
Hit:9 https://nginx.org/packages/mainline/ubuntu bionic InRelease                                   
Fetched 6265 B in 4s (1418 B/s)                    
Reading package lists... Done
Building dependency tree       
Reading state information... Done
138 packages can be upgraded. Run 'apt list --upgradable' to see them.
root@tcargobr:~/api-btz-tcargo# sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml
Reading package lists... Done
Building dependency tree       
Reading state information... Done
E: Unable to locate package php8.2
E: Couldn't find any package by glob 'php8.2'
E: Couldn't find any package by regex 'php8.2'
E: Unable to locate package php8.2-cli
E: Couldn't find any package by glob 'php8.2-cli'
E: Couldn't find any package by regex 'php8.2-cli'
E: Unable to locate package php8.2-fpm
E: Couldn't find any package by glob 'php8.2-fpm'
E: Couldn't find any package by regex 'php8.2-fpm'
E: Unable to locate package php8.2-mysql
E: Couldn't find any package by glob 'php8.2-mysql'
E: Couldn't find any package by regex 'php8.2-mysql'
E: Unable to locate package php8.2-curl
E: Couldn't find any package by glob 'php8.2-curl'
E: Couldn't find any package by regex 'php8.2-curl'
E: Unable to locate package php8.2-gd
E: Couldn't find any package by glob 'php8.2-gd'
E: Couldn't find any package by regex 'php8.2-gd'
E: Unable to locate package php8.2-mbstring
E: Couldn't find any package by glob 'php8.2-mbstring'
E: Couldn't find any package by regex 'php8.2-mbstring'
E: Unable to locate package php8.2-xml
E: Couldn't find any package by glob 'php8.2-xml'
E: Couldn't find any package by regex 'php8.2-xml'
root@tcargobr:~/api-btz-tcargo# php --ini
Configuration File (php.ini) Path: /etc/php/8.1/cli
Loaded Configuration File:         /etc/php/8.1/cli/php.ini
Scan for additional .ini files in: /etc/php/8.1/cli/conf.d
Additional .ini files parsed:      /etc/php/8.1/cli/conf.d/10-mysqlnd.ini,
/etc/php/8.1/cli/conf.d/10-opcache.ini,
/etc/php/8.1/cli/conf.d/10-pdo.ini,
/etc/php/8.1/cli/conf.d/15-xml.ini,
/etc/php/8.1/cli/conf.d/20-bcmath.ini,
/etc/php/8.1/cli/conf.d/20-bz2.ini,
/etc/php/8.1/cli/conf.d/20-calendar.ini,
/etc/php/8.1/cli/conf.d/20-ctype.ini,
/etc/php/8.1/cli/conf.d/20-curl.ini,
/etc/php/8.1/cli/conf.d/20-dom.ini,
/etc/php/8.1/cli/conf.d/20-exif.ini,
/etc/php/8.1/cli/conf.d/20-ffi.ini,
/etc/php/8.1/cli/conf.d/20-fileinfo.ini,
/etc/php/8.1/cli/conf.d/20-ftp.ini,
/etc/php/8.1/cli/conf.d/20-gd.ini,
/etc/php/8.1/cli/conf.d/20-gettext.ini,
/etc/php/8.1/cli/conf.d/20-iconv.ini,
/etc/php/8.1/cli/conf.d/20-imagick.ini,
/etc/php/8.1/cli/conf.d/20-imap.ini,
/etc/php/8.1/cli/conf.d/20-intl.ini,
/etc/php/8.1/cli/conf.d/20-ldap.ini,
/etc/php/8.1/cli/conf.d/20-mbstring.ini,
/etc/php/8.1/cli/conf.d/20-mysqli.ini,
/etc/php/8.1/cli/conf.d/20-pdo_mysql.ini,
/etc/php/8.1/cli/conf.d/20-phar.ini,
/etc/php/8.1/cli/conf.d/20-posix.ini,
/etc/php/8.1/cli/conf.d/20-pspell.ini,
/etc/php/8.1/cli/conf.d/20-readline.ini,
/etc/php/8.1/cli/conf.d/20-shmop.ini,
/etc/php/8.1/cli/conf.d/20-simplexml.ini,
/etc/php/8.1/cli/conf.d/20-soap.ini,
/etc/php/8.1/cli/conf.d/20-sockets.ini,
/etc/php/8.1/cli/conf.d/20-sysvmsg.ini,
/etc/php/8.1/cli/conf.d/20-sysvsem.ini,
/etc/php/8.1/cli/conf.d/20-sysvshm.ini,
/etc/php/8.1/cli/conf.d/20-tokenizer.ini,
/etc/php/8.1/cli/conf.d/20-xmlreader.ini,
/etc/php/8.1/cli/conf.d/20-xmlwriter.ini,
/etc/php/8.1/cli/conf.d/20-xsl.ini,
/etc/php/8.1/cli/conf.d/20-zip.ini

root@tcargobr:~/api-btz-tcargo# nano /usr/local/hestia/web/edit/server/index.php
root@tcargobr:~/api-btz-tcargo# vim /usr/local/hestia/web/edit/server/index.php
root@tcargobr:~/api-btz-tcargo# vim /usr/local/hestia/web/edit/server/index.php
root@tcargobr:~/api-btz-tcargo# sudo apt install php8.2
Reading package lists... Done
Building dependency tree       
Reading state information... Done
E: Unable to locate package php8.2
E: Couldn't find any package by glob 'php8.2'
E: Couldn't find any package by regex 'php8.2'
root@tcargobr:~/api-btz-tcargo# php -v
PHP 8.1.12 (cli) (built: Oct 28 2022 17:39:18) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.12, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.12, Copyright (c), by Zend Technologies
root@tcargobr:~/api-btz-tcargo# client_loop: send disconnect: Broken pipe
pablorio@MacBook-Air-de-Pablo ~ % ssh root@185.137.92.229
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
@    WARNING: REMOTE HOST IDENTIFICATION HAS CHANGED!     @
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
IT IS POSSIBLE THAT SOMEONE IS DOING SOMETHING NASTY!
Someone could be eavesdropping on you right now (man-in-the-middle attack)!
It is also possible that a host key has just been changed.
The fingerprint for the RSA key sent by the remote host is
SHA256:hrjytozWbcuIyssqubo+m2qs2gS+Ro58a6TVBtegwlk.
Please contact your system administrator.
Add correct host key in /Users/pablorio/.ssh/known_hosts to get rid of this message.
Offending RSA key in /Users/pablorio/.ssh/known_hosts:26
Host key for 185.137.92.229 has changed and you have requested strict checking.
Host key verification failed.
pablorio@MacBook-Air-de-Pablo ~ % cd ~/.ssh

pablorio@MacBook-Air-de-Pablo .ssh % nano known_hosts
pablorio@MacBook-Air-de-Pablo .ssh % ssh root@185.137.92.229
The authenticity of host '185.137.92.229 (185.137.92.229)' can't be established.
RSA key fingerprint is SHA256:hrjytozWbcuIyssqubo+m2qs2gS+Ro58a6TVBtegwlk.
This key is not known by any other names.
Are you sure you want to continue connecting (yes/no/[fingerprint])? yes
Warning: Permanently added '185.137.92.229' (RSA) to the list of known hosts.
root@185.137.92.229's password: 
Welcome to Ubuntu 22.04.3 LTS (GNU/Linux 5.2.0 x86_64)

 * Documentation:  https://help.ubuntu.com
 * Management:     https://landscape.canonical.com
 * Support:        https://ubuntu.com/advantage

The programs included with the Ubuntu system are free software;
the exact distribution terms for each program are described in the
individual files in /usr/share/doc/*/copyright.

Ubuntu comes with ABSOLUTELY NO WARRANTY, to the extent permitted by
applicable law.

root@tcargobr:~# ls
root@tcargobr:~# sudo apt update
Hit:1 http://archive.canonical.com/ubuntu jammy InRelease                                        
Hit:2 http://archive.ubuntu.com/ubuntu jammy InRelease                                           
Get:3 http://security.ubuntu.com/ubuntu jammy-security InRelease [110 kB]
Get:4 http://archive.ubuntu.com/ubuntu jammy-updates InRelease [119 kB]
Get:5 http://security.ubuntu.com/ubuntu jammy-security/main amd64 Packages [1201 kB]
Get:6 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 Packages [1419 kB]
Get:7 http://security.ubuntu.com/ubuntu jammy-security/main Translation-en [218 kB]
Get:8 http://security.ubuntu.com/ubuntu jammy-security/restricted amd64 Packages [1476 kB]    
Get:9 http://security.ubuntu.com/ubuntu jammy-security/restricted Translation-en [244 kB]       
Get:10 http://security.ubuntu.com/ubuntu jammy-security/universe amd64 Packages [845 kB]         
Get:11 http://security.ubuntu.com/ubuntu jammy-security/universe Translation-en [161 kB]    
Get:12 http://security.ubuntu.com/ubuntu jammy-security/multiverse amd64 Packages [37.1 kB]     
Get:13 http://archive.ubuntu.com/ubuntu jammy-updates/main Translation-en [278 kB]              
Get:14 http://archive.ubuntu.com/ubuntu jammy-updates/restricted amd64 Packages [1504 kB]
Get:15 http://archive.ubuntu.com/ubuntu jammy-updates/restricted Translation-en [247 kB]
Get:16 http://archive.ubuntu.com/ubuntu jammy-updates/universe amd64 Packages [1050 kB]
Get:17 http://archive.ubuntu.com/ubuntu jammy-updates/universe Translation-en [237 kB]
Fetched 9147 kB in 10s (944 kB/s)                                  
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
48 packages can be upgraded. Run 'apt list --upgradable' to see them.
root@tcargobr:~# sudo apt upgrade
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
Calculating upgrade... Done
The following packages will be upgraded:
  base-files bind9 bind9-dnsutils bind9-host bind9-libs bind9-utils binutils binutils-common
  binutils-x86-64-linux-gnu coreutils dns-root-data dnsutils exim4 exim4-base exim4-config
  exim4-daemon-light iptables ldap-utils less libbinutils libctf-nobfd0 libctf0 libgnutls-dane0
  libgnutls30 libip4tc2 libip6tc2 libldap-2.5-0 libpam-modules libpam-modules-bin libpam-runtime
  libpam0g libssh-4 libssl3 libsystemd0 libudev1 libunbound8 libuv1 libxml2 libxtables12 login
  openssl passwd systemd systemd-sysv tcpdump tzdata udev unzip
48 upgraded, 0 newly installed, 0 to remove and 0 not upgraded.
Need to get 24.1 MB of archives.
After this operation, 25.6 kB of additional disk space will be used.
Do you want to continue? [Y/n] Y
Get:1 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 base-files amd64 12ubuntu4.6 [62.5 kB]
Get:2 http://security.ubuntu.com/ubuntu jammy-security/main amd64 libunbound8 amd64 1.13.1-1ubuntu5.4 [398 kB]
Get:3 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 coreutils amd64 8.32-4.1ubuntu1.1 [1436 kB]
Get:4 http://security.ubuntu.com/ubuntu jammy-security/main amd64 libuv1 amd64 1.43.0-1ubuntu0.1 [92.7 kB]
Get:5 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 login amd64 1:4.8.1-2ubuntu2.2 [188 kB]
Get:6 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libpam0g amd64 1.4.0-11ubuntu2.4 [60.2 kB]
Get:7 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libpam-modules-bin amd64 1.4.0-11ubuntu2.4 [37.6 kB]
Get:8 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libpam-modules amd64 1.4.0-11ubuntu2.4 [280 kB]
Get:9 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 systemd-sysv amd64 249.11-0ubuntu3.12 [10.5 kB]
Get:10 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libsystemd0 amd64 249.11-0ubuntu3.12 [319 kB]
Get:11 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 systemd amd64 249.11-0ubuntu3.12 [4581 kB]
Get:12 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 udev amd64 249.11-0ubuntu3.12 [1557 kB]
Get:13 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libudev1 amd64 249.11-0ubuntu3.12 [78.2 kB]
Get:14 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libgnutls-dane0 amd64 3.7.3-4ubuntu1.4 [22.6 kB]
Get:15 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libgnutls30 amd64 3.7.3-4ubuntu1.4 [969 kB]
Get:16 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libxtables12 amd64 1.8.7-1ubuntu5.2 [31.3 kB]
Get:17 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 iptables amd64 1.8.7-1ubuntu5.2 [455 kB]
Get:18 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libip6tc2 amd64 1.8.7-1ubuntu5.2 [20.3 kB]
Get:19 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libip4tc2 amd64 1.8.7-1ubuntu5.2 [19.9 kB]
Get:20 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libssl3 amd64 3.0.2-0ubuntu1.15 [1905 kB]
Get:21 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 bind9 amd64 1:9.18.18-0ubuntu0.22.04.2 [260 kB]
Get:22 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 bind9-utils amd64 1:9.18.18-0ubuntu0.22.04.2 [161 kB]
Get:23 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 bind9-host amd64 1:9.18.18-0ubuntu0.22.04.2 [52.5 kB]
Get:24 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 bind9-dnsutils amd64 1:9.18.18-0ubuntu0.22.04.2 [157 kB]
Get:25 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libxml2 amd64 2.9.13+dfsg-1ubuntu0.4 [763 kB]
Get:26 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 bind9-libs amd64 1:9.18.18-0ubuntu0.22.04.2 [1245 kB]
Get:27 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 dns-root-data all 2023112702~ubuntu0.22.04.1 [5136 B]
Get:28 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libpam-runtime all 1.4.0-11ubuntu2.4 [40.3 kB]
Get:29 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 passwd amd64 1:4.8.1-2ubuntu2.2 [768 kB]
Get:30 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 less amd64 590-1ubuntu0.22.04.2 [143 kB]
Get:31 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 openssl amd64 3.0.2-0ubuntu1.15 [1186 kB]
Get:32 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 tzdata all 2023d-0ubuntu0.22.04 [351 kB]
Get:33 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 tcpdump amd64 4.99.1-3ubuntu0.2 [501 kB]
Get:34 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libctf0 amd64 2.38-4ubuntu2.6 [103 kB]
Get:35 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libctf-nobfd0 amd64 2.38-4ubuntu2.6 [108 kB]
Get:36 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 binutils-x86-64-linux-gnu amd64 2.38-4ubuntu2.6 [2326 kB]
Get:37 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libbinutils amd64 2.38-4ubuntu2.6 [662 kB]
Get:38 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 binutils amd64 2.38-4ubuntu2.6 [3200 B]
Get:39 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 binutils-common amd64 2.38-4ubuntu2.6 [222 kB]
Get:40 http://archive.ubuntu.com/ubuntu jammy-updates/universe amd64 dnsutils all 1:9.18.18-0ubuntu0.22.04.2 [3926 B]
Get:41 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 exim4-config all 4.95-4ubuntu2.5 [252 kB]
Get:42 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 exim4 all 4.95-4ubuntu2.5 [7578 B]
Get:43 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 exim4-base amd64 4.95-4ubuntu2.5 [947 kB]
Get:44 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 exim4-daemon-light amd64 4.95-4ubuntu2.5 [602 kB]
Get:45 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 ldap-utils amd64 2.5.16+dfsg-0ubuntu0.22.04.2 [147 kB]
Get:46 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libldap-2.5-0 amd64 2.5.16+dfsg-0ubuntu0.22.04.2 [183 kB]
Get:47 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libssh-4 amd64 0.9.6-2ubuntu0.22.04.3 [186 kB]
Get:48 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 unzip amd64 6.0-26ubuntu3.2 [175 kB]
Fetched 24.1 MB in 4s (5400 kB/s)
Extracting templates from packages: 100%
Preconfiguring packages ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../base-files_12ubuntu4.6_amd64.deb ...
Unpacking base-files (12ubuntu4.6) over (12ubuntu4.4) ...
Setting up base-files (12ubuntu4.6) ...
Installing new version of config file /etc/issue ...
Installing new version of config file /etc/issue.net ...
Installing new version of config file /etc/lsb-release ...
Installing new version of config file /etc/update-motd.d/10-help-text ...
motd-news.service is a disabled or a static unit not running, not starting it.
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../coreutils_8.32-4.1ubuntu1.1_amd64.deb ...
Unpacking coreutils (8.32-4.1ubuntu1.1) over (8.32-4.1ubuntu1) ...
Setting up coreutils (8.32-4.1ubuntu1.1) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../login_1%3a4.8.1-2ubuntu2.2_amd64.deb ...
Unpacking login (1:4.8.1-2ubuntu2.2) over (1:4.8.1-2ubuntu2.1) ...
Setting up login (1:4.8.1-2ubuntu2.2) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../libpam0g_1.4.0-11ubuntu2.4_amd64.deb ...
Unpacking libpam0g:amd64 (1.4.0-11ubuntu2.4) over (1.4.0-11ubuntu2.3) ...
Setting up libpam0g:amd64 (1.4.0-11ubuntu2.4) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../libpam-modules-bin_1.4.0-11ubuntu2.4_amd64.deb ...
Unpacking libpam-modules-bin (1.4.0-11ubuntu2.4) over (1.4.0-11ubuntu2.3) ...
Setting up libpam-modules-bin (1.4.0-11ubuntu2.4) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../libpam-modules_1.4.0-11ubuntu2.4_amd64.deb ...
Unpacking libpam-modules:amd64 (1.4.0-11ubuntu2.4) over (1.4.0-11ubuntu2.3) ...
Setting up libpam-modules:amd64 (1.4.0-11ubuntu2.4) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../systemd-sysv_249.11-0ubuntu3.12_amd64.deb ...
Unpacking systemd-sysv (249.11-0ubuntu3.12) over (249.11-0ubuntu3.11) ...
Preparing to unpack .../libsystemd0_249.11-0ubuntu3.12_amd64.deb ...
Unpacking libsystemd0:amd64 (249.11-0ubuntu3.12) over (249.11-0ubuntu3.11) ...
Setting up libsystemd0:amd64 (249.11-0ubuntu3.12) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../systemd_249.11-0ubuntu3.12_amd64.deb ...
Unpacking systemd (249.11-0ubuntu3.12) over (249.11-0ubuntu3.11) ...
Preparing to unpack .../udev_249.11-0ubuntu3.12_amd64.deb ...
Unpacking udev (249.11-0ubuntu3.12) over (249.11-0ubuntu3.11) ...
Preparing to unpack .../libudev1_249.11-0ubuntu3.12_amd64.deb ...
Unpacking libudev1:amd64 (249.11-0ubuntu3.12) over (249.11-0ubuntu3.11) ...
Setting up libudev1:amd64 (249.11-0ubuntu3.12) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../libunbound8_1.13.1-1ubuntu5.4_amd64.deb ...
Unpacking libunbound8:amd64 (1.13.1-1ubuntu5.4) over (1.13.1-1ubuntu5.3) ...
Preparing to unpack .../libgnutls-dane0_3.7.3-4ubuntu1.4_amd64.deb ...
Unpacking libgnutls-dane0:amd64 (3.7.3-4ubuntu1.4) over (3.7.3-4ubuntu1.3) ...
Preparing to unpack .../libgnutls30_3.7.3-4ubuntu1.4_amd64.deb ...
Unpacking libgnutls30:amd64 (3.7.3-4ubuntu1.4) over (3.7.3-4ubuntu1.3) ...
Setting up libgnutls30:amd64 (3.7.3-4ubuntu1.4) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../libxtables12_1.8.7-1ubuntu5.2_amd64.deb ...
Unpacking libxtables12:amd64 (1.8.7-1ubuntu5.2) over (1.8.7-1ubuntu5.1) ...
Preparing to unpack .../iptables_1.8.7-1ubuntu5.2_amd64.deb ...
Unpacking iptables (1.8.7-1ubuntu5.2) over (1.8.7-1ubuntu5.1) ...
Preparing to unpack .../libip6tc2_1.8.7-1ubuntu5.2_amd64.deb ...
Unpacking libip6tc2:amd64 (1.8.7-1ubuntu5.2) over (1.8.7-1ubuntu5.1) ...
Preparing to unpack .../libip4tc2_1.8.7-1ubuntu5.2_amd64.deb ...
Unpacking libip4tc2:amd64 (1.8.7-1ubuntu5.2) over (1.8.7-1ubuntu5.1) ...
Preparing to unpack .../libssl3_3.0.2-0ubuntu1.15_amd64.deb ...
Unpacking libssl3:amd64 (3.0.2-0ubuntu1.15) over (3.0.2-0ubuntu1.12) ...
Setting up libssl3:amd64 (3.0.2-0ubuntu1.15) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../0-bind9_1%3a9.18.18-0ubuntu0.22.04.2_amd64.deb ...
Unpacking bind9 (1:9.18.18-0ubuntu0.22.04.2) over (1:9.18.18-0ubuntu0.22.04.1) ...
Preparing to unpack .../1-bind9-utils_1%3a9.18.18-0ubuntu0.22.04.2_amd64.deb ...
Unpacking bind9-utils (1:9.18.18-0ubuntu0.22.04.2) over (1:9.18.18-0ubuntu0.22.04.1) ...
Preparing to unpack .../2-bind9-host_1%3a9.18.18-0ubuntu0.22.04.2_amd64.deb ...
Unpacking bind9-host (1:9.18.18-0ubuntu0.22.04.2) over (1:9.18.18-0ubuntu0.22.04.1) ...
Preparing to unpack .../3-bind9-dnsutils_1%3a9.18.18-0ubuntu0.22.04.2_amd64.deb ...
Unpacking bind9-dnsutils (1:9.18.18-0ubuntu0.22.04.2) over (1:9.18.18-0ubuntu0.22.04.1) ...
Preparing to unpack .../4-libuv1_1.43.0-1ubuntu0.1_amd64.deb ...
Unpacking libuv1:amd64 (1.43.0-1ubuntu0.1) over (1.43.0-1) ...
Preparing to unpack .../5-libxml2_2.9.13+dfsg-1ubuntu0.4_amd64.deb ...
Unpacking libxml2:amd64 (2.9.13+dfsg-1ubuntu0.4) over (2.9.13+dfsg-1ubuntu0.3) ...
Preparing to unpack .../6-bind9-libs_1%3a9.18.18-0ubuntu0.22.04.2_amd64.deb ...
Unpacking bind9-libs:amd64 (1:9.18.18-0ubuntu0.22.04.2) over (1:9.18.18-0ubuntu0.22.04.1) ...
Preparing to unpack .../7-dns-root-data_2023112702~ubuntu0.22.04.1_all.deb ...
Unpacking dns-root-data (2023112702~ubuntu0.22.04.1) over (2021011101) ...
Preparing to unpack .../8-libpam-runtime_1.4.0-11ubuntu2.4_all.deb ...
Unpacking libpam-runtime (1.4.0-11ubuntu2.4) over (1.4.0-11ubuntu2.3) ...
Setting up libpam-runtime (1.4.0-11ubuntu2.4) ...

Progress: [ 39%] [#############################...............................................] 
 Package configuration
 ────────────────────────────────────────────────────────────────────────────────────────────────






















┌─────────────────────────────────────PAM configuration────────────────────────────────────────┐
│ One or more of the files /etc/pam.d/common-{auth,account,password,session} have been         │
│ locally modified. Please indicate whether these local changes should be overridden using     │
│ the system-provided configuration. If you decline this option, you will need to manage       │
│ your system's authentication configuration by hand.                                          │
│                                                                                              │
│ Override local changes to /etc/pam.d/common-*?                                               │
│                                                                                              │
│                                                                                              │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│                               < Yes >                < No  >                                 │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
  
























(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../passwd_1%3a4.8.1-2ubuntu2.2_amd64.deb ...
Unpacking passwd (1:4.8.1-2ubuntu2.2) over (1:4.8.1-2ubuntu2.1) ...
Setting up passwd (1:4.8.1-2ubuntu2.2) ...
(Reading database ... 27157 files and directories currently installed.)
Preparing to unpack .../00-less_590-1ubuntu0.22.04.2_amd64.deb ...
Unpacking less (590-1ubuntu0.22.04.2) over (590-1ubuntu0.22.04.1) ...
Preparing to unpack .../01-openssl_3.0.2-0ubuntu1.15_amd64.deb ...
Unpacking openssl (3.0.2-0ubuntu1.15) over (3.0.2-0ubuntu1.12) ...
Preparing to unpack .../02-tzdata_2023d-0ubuntu0.22.04_all.deb ...
Unpacking tzdata (2023d-0ubuntu0.22.04) over (2023c-0ubuntu0.22.04.2) ...
Preparing to unpack .../03-tcpdump_4.99.1-3ubuntu0.2_amd64.deb ...
Unpacking tcpdump (4.99.1-3ubuntu0.2) over (4.99.1-3ubuntu0.1) ...
Preparing to unpack .../04-libctf0_2.38-4ubuntu2.6_amd64.deb ...
Unpacking libctf0:amd64 (2.38-4ubuntu2.6) over (2.38-4ubuntu2.4) ...
Preparing to unpack .../05-libctf-nobfd0_2.38-4ubuntu2.6_amd64.deb ...
Unpacking libctf-nobfd0:amd64 (2.38-4ubuntu2.6) over (2.38-4ubuntu2.4) ...
Preparing to unpack .../06-binutils-x86-64-linux-gnu_2.38-4ubuntu2.6_amd64.deb ...
Unpacking binutils-x86-64-linux-gnu (2.38-4ubuntu2.6) over (2.38-4ubuntu2.4) ...
Preparing to unpack .../07-libbinutils_2.38-4ubuntu2.6_amd64.deb ...
Unpacking libbinutils:amd64 (2.38-4ubuntu2.6) over (2.38-4ubuntu2.4) ...
Preparing to unpack .../08-binutils_2.38-4ubuntu2.6_amd64.deb ...
Unpacking binutils (2.38-4ubuntu2.6) over (2.38-4ubuntu2.4) ...
Preparing to unpack .../09-binutils-common_2.38-4ubuntu2.6_amd64.deb ...
Unpacking binutils-common:amd64 (2.38-4ubuntu2.6) over (2.38-4ubuntu2.4) ...
Preparing to unpack .../10-dnsutils_1%3a9.18.18-0ubuntu0.22.04.2_all.deb ...
Unpacking dnsutils (1:9.18.18-0ubuntu0.22.04.2) over (1:9.18.18-0ubuntu0.22.04.1) ...
Preparing to unpack .../11-exim4-config_4.95-4ubuntu2.5_all.deb ...
Unpacking exim4-config (4.95-4ubuntu2.5) over (4.95-4ubuntu2.4) ...
Preparing to unpack .../12-exim4_4.95-4ubuntu2.5_all.deb ...
Unpacking exim4 (4.95-4ubuntu2.5) over (4.95-4ubuntu2.4) ...
Preparing to unpack .../13-exim4-base_4.95-4ubuntu2.5_amd64.deb ...
Unpacking exim4-base (4.95-4ubuntu2.5) over (4.95-4ubuntu2.4) ...
Preparing to unpack .../14-exim4-daemon-light_4.95-4ubuntu2.5_amd64.deb ...
Unpacking exim4-daemon-light (4.95-4ubuntu2.5) over (4.95-4ubuntu2.4) ...
Preparing to unpack .../15-ldap-utils_2.5.16+dfsg-0ubuntu0.22.04.2_amd64.deb ...
Unpacking ldap-utils (2.5.16+dfsg-0ubuntu0.22.04.2) over (2.5.16+dfsg-0ubuntu0.22.04.1) ...
Preparing to unpack .../16-libldap-2.5-0_2.5.16+dfsg-0ubuntu0.22.04.2_amd64.deb ...
Unpacking libldap-2.5-0:amd64 (2.5.16+dfsg-0ubuntu0.22.04.2) over (2.5.16+dfsg-0ubuntu0.22.04.1) ...
Preparing to unpack .../17-libssh-4_0.9.6-2ubuntu0.22.04.3_amd64.deb ...
Unpacking libssh-4:amd64 (0.9.6-2ubuntu0.22.04.3) over (0.9.6-2ubuntu0.22.04.2) ...
Preparing to unpack .../18-unzip_6.0-26ubuntu3.2_amd64.deb ...
Unpacking unzip (6.0-26ubuntu3.2) over (6.0-26ubuntu3.1) ...
Setting up libip4tc2:amd64 (1.8.7-1ubuntu5.2) ...
Setting up tcpdump (4.99.1-3ubuntu0.2) ...
Installing new version of config file /etc/apparmor.d/usr.bin.tcpdump ...
Setting up libip6tc2:amd64 (1.8.7-1ubuntu5.2) ...
Setting up unzip (6.0-26ubuntu3.2) ...
Setting up binutils-common:amd64 (2.38-4ubuntu2.6) ...
Setting up less (590-1ubuntu0.22.04.2) ...
Setting up libctf-nobfd0:amd64 (2.38-4ubuntu2.6) ...
Setting up systemd (249.11-0ubuntu3.12) ...
Setting up libldap-2.5-0:amd64 (2.5.16+dfsg-0ubuntu0.22.04.2) ...
Setting up dns-root-data (2023112702~ubuntu0.22.04.1) ...
Setting up tzdata (2023d-0ubuntu0.22.04) ...

Current default time zone: 'Etc/UTC'
Local time is now:      Wed Feb 28 14:19:56 UTC 2024.
Universal Time is now:  Wed Feb 28 14:19:56 UTC 2024.
Run 'dpkg-reconfigure tzdata' if you wish to change it.

Setting up libunbound8:amd64 (1.13.1-1ubuntu5.4) ...
Setting up libuv1:amd64 (1.43.0-1ubuntu0.1) ...
Setting up udev (249.11-0ubuntu3.12) ...
Setting up libxtables12:amd64 (1.8.7-1ubuntu5.2) ...
Setting up libssh-4:amd64 (0.9.6-2ubuntu0.22.04.3) ...
Setting up exim4-config (4.95-4ubuntu2.5) ...
Setting up libbinutils:amd64 (2.38-4ubuntu2.6) ...
Setting up openssl (3.0.2-0ubuntu1.15) ...
Setting up libxml2:amd64 (2.9.13+dfsg-1ubuntu0.4) ...
Setting up libctf0:amd64 (2.38-4ubuntu2.6) ...
Setting up libgnutls-dane0:amd64 (3.7.3-4ubuntu1.4) ...
Setting up systemd-sysv (249.11-0ubuntu3.12) ...
Setting up exim4-base (4.95-4ubuntu2.5) ...
exim4-base.service is a disabled or a static unit not running, not starting it.
Setting up bind9-libs:amd64 (1:9.18.18-0ubuntu0.22.04.2) ...
Setting up iptables (1.8.7-1ubuntu5.2) ...
Setting up ldap-utils (2.5.16+dfsg-0ubuntu0.22.04.2) ...
Setting up bind9-utils (1:9.18.18-0ubuntu0.22.04.2) ...
Setting up bind9 (1:9.18.18-0ubuntu0.22.04.2) ...
named-resolvconf.service is a disabled or a static unit not running, not starting it.
named.service is a disabled or a static unit not running, not starting it.
Setting up bind9-host (1:9.18.18-0ubuntu0.22.04.2) ...
Setting up exim4-daemon-light (4.95-4ubuntu2.5) ...
Setting up binutils-x86-64-linux-gnu (2.38-4ubuntu2.6) ...
Setting up binutils (2.38-4ubuntu2.6) ...
Setting up exim4 (4.95-4ubuntu2.5) ...
Setting up bind9-dnsutils (1:9.18.18-0ubuntu0.22.04.2) ...
Setting up dnsutils (1:9.18.18-0ubuntu0.22.04.2) ...
Processing triggers for cracklib-runtime (2.9.6-3.4build4) ...
Processing triggers for dbus (1.12.20-2ubuntu4.1) ...
Processing triggers for install-info (6.8-4build1) ...
Processing triggers for mailcap (3.70+nmu1ubuntu1) ...
Processing triggers for initramfs-tools (0.140ubuntu13.4) ...
Processing triggers for libc-bin (2.35-0ubuntu3.6) ...
Processing triggers for man-db (2.10.2-1) ...
root@tcargobr:~# sudo apt install git apache2
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
apache2 is already the newest version (2.4.52-1ubuntu4.7).
The following additional packages will be installed:
  git-man libcurl3-gnutls liberror-perl
Suggested packages:
  git-daemon-run | git-daemon-sysvinit git-doc git-email git-gui gitk gitweb git-cvs
  git-mediawiki git-svn
The following NEW packages will be installed:
  git git-man libcurl3-gnutls liberror-perl
0 upgraded, 4 newly installed, 0 to remove and 0 not upgraded.
Need to get 4430 kB of archives.
After this operation, 21.8 MB of additional disk space will be used.
Do you want to continue? [Y/n] Y
Get:1 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libcurl3-gnutls amd64 7.81.0-1ubuntu1.15 [284 kB]
Get:2 http://archive.ubuntu.com/ubuntu jammy/main amd64 liberror-perl all 0.17029-1 [26.5 kB]    
Get:3 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 git-man all 1:2.34.1-1ubuntu1.10 [954 kB]
Get:4 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 git amd64 1:2.34.1-1ubuntu1.10 [3166 kB]
Fetched 4430 kB in 8s (569 kB/s)                                                                 
Selecting previously unselected package libcurl3-gnutls:amd64.
(Reading database ... 27158 files and directories currently installed.)
Preparing to unpack .../libcurl3-gnutls_7.81.0-1ubuntu1.15_amd64.deb ...
Unpacking libcurl3-gnutls:amd64 (7.81.0-1ubuntu1.15) ...
Selecting previously unselected package liberror-perl.
Preparing to unpack .../liberror-perl_0.17029-1_all.deb ...
Unpacking liberror-perl (0.17029-1) ...
Selecting previously unselected package git-man.
Preparing to unpack .../git-man_1%3a2.34.1-1ubuntu1.10_all.deb ...
Unpacking git-man (1:2.34.1-1ubuntu1.10) ...
Selecting previously unselected package git.
Preparing to unpack .../git_1%3a2.34.1-1ubuntu1.10_amd64.deb ...
Unpacking git (1:2.34.1-1ubuntu1.10) ...
Setting up libcurl3-gnutls:amd64 (7.81.0-1ubuntu1.15) ...
Setting up liberror-perl (0.17029-1) ...
Setting up git-man (1:2.34.1-1ubuntu1.10) ...
Setting up git (1:2.34.1-1ubuntu1.10) ...
Processing triggers for man-db (2.10.2-1) ...
Processing triggers for libc-bin (2.35-0ubuntu3.6) ...
root@tcargobr:~# sudo apt install php libapache2-mod-php php-mysql php-json php-curl php-mbstring php-zip
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
The following additional packages will be installed:
  libapache2-mod-php8.1 libonig5 libzip4 php-common php8.1 php8.1-cli php8.1-common php8.1-curl
  php8.1-mbstring php8.1-mysql php8.1-opcache php8.1-readline php8.1-zip
Suggested packages:
  php-pear
The following NEW packages will be installed:
  libapache2-mod-php libapache2-mod-php8.1 libonig5 libzip4 php php-common php-curl php-json
  php-mbstring php-mysql php-zip php8.1 php8.1-cli php8.1-common php8.1-curl php8.1-mbstring
  php8.1-mysql php8.1-opcache php8.1-readline php8.1-zip
0 upgraded, 20 newly installed, 0 to remove and 0 not upgraded.
Need to get 6048 kB of archives.
After this operation, 24.0 MB of additional disk space will be used.
Do you want to continue? [Y/n] Y
Get:1 http://archive.ubuntu.com/ubuntu jammy/main amd64 php-common all 2:92ubuntu1 [12.4 kB]
Get:2 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1-common amd64 8.1.2-1ubuntu2.14 [1127 kB]
Get:3 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1-opcache amd64 8.1.2-1ubuntu2.14 [365 kB]
Get:4 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1-readline amd64 8.1.2-1ubuntu2.14 [13.6 kB]
Get:5 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1-cli amd64 8.1.2-1ubuntu2.14 [1834 kB]
Get:6 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libapache2-mod-php8.1 amd64 8.1.2-1ubuntu2.14 [1766 kB]
Get:7 http://archive.ubuntu.com/ubuntu jammy/main amd64 libapache2-mod-php all 2:8.1+92ubuntu1 [2898 B]
Get:8 http://archive.ubuntu.com/ubuntu jammy/main amd64 libonig5 amd64 6.9.7.1-2build1 [172 kB]
Get:9 http://archive.ubuntu.com/ubuntu jammy/universe amd64 libzip4 amd64 1.7.3-1ubuntu2 [55.0 kB]
Get:10 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1 all 8.1.2-1ubuntu2.14 [9158 B]
Get:11 http://archive.ubuntu.com/ubuntu jammy/main amd64 php all 2:8.1+92ubuntu1 [2756 B]
Get:12 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1-curl amd64 8.1.2-1ubuntu2.14 [38.7 kB]
Get:13 http://archive.ubuntu.com/ubuntu jammy/main amd64 php-curl all 2:8.1+92ubuntu1 [1834 B]
Get:14 http://archive.ubuntu.com/ubuntu jammy/main amd64 php-json all 2:8.1+92ubuntu1 [1834 B]
Get:15 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1-mbstring amd64 8.1.2-1ubuntu2.14 [483 kB]
Get:16 http://archive.ubuntu.com/ubuntu jammy/universe amd64 php-mbstring all 2:8.1+92ubuntu1 [1844 B]
Get:17 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 php8.1-mysql amd64 8.1.2-1ubuntu2.14 [130 kB]
Get:18 http://archive.ubuntu.com/ubuntu jammy/main amd64 php-mysql all 2:8.1+92ubuntu1 [1834 B]
Get:19 http://archive.ubuntu.com/ubuntu jammy-updates/universe amd64 php8.1-zip amd64 8.1.2-1ubuntu2.14 [27.1 kB]
Get:20 http://archive.ubuntu.com/ubuntu jammy/universe amd64 php-zip all 2:8.1+92ubuntu1 [1830 B]
Fetched 6048 kB in 2s (2947 kB/s)
Selecting previously unselected package php-common.
(Reading database ... 28152 files and directories currently installed.)
Preparing to unpack .../00-php-common_2%3a92ubuntu1_all.deb ...
Unpacking php-common (2:92ubuntu1) ...
Selecting previously unselected package php8.1-common.
Preparing to unpack .../01-php8.1-common_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-common (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php8.1-opcache.
Preparing to unpack .../02-php8.1-opcache_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-opcache (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php8.1-readline.
Preparing to unpack .../03-php8.1-readline_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-readline (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php8.1-cli.
Preparing to unpack .../04-php8.1-cli_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-cli (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package libapache2-mod-php8.1.
Preparing to unpack .../05-libapache2-mod-php8.1_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking libapache2-mod-php8.1 (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package libapache2-mod-php.
Preparing to unpack .../06-libapache2-mod-php_2%3a8.1+92ubuntu1_all.deb ...
Unpacking libapache2-mod-php (2:8.1+92ubuntu1) ...
Selecting previously unselected package libonig5:amd64.
Preparing to unpack .../07-libonig5_6.9.7.1-2build1_amd64.deb ...
Unpacking libonig5:amd64 (6.9.7.1-2build1) ...
Selecting previously unselected package libzip4:amd64.
Preparing to unpack .../08-libzip4_1.7.3-1ubuntu2_amd64.deb ...
Unpacking libzip4:amd64 (1.7.3-1ubuntu2) ...
Selecting previously unselected package php8.1.
Preparing to unpack .../09-php8.1_8.1.2-1ubuntu2.14_all.deb ...
Unpacking php8.1 (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php.
Preparing to unpack .../10-php_2%3a8.1+92ubuntu1_all.deb ...
Unpacking php (2:8.1+92ubuntu1) ...
Selecting previously unselected package php8.1-curl.
Preparing to unpack .../11-php8.1-curl_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-curl (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php-curl.
Preparing to unpack .../12-php-curl_2%3a8.1+92ubuntu1_all.deb ...
Unpacking php-curl (2:8.1+92ubuntu1) ...
Selecting previously unselected package php-json.
Preparing to unpack .../13-php-json_2%3a8.1+92ubuntu1_all.deb ...
Unpacking php-json (2:8.1+92ubuntu1) ...
Selecting previously unselected package php8.1-mbstring.
Preparing to unpack .../14-php8.1-mbstring_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-mbstring (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php-mbstring.
Preparing to unpack .../15-php-mbstring_2%3a8.1+92ubuntu1_all.deb ...
Unpacking php-mbstring (2:8.1+92ubuntu1) ...
Selecting previously unselected package php8.1-mysql.
Preparing to unpack .../16-php8.1-mysql_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-mysql (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php-mysql.
Preparing to unpack .../17-php-mysql_2%3a8.1+92ubuntu1_all.deb ...
Unpacking php-mysql (2:8.1+92ubuntu1) ...
Selecting previously unselected package php8.1-zip.
Preparing to unpack .../18-php8.1-zip_8.1.2-1ubuntu2.14_amd64.deb ...
Unpacking php8.1-zip (8.1.2-1ubuntu2.14) ...
Selecting previously unselected package php-zip.
Preparing to unpack .../19-php-zip_2%3a8.1+92ubuntu1_all.deb ...
Unpacking php-zip (2:8.1+92ubuntu1) ...
Setting up php-common (2:92ubuntu1) ...
Created symlink /etc/systemd/system/timers.target.wants/phpsessionclean.timer → /lib/systemd/system/phpsessionclean.timer.
Setting up php8.1-common (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/mods-available/calendar.ini with new version

Creating config file /etc/php/8.1/mods-available/ctype.ini with new version

Creating config file /etc/php/8.1/mods-available/exif.ini with new version

Creating config file /etc/php/8.1/mods-available/fileinfo.ini with new version

Creating config file /etc/php/8.1/mods-available/ffi.ini with new version

Creating config file /etc/php/8.1/mods-available/ftp.ini with new version

Creating config file /etc/php/8.1/mods-available/gettext.ini with new version

Creating config file /etc/php/8.1/mods-available/iconv.ini with new version

Creating config file /etc/php/8.1/mods-available/pdo.ini with new version

Creating config file /etc/php/8.1/mods-available/phar.ini with new version

Creating config file /etc/php/8.1/mods-available/posix.ini with new version

Creating config file /etc/php/8.1/mods-available/shmop.ini with new version

Creating config file /etc/php/8.1/mods-available/sockets.ini with new version

Creating config file /etc/php/8.1/mods-available/sysvmsg.ini with new version

Creating config file /etc/php/8.1/mods-available/sysvsem.ini with new version

Creating config file /etc/php/8.1/mods-available/sysvshm.ini with new version

Creating config file /etc/php/8.1/mods-available/tokenizer.ini with new version
Setting up libzip4:amd64 (1.7.3-1ubuntu2) ...
Setting up php8.1-curl (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/mods-available/curl.ini with new version
Setting up php8.1-mysql (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/mods-available/mysqlnd.ini with new version

Creating config file /etc/php/8.1/mods-available/mysqli.ini with new version

Creating config file /etc/php/8.1/mods-available/pdo_mysql.ini with new version
Setting up php8.1-zip (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/mods-available/zip.ini with new version
Setting up php8.1-readline (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/mods-available/readline.ini with new version
Setting up php8.1-opcache (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/mods-available/opcache.ini with new version
Setting up libonig5:amd64 (6.9.7.1-2build1) ...
Setting up php-curl (2:8.1+92ubuntu1) ...
Setting up php-mysql (2:8.1+92ubuntu1) ...
Setting up php8.1-mbstring (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/mods-available/mbstring.ini with new version
Setting up php-mbstring (2:8.1+92ubuntu1) ...
Setting up php8.1-cli (8.1.2-1ubuntu2.14) ...
update-alternatives: using /usr/bin/php8.1 to provide /usr/bin/php (php) in auto mode
update-alternatives: using /usr/bin/phar8.1 to provide /usr/bin/phar (phar) in auto mode
update-alternatives: using /usr/bin/phar.phar8.1 to provide /usr/bin/phar.phar (phar.phar) in auto mode

Creating config file /etc/php/8.1/cli/php.ini with new version
Setting up php-zip (2:8.1+92ubuntu1) ...
Setting up libapache2-mod-php8.1 (8.1.2-1ubuntu2.14) ...

Creating config file /etc/php/8.1/apache2/php.ini with new version
Module mpm_event disabled.
Enabling module mpm_prefork.
apache2_switch_mpm Switch to prefork
apache2_invoke: Enable module php8.1
Setting up php-json (2:8.1+92ubuntu1) ...
Setting up php8.1 (8.1.2-1ubuntu2.14) ...
Setting up libapache2-mod-php (2:8.1+92ubuntu1) ...
Setting up php (2:8.1+92ubuntu1) ...
Processing triggers for man-db (2.10.2-1) ...
Processing triggers for libc-bin (2.35-0ubuntu3.6) ...
Processing triggers for php8.1-cli (8.1.2-1ubuntu2.14) ...
Processing triggers for libapache2-mod-php8.1 (8.1.2-1ubuntu2.14) ...
root@tcargobr:~# sudo add-apt-repository ppa:ondrej/php
sudo: add-apt-repository: command not found
root@tcargobr:~# sudo apt update && apt upgrade -y
Hit:1 http://archive.ubuntu.com/ubuntu jammy InRelease                                           
Hit:2 http://archive.ubuntu.com/ubuntu jammy-updates InRelease                                   
Hit:3 http://security.ubuntu.com/ubuntu jammy-security InRelease
Hit:4 http://archive.canonical.com/ubuntu jammy InRelease
Reading package lists... Done                 
Building dependency tree... Done
Reading state information... Done
All packages are up to date.
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
Calculating upgrade... Done
0 upgraded, 0 newly installed, 0 to remove and 0 not upgraded.
root@tcargobr:~# sudo add-apt-repository ppa:ondrej/php
sudo: add-apt-repository: command not found
root@tcargobr:~# add-apt-repository ppa:ondrej/php
-bash: add-apt-repository: command not found
root@tcargobr:~# sudo apt update && sudo apt install software-properties-common
Hit:1 http://archive.canonical.com/ubuntu jammy InRelease                                        
Hit:2 http://archive.ubuntu.com/ubuntu jammy InRelease                                           
Hit:3 http://archive.ubuntu.com/ubuntu jammy-updates InRelease
Hit:4 http://security.ubuntu.com/ubuntu jammy-security InRelease
Reading package lists... Done                               
Building dependency tree... Done
Reading state information... Done
All packages are up to date.
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
The following additional packages will be installed:
  gir1.2-glib-2.0 gir1.2-packagekitglib-1.0 iso-codes libappstream4 libdw1 libgirepository-1.0-1
  libglib2.0-0 libglib2.0-bin libglib2.0-data libgstreamer1.0-0 libpackagekit-glib2-18
  libpam-systemd libpolkit-agent-1-0 libpolkit-gobject-1-0 libstemmer0d libunwind8 libxmlb2
  libyaml-0-2 packagekit packagekit-tools pkexec policykit-1 polkitd python-apt-common
  python3-apt python3-blinker python3-cffi-backend python3-cryptography python3-dbus
  python3-distro python3-distro-info python3-gi python3-httplib2 python3-importlib-metadata
  python3-jeepney python3-jwt python3-keyring python3-launchpadlib python3-lazr.restfulclient
  python3-lazr.uri python3-more-itertools python3-oauthlib python3-pkg-resources
  python3-pyparsing python3-secretstorage python3-six python3-software-properties
  python3-wadllib python3-zipp shared-mime-info unattended-upgrades xdg-user-dirs
Suggested packages:
  isoquery gstreamer1.0-tools appstream python3-apt-dbg python-apt-doc python-blinker-doc
  python-cryptography-doc python3-cryptography-vectors python-dbus-doc python3-crypto
  gir1.2-secret-1 gnome-keyring libkf5wallet-bin python3-keyrings.alt python3-testresources
  python3-setuptools python-pyparsing-doc python-secretstorage-doc bsd-mailx needrestart
  powermgmt-base
The following NEW packages will be installed:
  gir1.2-glib-2.0 gir1.2-packagekitglib-1.0 iso-codes libappstream4 libdw1 libgirepository-1.0-1
  libglib2.0-0 libglib2.0-bin libglib2.0-data libgstreamer1.0-0 libpackagekit-glib2-18
  libpam-systemd libpolkit-agent-1-0 libpolkit-gobject-1-0 libstemmer0d libunwind8 libxmlb2
  libyaml-0-2 packagekit packagekit-tools pkexec policykit-1 polkitd python-apt-common
  python3-apt python3-blinker python3-cffi-backend python3-cryptography python3-dbus
  python3-distro python3-distro-info python3-gi python3-httplib2 python3-importlib-metadata
  python3-jeepney python3-jwt python3-keyring python3-launchpadlib python3-lazr.restfulclient
  python3-lazr.uri python3-more-itertools python3-oauthlib python3-pkg-resources
  python3-pyparsing python3-secretstorage python3-six python3-software-properties
  python3-wadllib python3-zipp shared-mime-info software-properties-common unattended-upgrades
  xdg-user-dirs
0 upgraded, 53 newly installed, 0 to remove and 0 not upgraded.
Need to get 10.2 MB of archives.
After this operation, 48.7 MB of additional disk space will be used.
Do you want to continue? [Y/n] Y
Get:1 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libglib2.0-0 amd64 2.72.4-0ubuntu2.2 [1463 kB]
Get:2 http://archive.ubuntu.com/ubuntu jammy/main amd64 libgirepository-1.0-1 amd64 1.72.0-1 [55.6 kB]
Get:3 http://archive.ubuntu.com/ubuntu jammy/main amd64 gir1.2-glib-2.0 amd64 1.72.0-1 [164 kB]
Get:4 http://archive.ubuntu.com/ubuntu jammy/main amd64 iso-codes all 4.9.0-1 [3459 kB]
Get:5 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libglib2.0-data all 2.72.4-0ubuntu2.2 [4612 B]
Get:6 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libpam-systemd amd64 249.11-0ubuntu3.12 [203 kB]
Get:7 http://archive.ubuntu.com/ubuntu jammy/main amd64 libyaml-0-2 amd64 0.2.2-1build2 [51.6 kB]
Get:8 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python-apt-common all 2.4.0ubuntu3 [14.6 kB]
Get:9 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-apt amd64 2.4.0ubuntu3 [164 kB]
Get:10 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-dbus amd64 1.2.18-3build1 [99.5 kB]
Get:11 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-gi amd64 3.42.1-0ubuntu1 [229 kB]
Get:12 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-pkg-resources all 59.6.0-1.2ubuntu0.22.04.1 [132 kB]
Get:13 http://archive.ubuntu.com/ubuntu jammy/main amd64 shared-mime-info amd64 2.1-2 [454 kB]
Get:14 http://archive.ubuntu.com/ubuntu jammy/main amd64 xdg-user-dirs amd64 0.17-2ubuntu4 [53.9 kB]
Get:15 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-distro-info all 1.1ubuntu0.2 [6554 B]
Get:16 http://archive.ubuntu.com/ubuntu jammy/main amd64 libpackagekit-glib2-18 amd64 1.2.5-2ubuntu2 [123 kB]
Get:17 http://archive.ubuntu.com/ubuntu jammy/main amd64 gir1.2-packagekitglib-1.0 amd64 1.2.5-2ubuntu2 [25.3 kB]
Get:18 http://archive.ubuntu.com/ubuntu jammy/main amd64 libstemmer0d amd64 2.2.0-1build1 [165 kB]
Get:19 http://archive.ubuntu.com/ubuntu jammy/main amd64 libxmlb2 amd64 0.3.6-2build1 [67.8 kB]
Get:20 http://archive.ubuntu.com/ubuntu jammy/main amd64 libappstream4 amd64 0.15.2-2 [192 kB]
Get:21 http://archive.ubuntu.com/ubuntu jammy/main amd64 libdw1 amd64 0.186-1build1 [250 kB]
Get:22 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libglib2.0-bin amd64 2.72.4-0ubuntu2.2 [80.9 kB]
Get:23 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libunwind8 amd64 1.3.2-2build2.1 [54.5 kB]
Get:24 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 libgstreamer1.0-0 amd64 1.20.3-0ubuntu1 [983 kB]
Get:25 http://archive.ubuntu.com/ubuntu jammy/main amd64 libpolkit-gobject-1-0 amd64 0.105-33 [43.2 kB]
Get:26 http://archive.ubuntu.com/ubuntu jammy/main amd64 libpolkit-agent-1-0 amd64 0.105-33 [16.8 kB]
Get:27 http://archive.ubuntu.com/ubuntu jammy/main amd64 polkitd amd64 0.105-33 [80.0 kB]
Get:28 http://archive.ubuntu.com/ubuntu jammy/main amd64 pkexec amd64 0.105-33 [15.2 kB]
Get:29 http://archive.ubuntu.com/ubuntu jammy/main amd64 policykit-1 amd64 0.105-33 [2426 B]
Get:30 http://archive.ubuntu.com/ubuntu jammy/main amd64 packagekit amd64 1.2.5-2ubuntu2 [442 kB]
Get:31 http://archive.ubuntu.com/ubuntu jammy/main amd64 packagekit-tools amd64 1.2.5-2ubuntu2 [28.8 kB]
Get:32 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-blinker all 1.4+dfsg1-0.4 [14.0 kB]
Get:33 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-cffi-backend amd64 1.15.0-1build2 [77.4 kB]
Get:34 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-cryptography amd64 3.4.8-1ubuntu2.1 [236 kB]
Get:35 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-pyparsing all 2.4.7-1 [61.4 kB]
Get:36 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-httplib2 all 0.20.2-2 [30.4 kB]
Get:37 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-more-itertools all 8.10.0-2 [47.9 kB]
Get:38 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-zipp all 1.0.0-3 [5440 B]
Get:39 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-importlib-metadata all 4.6.4-1 [16.2 kB]
Get:40 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-jeepney all 0.7.1-3 [36.8 kB]
Get:41 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-jwt all 2.3.0-1ubuntu0.2 [17.1 kB]
Get:42 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-secretstorage all 3.3.1-1 [13.2 kB]
Get:43 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-keyring all 23.5.0-1 [35.7 kB]
Get:44 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-lazr.uri all 1.0.6-2 [14.4 kB]
Get:45 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-wadllib all 1.3.6-1 [36.4 kB]
Get:46 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-distro all 1.7.0-1 [17.0 kB]
Get:47 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-oauthlib all 3.2.0-1ubuntu0.1 [89.9 kB]
Get:48 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-six all 1.16.0-3ubuntu1 [12.6 kB]
Get:49 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-lazr.restfulclient all 0.14.4-1 [51.2 kB]
Get:50 http://archive.ubuntu.com/ubuntu jammy/main amd64 python3-launchpadlib all 1.10.16-1 [125 kB]
Get:51 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 python3-software-properties all 0.99.22.9 [28.8 kB]
Get:52 http://archive.ubuntu.com/ubuntu jammy-updates/main amd64 software-properties-common all 0.99.22.9 [14.1 kB]
Get:53 http://archive.ubuntu.com/ubuntu jammy/main amd64 unattended-upgrades all 2.8ubuntu1 [49.4 kB]
Fetched 10.2 MB in 3s (3337 kB/s)          
Extracting templates from packages: 100%
Preconfiguring packages ...
Selecting previously unselected package libglib2.0-0:amd64.
(Reading database ... 28351 files and directories currently installed.)
Preparing to unpack .../00-libglib2.0-0_2.72.4-0ubuntu2.2_amd64.deb ...
Unpacking libglib2.0-0:amd64 (2.72.4-0ubuntu2.2) ...
Selecting previously unselected package libgirepository-1.0-1:amd64.
Preparing to unpack .../01-libgirepository-1.0-1_1.72.0-1_amd64.deb ...
Unpacking libgirepository-1.0-1:amd64 (1.72.0-1) ...
Selecting previously unselected package gir1.2-glib-2.0:amd64.
Preparing to unpack .../02-gir1.2-glib-2.0_1.72.0-1_amd64.deb ...
Unpacking gir1.2-glib-2.0:amd64 (1.72.0-1) ...
Selecting previously unselected package iso-codes.
Preparing to unpack .../03-iso-codes_4.9.0-1_all.deb ...
Unpacking iso-codes (4.9.0-1) ...
Selecting previously unselected package libglib2.0-data.
Preparing to unpack .../04-libglib2.0-data_2.72.4-0ubuntu2.2_all.deb ...
Unpacking libglib2.0-data (2.72.4-0ubuntu2.2) ...
Selecting previously unselected package libpam-systemd:amd64.
Preparing to unpack .../05-libpam-systemd_249.11-0ubuntu3.12_amd64.deb ...
Unpacking libpam-systemd:amd64 (249.11-0ubuntu3.12) ...
Selecting previously unselected package libyaml-0-2:amd64.
Preparing to unpack .../06-libyaml-0-2_0.2.2-1build2_amd64.deb ...
Unpacking libyaml-0-2:amd64 (0.2.2-1build2) ...
Selecting previously unselected package python-apt-common.
Preparing to unpack .../07-python-apt-common_2.4.0ubuntu3_all.deb ...
Unpacking python-apt-common (2.4.0ubuntu3) ...
Selecting previously unselected package python3-apt.
Preparing to unpack .../08-python3-apt_2.4.0ubuntu3_amd64.deb ...
Unpacking python3-apt (2.4.0ubuntu3) ...
Selecting previously unselected package python3-dbus.
Preparing to unpack .../09-python3-dbus_1.2.18-3build1_amd64.deb ...
Unpacking python3-dbus (1.2.18-3build1) ...
Selecting previously unselected package python3-gi.
Preparing to unpack .../10-python3-gi_3.42.1-0ubuntu1_amd64.deb ...
Unpacking python3-gi (3.42.1-0ubuntu1) ...
Selecting previously unselected package python3-pkg-resources.
Preparing to unpack .../11-python3-pkg-resources_59.6.0-1.2ubuntu0.22.04.1_all.deb ...
Unpacking python3-pkg-resources (59.6.0-1.2ubuntu0.22.04.1) ...
Selecting previously unselected package shared-mime-info.
Preparing to unpack .../12-shared-mime-info_2.1-2_amd64.deb ...
Unpacking shared-mime-info (2.1-2) ...
Selecting previously unselected package xdg-user-dirs.
Preparing to unpack .../13-xdg-user-dirs_0.17-2ubuntu4_amd64.deb ...
Unpacking xdg-user-dirs (0.17-2ubuntu4) ...
Selecting previously unselected package python3-distro-info.
Preparing to unpack .../14-python3-distro-info_1.1ubuntu0.2_all.deb ...
Unpacking python3-distro-info (1.1ubuntu0.2) ...
Selecting previously unselected package libpackagekit-glib2-18:amd64.
Preparing to unpack .../15-libpackagekit-glib2-18_1.2.5-2ubuntu2_amd64.deb ...
Unpacking libpackagekit-glib2-18:amd64 (1.2.5-2ubuntu2) ...
Selecting previously unselected package gir1.2-packagekitglib-1.0.
Preparing to unpack .../16-gir1.2-packagekitglib-1.0_1.2.5-2ubuntu2_amd64.deb ...
Unpacking gir1.2-packagekitglib-1.0 (1.2.5-2ubuntu2) ...
Selecting previously unselected package libstemmer0d:amd64.
Preparing to unpack .../17-libstemmer0d_2.2.0-1build1_amd64.deb ...
Unpacking libstemmer0d:amd64 (2.2.0-1build1) ...
Selecting previously unselected package libxmlb2:amd64.
Preparing to unpack .../18-libxmlb2_0.3.6-2build1_amd64.deb ...
Unpacking libxmlb2:amd64 (0.3.6-2build1) ...
Selecting previously unselected package libappstream4:amd64.
Preparing to unpack .../19-libappstream4_0.15.2-2_amd64.deb ...
Unpacking libappstream4:amd64 (0.15.2-2) ...
Selecting previously unselected package libdw1:amd64.
Preparing to unpack .../20-libdw1_0.186-1build1_amd64.deb ...
Unpacking libdw1:amd64 (0.186-1build1) ...
Selecting previously unselected package libglib2.0-bin.
Preparing to unpack .../21-libglib2.0-bin_2.72.4-0ubuntu2.2_amd64.deb ...
Unpacking libglib2.0-bin (2.72.4-0ubuntu2.2) ...
Selecting previously unselected package libunwind8:amd64.
Preparing to unpack .../22-libunwind8_1.3.2-2build2.1_amd64.deb ...
Unpacking libunwind8:amd64 (1.3.2-2build2.1) ...
Selecting previously unselected package libgstreamer1.0-0:amd64.
Preparing to unpack .../23-libgstreamer1.0-0_1.20.3-0ubuntu1_amd64.deb ...
Unpacking libgstreamer1.0-0:amd64 (1.20.3-0ubuntu1) ...
Selecting previously unselected package libpolkit-gobject-1-0:amd64.
Preparing to unpack .../24-libpolkit-gobject-1-0_0.105-33_amd64.deb ...
Unpacking libpolkit-gobject-1-0:amd64 (0.105-33) ...
Selecting previously unselected package libpolkit-agent-1-0:amd64.
Preparing to unpack .../25-libpolkit-agent-1-0_0.105-33_amd64.deb ...
Unpacking libpolkit-agent-1-0:amd64 (0.105-33) ...
Selecting previously unselected package polkitd.
Preparing to unpack .../26-polkitd_0.105-33_amd64.deb ...
Unpacking polkitd (0.105-33) ...
Selecting previously unselected package pkexec.
Preparing to unpack .../27-pkexec_0.105-33_amd64.deb ...
Unpacking pkexec (0.105-33) ...
Selecting previously unselected package policykit-1.
Preparing to unpack .../28-policykit-1_0.105-33_amd64.deb ...
Unpacking policykit-1 (0.105-33) ...
Selecting previously unselected package packagekit.
Preparing to unpack .../29-packagekit_1.2.5-2ubuntu2_amd64.deb ...
Unpacking packagekit (1.2.5-2ubuntu2) ...
Selecting previously unselected package packagekit-tools.
Preparing to unpack .../30-packagekit-tools_1.2.5-2ubuntu2_amd64.deb ...
Unpacking packagekit-tools (1.2.5-2ubuntu2) ...
Selecting previously unselected package python3-blinker.
Preparing to unpack .../31-python3-blinker_1.4+dfsg1-0.4_all.deb ...
Unpacking python3-blinker (1.4+dfsg1-0.4) ...
Selecting previously unselected package python3-cffi-backend:amd64.
Preparing to unpack .../32-python3-cffi-backend_1.15.0-1build2_amd64.deb ...
Unpacking python3-cffi-backend:amd64 (1.15.0-1build2) ...
Selecting previously unselected package python3-cryptography.
Preparing to unpack .../33-python3-cryptography_3.4.8-1ubuntu2.1_amd64.deb ...
Unpacking python3-cryptography (3.4.8-1ubuntu2.1) ...
Selecting previously unselected package python3-pyparsing.
Preparing to unpack .../34-python3-pyparsing_2.4.7-1_all.deb ...
Unpacking python3-pyparsing (2.4.7-1) ...
Selecting previously unselected package python3-httplib2.
Preparing to unpack .../35-python3-httplib2_0.20.2-2_all.deb ...
Unpacking python3-httplib2 (0.20.2-2) ...
Selecting previously unselected package python3-more-itertools.
Preparing to unpack .../36-python3-more-itertools_8.10.0-2_all.deb ...
Unpacking python3-more-itertools (8.10.0-2) ...
Selecting previously unselected package python3-zipp.
Preparing to unpack .../37-python3-zipp_1.0.0-3_all.deb ...
Unpacking python3-zipp (1.0.0-3) ...
Selecting previously unselected package python3-importlib-metadata.
Preparing to unpack .../38-python3-importlib-metadata_4.6.4-1_all.deb ...
Unpacking python3-importlib-metadata (4.6.4-1) ...
Selecting previously unselected package python3-jeepney.
Preparing to unpack .../39-python3-jeepney_0.7.1-3_all.deb ...
Unpacking python3-jeepney (0.7.1-3) ...
Selecting previously unselected package python3-jwt.
Preparing to unpack .../40-python3-jwt_2.3.0-1ubuntu0.2_all.deb ...
Unpacking python3-jwt (2.3.0-1ubuntu0.2) ...
Selecting previously unselected package python3-secretstorage.
Preparing to unpack .../41-python3-secretstorage_3.3.1-1_all.deb ...
Unpacking python3-secretstorage (3.3.1-1) ...
Selecting previously unselected package python3-keyring.
Preparing to unpack .../42-python3-keyring_23.5.0-1_all.deb ...
Unpacking python3-keyring (23.5.0-1) ...
Selecting previously unselected package python3-lazr.uri.
Preparing to unpack .../43-python3-lazr.uri_1.0.6-2_all.deb ...
Unpacking python3-lazr.uri (1.0.6-2) ...
Selecting previously unselected package python3-wadllib.
Preparing to unpack .../44-python3-wadllib_1.3.6-1_all.deb ...
Unpacking python3-wadllib (1.3.6-1) ...
Selecting previously unselected package python3-distro.
Preparing to unpack .../45-python3-distro_1.7.0-1_all.deb ...
Unpacking python3-distro (1.7.0-1) ...
Selecting previously unselected package python3-oauthlib.
Preparing to unpack .../46-python3-oauthlib_3.2.0-1ubuntu0.1_all.deb ...
Unpacking python3-oauthlib (3.2.0-1ubuntu0.1) ...
Selecting previously unselected package python3-six.
Preparing to unpack .../47-python3-six_1.16.0-3ubuntu1_all.deb ...
Unpacking python3-six (1.16.0-3ubuntu1) ...
Selecting previously unselected package python3-lazr.restfulclient.
Preparing to unpack .../48-python3-lazr.restfulclient_0.14.4-1_all.deb ...
Unpacking python3-lazr.restfulclient (0.14.4-1) ...
Selecting previously unselected package python3-launchpadlib.
Preparing to unpack .../49-python3-launchpadlib_1.10.16-1_all.deb ...
Unpacking python3-launchpadlib (1.10.16-1) ...
Selecting previously unselected package python3-software-properties.
Preparing to unpack .../50-python3-software-properties_0.99.22.9_all.deb ...
Unpacking python3-software-properties (0.99.22.9) ...
Selecting previously unselected package software-properties-common.
Preparing to unpack .../51-software-properties-common_0.99.22.9_all.deb ...
Unpacking software-properties-common (0.99.22.9) ...
Selecting previously unselected package unattended-upgrades.
Preparing to unpack .../52-unattended-upgrades_2.8ubuntu1_all.deb ...
Unpacking unattended-upgrades (2.8ubuntu1) ...
Setting up python3-pkg-resources (59.6.0-1.2ubuntu0.22.04.1) ...
Setting up python3-more-itertools (8.10.0-2) ...
Setting up libdw1:amd64 (0.186-1build1) ...
Setting up python3-distro (1.7.0-1) ...
Setting up xdg-user-dirs (0.17-2ubuntu4) ...
Setting up python3-jwt (2.3.0-1ubuntu0.2) ...
Setting up libyaml-0-2:amd64 (0.2.2-1build2) ...
Setting up libglib2.0-0:amd64 (2.72.4-0ubuntu2.2) ...
No schema files found: doing nothing.
Setting up libxmlb2:amd64 (0.3.6-2build1) ...
Setting up libpackagekit-glib2-18:amd64 (1.2.5-2ubuntu2) ...
Setting up python3-lazr.uri (1.0.6-2) ...
Setting up python3-zipp (1.0.0-3) ...
Setting up libunwind8:amd64 (1.3.2-2build2.1) ...
Setting up python3-six (1.16.0-3ubuntu1) ...
Setting up libglib2.0-data (2.72.4-0ubuntu2.2) ...
Setting up python3-pyparsing (2.4.7-1) ...
Setting up python3-wadllib (1.3.6-1) ...
Setting up shared-mime-info (2.1-2) ...
Setting up python3-jeepney (0.7.1-3) ...
Setting up python-apt-common (2.4.0ubuntu3) ...
Setting up python3-httplib2 (0.20.2-2) ...
Setting up libpam-systemd:amd64 (249.11-0ubuntu3.12) ...
Setting up libgirepository-1.0-1:amd64 (1.72.0-1) ...
Setting up libstemmer0d:amd64 (2.2.0-1build1) ...
Setting up python3-distro-info (1.1ubuntu0.2) ...
Setting up iso-codes (4.9.0-1) ...
Setting up libpolkit-gobject-1-0:amd64 (0.105-33) ...
Setting up libgstreamer1.0-0:amd64 (1.20.3-0ubuntu1) ...
Setcap worked! gst-ptp-helper is not suid!
Setting up python3-cffi-backend:amd64 (1.15.0-1build2) ...
Setting up python3-blinker (1.4+dfsg1-0.4) ...
Setting up python3-dbus (1.2.18-3build1) ...
Setting up python3-importlib-metadata (4.6.4-1) ...
Setting up python3-apt (2.4.0ubuntu3) ...
Setting up libglib2.0-bin (2.72.4-0ubuntu2.2) ...
Setting up libappstream4:amd64 (0.15.2-2) ...
Setting up unattended-upgrades (2.8ubuntu1) ...

Creating config file /etc/apt/apt.conf.d/20auto-upgrades with new version

Creating config file /etc/apt/apt.conf.d/50unattended-upgrades with new version
Created symlink /etc/systemd/system/multi-user.target.wants/unattended-upgrades.service → /lib/systemd/system/unattended-upgrades.service.
Synchronizing state of unattended-upgrades.service with SysV service script with /lib/systemd/systemd-sysv-install.
Executing: /lib/systemd/systemd-sysv-install enable unattended-upgrades
Setting up python3-cryptography (3.4.8-1ubuntu2.1) ...
Setting up gir1.2-glib-2.0:amd64 (1.72.0-1) ...
Setting up libpolkit-agent-1-0:amd64 (0.105-33) ...
Setting up polkitd (0.105-33) ...
Setting up pkexec (0.105-33) ...
Setting up gir1.2-packagekitglib-1.0 (1.2.5-2ubuntu2) ...
Setting up python3-oauthlib (3.2.0-1ubuntu0.1) ...
Setting up python3-secretstorage (3.3.1-1) ...
Setting up python3-gi (3.42.1-0ubuntu1) ...
Setting up python3-keyring (23.5.0-1) ...
Setting up python3-lazr.restfulclient (0.14.4-1) ...
Setting up policykit-1 (0.105-33) ...
Setting up python3-launchpadlib (1.10.16-1) ...
Setting up python3-software-properties (0.99.22.9) ...
Setting up packagekit (1.2.5-2ubuntu2) ...
Created symlink /etc/systemd/user/sockets.target.wants/pk-debconf-helper.socket → /usr/lib/systemd/user/pk-debconf-helper.socket.
Setting up packagekit-tools (1.2.5-2ubuntu2) ...
Setting up software-properties-common (0.99.22.9) ...
Processing triggers for libc-bin (2.35-0ubuntu3.6) ...
Processing triggers for man-db (2.10.2-1) ...
Processing triggers for dbus (1.12.20-2ubuntu4.1) ...
root@tcargobr:~# apt update
Hit:1 http://security.ubuntu.com/ubuntu jammy-security InRelease                                 
Hit:2 http://archive.ubuntu.com/ubuntu jammy InRelease                                           
Hit:3 http://archive.ubuntu.com/ubuntu jammy-updates InRelease             
Hit:4 http://archive.canonical.com/ubuntu jammy InRelease
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
All packages are up to date.
root@tcargobr:~# apt install php8.2 -y

Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
E: Unable to locate package php8.2
E: Couldn't find any package by glob 'php8.2'
root@tcargobr:~# php --version
PHP 8.1.2-1ubuntu2.14 (cli) (built: Aug 18 2023 11:41:11) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.2, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.2-1ubuntu2.14, Copyright (c), by Zend Technologies
root@tcargobr:~# add-apt-repository ppa:ondrej/php
PPA publishes dbgsym, you may need to include 'main/debug' component
Repository: 'deb https://ppa.launchpadcontent.net/ondrej/php/ubuntu/ jammy main'
Description:
Co-installable PHP versions: PHP 5.6, PHP 7.x, PHP 8.x and most requested extensions are included. Only Supported Versions of PHP (http://php.net/supported-versions.php) for Supported Ubuntu Releases (https://wiki.ubuntu.com/Releases) are provided. Don't ask for end-of-life PHP versions or Ubuntu release, they won't be provided.

Debian oldstable and stable packages are provided as well: https://deb.sury.org/#debian-dpa

You can get more information about the packages at https://deb.sury.org

IMPORTANT: The <foo>-backports is now required on older Ubuntu releases.

BUGS&FEATURES: This PPA now has a issue tracker:
https://deb.sury.org/#bug-reporting

CAVEATS:
1. If you are using php-gearman, you need to add ppa:ondrej/pkg-gearman
2. If you are using apache2, you are advised to add ppa:ondrej/apache2
3. If you are using nginx, you are advised to add ppa:ondrej/nginx-mainline
   or ppa:ondrej/nginx

PLEASE READ: If you like my work and want to give me a little motivation, please consider donating regularly: https://donate.sury.org/

WARNING: add-apt-repository is broken with non-UTF-8 locales, see
https://github.com/oerdnj/deb.sury.org/issues/56 for workaround:

# LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
More info: https://launchpad.net/~ondrej/+archive/ubuntu/php
Adding repository.
Press [ENTER] to continue or Ctrl-c to cancel.
Adding deb entry to /etc/apt/sources.list.d/ondrej-ubuntu-php-jammy.list
Adding disabled deb-src entry to /etc/apt/sources.list.d/ondrej-ubuntu-php-jammy.list
Adding key to /etc/apt/trusted.gpg.d/ondrej-ubuntu-php.gpg with fingerprint 14AA40EC0831756756D7F66C4F4EA0AAE5267A6C
Hit:1 http://security.ubuntu.com/ubuntu jammy-security InRelease                                 
Hit:2 http://archive.ubuntu.com/ubuntu jammy InRelease                                           
Hit:3 http://archive.canonical.com/ubuntu jammy InRelease                                        
Hit:4 http://archive.ubuntu.com/ubuntu jammy-updates InRelease                        
Get:5 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy InRelease [23.9 kB]    
Get:6 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 Packages [122 kB]
Get:7 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main Translation-en [37.5 kB]
Fetched 183 kB in 3s (69.8 kB/s)            
Reading package lists... Done
root@tcargobr:~# apt install php8.2 -y
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
The following additional packages will be installed:
  libapache2-mod-php8.2 php8.2-cli php8.2-common php8.2-opcache php8.2-readline
Suggested packages:
  php-pear
The following NEW packages will be installed:
  libapache2-mod-php8.2 php8.2 php8.2-cli php8.2-common php8.2-opcache php8.2-readline
0 upgraded, 6 newly installed, 0 to remove and 21 not upgraded.
Need to get 4854 kB of archives.
After this operation, 21.2 MB of additional disk space will be used.
Get:1 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 php8.2-common amd64 8.2.15-1+ubuntu22.04.1+deb.sury.org+1 [725 kB]
Get:2 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 php8.2-opcache amd64 8.2.15-1+ubuntu22.04.1+deb.sury.org+1 [370 kB]
Get:3 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 php8.2-readline amd64 8.2.15-1+ubuntu22.04.1+deb.sury.org+1 [13.5 kB]
Get:4 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 php8.2-cli amd64 8.2.15-1+ubuntu22.04.1+deb.sury.org+1 [1887 kB]
Get:5 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 libapache2-mod-php8.2 amd64 8.2.15-1+ubuntu22.04.1+deb.sury.org+1 [1819 kB]
Get:6 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 php8.2 all 8.2.15-1+ubuntu22.04.1+deb.sury.org+1 [38.2 kB]
Fetched 4854 kB in 13s (379 kB/s)                                                                
Selecting previously unselected package php8.2-common.
(Reading database ... 30905 files and directories currently installed.)
Preparing to unpack .../0-php8.2-common_8.2.15-1+ubuntu22.04.1+deb.sury.org+1_amd64.deb ...
Unpacking php8.2-common (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Selecting previously unselected package php8.2-opcache.
Preparing to unpack .../1-php8.2-opcache_8.2.15-1+ubuntu22.04.1+deb.sury.org+1_amd64.deb ...
Unpacking php8.2-opcache (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Selecting previously unselected package php8.2-readline.
Preparing to unpack .../2-php8.2-readline_8.2.15-1+ubuntu22.04.1+deb.sury.org+1_amd64.deb ...
Unpacking php8.2-readline (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Selecting previously unselected package php8.2-cli.
Preparing to unpack .../3-php8.2-cli_8.2.15-1+ubuntu22.04.1+deb.sury.org+1_amd64.deb ...
Unpacking php8.2-cli (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Selecting previously unselected package libapache2-mod-php8.2.
Preparing to unpack .../4-libapache2-mod-php8.2_8.2.15-1+ubuntu22.04.1+deb.sury.org+1_amd64.deb ...
Unpacking libapache2-mod-php8.2 (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Selecting previously unselected package php8.2.
Preparing to unpack .../5-php8.2_8.2.15-1+ubuntu22.04.1+deb.sury.org+1_all.deb ...
Unpacking php8.2 (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Setting up php8.2-common (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...

Creating config file /etc/php/8.2/mods-available/calendar.ini with new version

Creating config file /etc/php/8.2/mods-available/ctype.ini with new version

Creating config file /etc/php/8.2/mods-available/exif.ini with new version

Creating config file /etc/php/8.2/mods-available/fileinfo.ini with new version

Creating config file /etc/php/8.2/mods-available/ffi.ini with new version

Creating config file /etc/php/8.2/mods-available/ftp.ini with new version

Creating config file /etc/php/8.2/mods-available/gettext.ini with new version

Creating config file /etc/php/8.2/mods-available/iconv.ini with new version

Creating config file /etc/php/8.2/mods-available/pdo.ini with new version

Creating config file /etc/php/8.2/mods-available/phar.ini with new version

Creating config file /etc/php/8.2/mods-available/posix.ini with new version

Creating config file /etc/php/8.2/mods-available/shmop.ini with new version

Creating config file /etc/php/8.2/mods-available/sockets.ini with new version

Creating config file /etc/php/8.2/mods-available/sysvmsg.ini with new version

Creating config file /etc/php/8.2/mods-available/sysvsem.ini with new version

Creating config file /etc/php/8.2/mods-available/sysvshm.ini with new version

Creating config file /etc/php/8.2/mods-available/tokenizer.ini with new version
Setting up php8.2-opcache (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...

Creating config file /etc/php/8.2/mods-available/opcache.ini with new version
Setting up php8.2-readline (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...

Creating config file /etc/php/8.2/mods-available/readline.ini with new version
Setting up php8.2-cli (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
update-alternatives: using /usr/bin/php8.2 to provide /usr/bin/php (php) in auto mode
update-alternatives: using /usr/bin/phar8.2 to provide /usr/bin/phar (phar) in auto mode
update-alternatives: using /usr/bin/phar.phar8.2 to provide /usr/bin/phar.phar (phar.phar) in auto mode

Creating config file /etc/php/8.2/cli/php.ini with new version
Setting up libapache2-mod-php8.2 (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...

Creating config file /etc/php/8.2/apache2/php.ini with new version
libapache2-mod-php8.2: php8.1 module already enabled, not enabling PHP 8.2
Setting up php8.2 (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Processing triggers for man-db (2.10.2-1) ...
Processing triggers for php8.2-cli (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
Processing triggers for libapache2-mod-php8.2 (8.2.15-1+ubuntu22.04.1+deb.sury.org+1) ...
root@tcargobr:~# php -v
PHP 8.2.15 (cli) (built: Jan 20 2024 14:17:05) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.15, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.15, Copyright (c), by Zend Technologies
root@tcargobr:~# git clone https://github.com/BOTzerorepo/api-btz-tcargo.git
Cloning into 'api-btz-tcargo'...
Username for 'https://github.com': pachimanok
Password for 'https://pachimanok@github.com': 
remote: Enumerating objects: 1743, done.
remote: Counting objects: 100% (1743/1743), done.
remote: Compressing objects: 100% (633/633), done.
remote: Total 1743 (delta 1068), reused 1743 (delta 1068), pack-reused 0
Receiving objects: 100% (1743/1743), 5.75 MiB | 3.70 MiB/s, done.
Resolving deltas: 100% (1068/1068), done.
root@tcargobr:~# sudo ln -s api-btz-tcargo/ /var/www/api-btz-tcargo
root@tcargobr:~# sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/api-btz-tcargo
root@tcargobr:~# sudo nano /etc/apache2/sites-available/api-btz-tcargo
root@tcargobr:~# sudo a2dissite 000-default.conf
Site 000-default disabled.
To activate the new configuration, you need to run:
  systemctl reload apache2
root@tcargobr:~# sudo a2ensite api-btz-tcargo
ERROR: Site api-btz-tcargo does not exist!
root@tcargobr:~# sudo a2ensite api-btz-tcargo.conf
ERROR: Site api-btz-tcargo does not exist!
root@tcargobr:~# ls
api-btz-tcargo
root@tcargobr:~# cd /etc/apache2/sites-available/
root@tcargobr:/etc/apache2/sites-available# ls
000-default.conf  api-btz-tcargo  default-ssl.conf
root@tcargobr:/etc/apache2/sites-available# mv api-btz-tcargo api-btz-cargo.conf
root@tcargobr:/etc/apache2/sites-available# ls
000-default.conf  api-btz-cargo.conf  default-ssl.conf
root@tcargobr:/etc/apache2/sites-available# sudo a2ensite api-btz-tcargo.conf
ERROR: Site api-btz-tcargo does not exist!
root@tcargobr:/etc/apache2/sites-available# mv api-btz-cargo.conf api-btz-tcargo.conf
root@tcargobr:/etc/apache2/sites-available# sudo a2ensite api-btz-tcargo.conf
Enabling site api-btz-tcargo.
To activate the new configuration, you need to run:
  systemctl reload apache2
root@tcargobr:/etc/apache2/sites-available# sudo service apache2 restart
root@tcargobr:/etc/apache2/sites-available# cd /var/www/api-btz-tcargo 
-bash: cd: /var/www/api-btz-tcargo: Too many levels of symbolic links
root@tcargobr:/etc/apache2/sites-available# cd /var/www/html/
root@tcargobr:/var/www/html# ls
index.html
root@tcargobr:/var/www/html# cd ..
root@tcargobr:/var/www# ls
api-btz-tcargo  html
root@tcargobr:/var/www# cd api-btz-tcargo 
-bash: cd: api-btz-tcargo: Too many levels of symbolic links
root@tcargobr:/var/www# ls -la
total 12
drwxr-xr-x  3 root root 4096 Feb 28 14:43 .
drwxr-xr-x 12 root root 4096 Jan  4  2023 ..
lrwxrwxrwx  1 root root   15 Feb 28 14:43 api-btz-tcargo -> api-btz-tcargo/
drwxr-xr-x  2 root root 4096 Jan  4  2023 html
root@tcargobr:/var/www# cd api-btz-tcargo/
-bash: cd: api-btz-tcargo/: Too many levels of symbolic links
root@tcargobr:/var/www# cd --
root@tcargobr:~# ls
api-btz-tcargo
root@tcargobr:~# cd api-btz-tcargo/
root@tcargobr:~/api-btz-tcargo# sudo chown -R www-data:www-data public storage
root@tcargobr:~/api-btz-tcargo# ls -la
total 532
drwxr-xr-x 14 root     root       4096 Feb 28 14:41  .
drwx------  7 root     root       4096 Feb 28 14:43  ..
-rw-r--r--  1 root     root        258 Feb 28 14:41  .editorconfig
-rw-r--r--  1 root     root       1455 Feb 28 14:41 '.env copy'
-rw-r--r--  1 root     root       1083 Feb 28 14:41  .env.Produccion
drwxr-xr-x  8 root     root       4096 Feb 28 14:41  .git
-rw-r--r--  1 root     root        152 Feb 28 14:41  .gitattributes
-rw-r--r--  1 root     root        230 Feb 28 14:41  .gitignore
-rw-r--r--  1 root     root        327 Feb 28 14:41  .htaccess
-rw-r--r--  1 root     root        162 Feb 28 14:41  .styleci.yml
-rw-r--r--  1 root     root       3958 Feb 28 14:41  README.md
drwxr-xr-x 11 root     root       4096 Feb 28 14:41  app
-rw-r--r--  1 root     root       1686 Feb 28 14:41  artisan
drwxr-xr-x  3 root     root       4096 Feb 28 14:41  bootstrap
-rw-r--r--  1 root     root       2042 Feb 28 14:41  composer.json
-rw-r--r--  1 root     root     388335 Feb 28 14:41  composer.lock
drwxr-xr-x  2 root     root       4096 Feb 28 14:41  config
drwxr-xr-x  5 root     root       4096 Feb 28 14:41  database
-rw-r--r--  1 root     root      16406 Feb 28 14:41  default.php
-rw-r--r--  1 root     root      16406 Feb 28 14:41  default.php.old.php
drwxr-xr-x  3 root     root       4096 Feb 28 14:41  lang
-rw-r--r--  1 root     root          0 Feb 28 14:41  leeme.txt
drwxr-xr-x 12 root     root       4096 Feb 28 14:41  mail
-rw-r--r--  1 root     root        473 Feb 28 14:41  package.json
-rw-r--r--  1 root     root       1175 Feb 28 14:41  phpunit.xml
-rw-r--r--  1 root     root         17 Feb 28 14:41  prueba.txt
drwxr-xr-x  5 www-data www-data   4096 Feb 28 14:41  public
drwxr-xr-x  5 root     root       4096 Feb 28 14:41  resources
drwxr-xr-x  2 root     root       4096 Feb 28 14:41  routes
drwxr-xr-x  5 www-data www-data   4096 Feb 28 14:41  storage
drwxr-xr-x  4 root     root       4096 Feb 28 14:41  tests
-rw-r--r--  1 root     root        559 Feb 28 14:41  webpack.mix.js
root@tcargobr:~/api-btz-tcargo# sudo chmod -R 755 public storage
root@tcargobr:~/api-btz-tcargo# sudo a2enmod rewrite
Enabling module rewrite.
To activate the new configuration, you need to run:
  systemctl restart apache2

root@tcargobr:~/api-btz-tcargo# sudo service apache2 restart

root@tcargobr:~/api-btz-tcargo# composer install
-bash: composer: command not found

root@tcargobr:~/api-btz-tcargo# sudo apt install curl php-cli php-mbstring git unzip


root@tcargobr:~/api-btz-tcargo# curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
All settings correct for using Composer
Downloading...

Composer (version 2.7.1) successfully installed to: /usr/local/bin/composer
Use it: php /usr/local/bin/composer

root@tcargobr:~/api-btz-tcargo# composer --version
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? yes
Composer version 2.7.1 2024-02-09 15:26:28

root@tcargobr:~/api-btz-tcargo# php -v
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies

## Install composer 

root@tcargobr:~/api-btz-tcargo# sudo composer install

root@tcargobr:~/api-btz-tcargo# sudo apt-get install php8.3-intl
root@tcargobr:~/api-btz-tcargo# sudo apt-get install php8.3-gd
root@tcargobr:~/api-btz-tcargo# sudo apt-get install php8.3-zip
root@tcargobr:~/api-btz-tcargo# sudo apt-get install php8.3-curl
root@tcargobr:~/api-btz-tcargo# sudo service apache2 restart
root@tcargobr:~/api-btz-tcargo# sudo composer install



## Habilitar sitio en apache.

root@tcargobr:~/api-btz-tcargo# cd /etc/apache2/sites-available/
root@tcargobr:/etc/apache2/sites-available# ls
000-default.conf  api-btz-tcargo.conf  default-ssl.conf
root@tcargobr:/etc/apache2/sites-available# cd api-btz-tcargo.conf 


root@tcargobr:/var/www# ls
html

root@tcargobr:/var/www# git clone https://github.com/BOTzerorepo/api-btz-tcargo.git
Cloning into 'api-btz-tcargo'...
Username for 'https://github.com': pachimanok
Password for 'https://pachimanok@github.com': 
remote: Enumerating objects: 1743, done.
remote: Counting objects: 100% (1743/1743), done.
remote: Compressing objects: 100% (633/633), done.
remote: Total 1743 (delta 1068), reused 1743 (delta 1068), pack-reused 0
Receiving objects: 100% (1743/1743), 5.75 MiB | 3.65 MiB/s, done.
Resolving deltas: 100% (1068/1068), done.
root@tcargobr:/var/www# ls
api-btz-tcargo  html2
root@tcargobr:/var/www# mv api-btz-tcargo/ html
root@tcargobr:/var/www# ls
html  html2
root@tcargobr:/var/www# sudo nano /etc/apache2/sites-available/api-btz-tcargo.conf 
root@tcargobr:/var/www# sudo a2ensite api-btz-tcargo
Site api-btz-tcargo already enabled
root@tcargobr:/var/www# sudo systemctl restart apache2

## Instalar Composer en Proyecto

root@tcargobr:/var/www/html# composer install
Cannot load Zend OPcache - it was already loaded
PHP Warning:  PHP Startup: Unable to load dynamic library 'imap' (tried: /usr/lib/php/20230831/imap (/usr/lib/php/20230831/imap: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/imap.so (/usr/lib/php/20230831/imap.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? yes
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Package operations: 146 installs, 0 updates, 0 removals
  - Installing voku/portable-ascii (2.0.1): Extracting archive
  - Installing symfony/polyfill-php80 (v1.28.0): Extracting archive
  - Installing symfony/polyfill-mbstring (v1.28.0): Extracting archive
  - Installing symfony/polyfill-ctype (v1.28.0): Extracting archive
  - Installing phpoption/phpoption (1.9.2): Extracting archive
  - Installing graham-campbell/result-type (v1.1.2): Extracting archive
  - Installing vlucas/phpdotenv (v5.6.0): Extracting archive
  - Installing symfony/css-selector (v6.4.0): Extracting archive
  - Installing tijsverkoyen/css-to-inline-styles (2.2.6): Extracting archive
  - Installing symfony/deprecation-contracts (v3.4.0): Extracting archive
  - Installing symfony/var-dumper (v6.4.0): Extracting archive
  - Installing symfony/polyfill-uuid (v1.28.0): Extracting archive
  - Installing symfony/uid (v6.4.0): Extracting archive
  - Installing symfony/routing (v6.4.1): Extracting archive
  - Installing symfony/process (v6.4.0): Extracting archive
  - Installing symfony/polyfill-php72 (v1.28.0): Extracting archive
  - Installing symfony/polyfill-intl-normalizer (v1.28.0): Extracting archive
  - Installing symfony/polyfill-intl-idn (v1.28.0): Extracting archive
  - Installing symfony/mime (v6.4.0): Extracting archive
  - Installing psr/container (2.0.2): Extracting archive
  - Installing symfony/service-contracts (v3.4.0): Extracting archive
  - Installing psr/event-dispatcher (1.0.0): Extracting archive
  - Installing symfony/event-dispatcher-contracts (v3.4.0): Extracting archive
  - Installing symfony/event-dispatcher (v7.0.0): Extracting archive
  - Installing psr/log (3.0.0): Extracting archive
  - Installing doctrine/lexer (3.0.0): Extracting archive
  - Installing egulias/email-validator (4.0.2): Extracting archive
  - Installing symfony/mailer (v6.4.0): Extracting archive
  - Installing symfony/polyfill-php83 (v1.28.0): Extracting archive
  - Installing symfony/http-foundation (v6.4.0): Extracting archive
  - Installing symfony/error-handler (v6.4.0): Extracting archive
  - Installing symfony/http-kernel (v6.4.1): Extracting archive
  - Installing symfony/finder (v6.4.0): Extracting archive
  - Installing symfony/polyfill-intl-grapheme (v1.28.0): Extracting archive
  - Installing symfony/string (v7.0.0): Extracting archive
  - Installing symfony/console (v6.4.1): Extracting archive
  - Installing ramsey/collection (2.0.0): Extracting archive
  - Installing brick/math (0.11.0): Extracting archive
  - Installing ramsey/uuid (4.7.5): Extracting archive
  - Installing psr/simple-cache (2.0.0): Extracting archive
  - Installing nunomaduro/termwind (v1.15.1): Extracting archive
  - Installing symfony/translation-contracts (v3.4.0): Extracting archive
  - Installing symfony/translation (v6.4.0): Extracting archive
  - Installing psr/clock (1.0.0): Extracting archive
  - Installing carbonphp/carbon-doctrine-types (3.0.0): Extracting archive
  - Installing nesbot/carbon (2.72.0): Extracting archive
  - Installing monolog/monolog (2.9.2): Extracting archive
  - Installing league/mime-type-detection (1.14.0): Extracting archive
  - Installing league/flysystem (3.23.0): Extracting archive
  - Installing league/flysystem-local (3.23.0): Extracting archive
  - Installing nette/utils (v4.0.3): Extracting archive
  - Installing nette/schema (v1.2.5): Extracting archive
  - Installing dflydev/dot-access-data (v3.0.2): Extracting archive
  - Installing league/config (v1.2.0): Extracting archive
  - Installing league/commonmark (2.4.1): Extracting archive
  - Installing laravel/serializable-closure (v1.3.3): Extracting archive
  - Installing guzzlehttp/uri-template (v1.0.3): Extracting archive
  - Installing fruitcake/php-cors (v1.3.0): Extracting archive
  - Installing webmozart/assert (1.11.0): Extracting archive
  - Installing dragonmantank/cron-expression (v3.3.3): Extracting archive
  - Installing doctrine/inflector (2.0.8): Extracting archive
  - Installing laravel/framework (v9.52.16): Extracting archive
  - Installing sabberworm/php-css-parser (8.4.0): Extracting archive
  - Installing phenx/php-svg-lib (0.4.1): Extracting archive
  - Installing phenx/php-font-lib (0.5.4): Extracting archive
  - Installing dompdf/dompdf (v1.2.2): Extracting archive
  - Installing barryvdh/laravel-dompdf (v1.0.2): Extracting archive
  - Installing symfony/yaml (v6.4.0): Extracting archive
  - Installing psr/cache (3.0.0): Extracting archive
  - Installing doctrine/annotations (2.0.1): Extracting archive
  - Installing zircote/swagger-php (4.7.16): Extracting archive
  - Installing swagger-api/swagger-ui (v5.10.3): Extracting archive
  - Installing darkaonline/l5-swagger (8.5.1): Extracting archive
  - Installing dasprid/enum (1.0.5): Extracting archive
  - Installing fakerphp/faker (v1.23.0): Extracting archive
  - Installing psr/http-message (2.0): Extracting archive
  - Installing psr/http-client (1.0.3): Extracting archive
  - Installing ralouphie/getallheaders (3.0.3): Extracting archive
  - Installing psr/http-factory (1.0.2): Extracting archive
  - Installing guzzlehttp/psr7 (2.6.2): Extracting archive
  - Installing guzzlehttp/promises (2.0.2): Extracting archive
  - Installing guzzlehttp/guzzle (7.8.1): Extracting archive
  - Installing paragonie/constant_time_encoding (v2.6.3): Extracting archive
  - Installing pragmarx/google2fa (v8.0.1): Extracting archive
  - Installing bacon/bacon-qr-code (2.0.8): Extracting archive
  - Installing laravel/fortify (v1.19.0): Extracting archive
  - Installing symfony/psr-http-message-bridge (v2.3.1): Extracting archive
  - Installing paragonie/random_compat (v9.99.100): Extracting archive
  - Installing phpseclib/phpseclib (3.0.34): Extracting archive
  - Installing nyholm/psr7 (1.8.1): Extracting archive
  - Installing league/uri-interfaces (7.4.0): Extracting archive
  - Installing league/uri (7.4.0): Extracting archive
  - Installing league/event (2.2.0): Extracting archive
  - Installing lcobucci/clock (3.2.0): Extracting archive
  - Installing lcobucci/jwt (4.3.0): Extracting archive
  - Installing defuse/php-encryption (v2.4.0): Extracting archive
  - Installing league/oauth2-server (8.5.4): Extracting archive
  - Installing firebase/php-jwt (v6.10.0): Extracting archive
  - Installing laravel/passport (v10.4.2): Extracting archive
  - Installing laravel/sail (v1.26.2): Extracting archive
  - Installing laravel/sanctum (v2.15.1): Extracting archive
  - Installing nikic/php-parser (v4.17.1): Extracting archive
  - Installing psy/psysh (v0.11.22): Extracting archive
  - Installing laravel/tinker (v2.8.2): Extracting archive
  - Installing markbaker/matrix (3.0.1): Extracting archive
  - Installing markbaker/complex (3.0.2): Extracting archive
  - Installing maennchen/zipstream-php (3.1.0): Extracting archive
  - Installing ezyang/htmlpurifier (v4.17.0): Extracting archive
  - Installing phpoffice/phpspreadsheet (1.29.0): Extracting archive
  - Installing composer/semver (3.4.0): Extracting archive
  - Installing maatwebsite/excel (3.1.50): Extracting archive
  - Installing hamcrest/hamcrest-php (v2.0.1): Extracting archive
  - Installing mockery/mockery (1.6.6): Extracting archive
  - Installing filp/whoops (2.15.4): Extracting archive
  - Installing nunomaduro/collision (v6.4.0): Extracting archive
  - Installing sebastian/version (3.0.2): Extracting archive
  - Installing sebastian/type (3.2.1): Extracting archive
  - Installing sebastian/resource-operations (3.0.3): Extracting archive
  - Installing sebastian/recursion-context (4.0.5): Extracting archive
  - Installing sebastian/object-reflector (2.0.4): Extracting archive
  - Installing sebastian/object-enumerator (4.0.4): Extracting archive
  - Installing sebastian/global-state (5.0.6): Extracting archive
  - Installing sebastian/exporter (4.0.5): Extracting archive
  - Installing sebastian/environment (5.1.5): Extracting archive
  - Installing sebastian/diff (4.0.5): Extracting archive
  - Installing sebastian/comparator (4.0.8): Extracting archive
  - Installing sebastian/code-unit (1.0.8): Extracting archive
  - Installing sebastian/cli-parser (1.0.1): Extracting archive
  - Installing phpunit/php-timer (5.0.3): Extracting archive
  - Installing phpunit/php-text-template (2.0.4): Extracting archive
  - Installing phpunit/php-invoker (3.1.1): Extracting archive
  - Installing phpunit/php-file-iterator (3.0.6): Extracting archive
  - Installing theseer/tokenizer (1.2.2): Extracting archive
  - Installing sebastian/lines-of-code (1.0.3): Extracting archive
  - Installing sebastian/complexity (2.0.2): Extracting archive
  - Installing sebastian/code-unit-reverse-lookup (2.0.3): Extracting archive
  - Installing phpunit/php-code-coverage (9.2.29): Extracting archive
  - Installing phar-io/version (3.2.1): Extracting archive
  - Installing phar-io/manifest (2.0.3): Extracting archive
  - Installing myclabs/deep-copy (1.11.1): Extracting archive
  - Installing doctrine/instantiator (2.0.0): Extracting archive
  - Installing phpunit/phpunit (9.6.15): Extracting archive
  - Installing spatie/backtrace (1.5.3): Extracting archive
  - Installing spatie/flare-client-php (1.4.3): Extracting archive
  - Installing spatie/ignition (1.11.3): Extracting archive
  - Installing spatie/laravel-ignition (1.6.4): Extracting archive
Generating optimized autoload files
Class Database\Seeders\PayModeSeeder located in ./database/seeders/PaymodeSeeder.php does not comply with psr-4 autoloading standard. Skipping.
Class Database\Seeders\PayTimeSeeder located in ./database/seeders/PaytimeSeeder.php does not comply with psr-4 autoloading standard. Skipping.
Class Database\Seeders\CntrTypeSeeder located in ./database/seeders/CntrtypeSeeder.php does not comply with psr-4 autoloading standard. Skipping.
Class Database\Factories\PayModeFactory located in ./database/factories/PaymodeFactory.php does not comply with psr-4 autoloading standard. Skipping.
Class Database\Factories\PayTimeFactory located in ./database/factories/PaytimeFactory.php does not comply with psr-4 autoloading standard. Skipping.
Class Database\Factories\CntrTypeFactory located in ./database/factories/CntrtypeFactory.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Models\Ata located in ./app/Models/ata.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Models\Customer located in ./app/Models/customer.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Models\PayTime located in ./app/Models/paytime.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Models\CntrType located in ./app/Models/cntrtype.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Models\Company located in ./app/Models/company.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Models\PayMode located in ./app/Models/paymode.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Http\Requests\StorecustomerRequest located in ./app/Http/Requests/StorecustomerRequest copy.php does not comply with psr-4 autoloading standard. Skipping.
Class App\Exports\companies located in ./app/Exports/compnanies.php does not comply with psr-4 autoloading standard. Skipping.
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> @php artisan package:discover --ansi
Cannot load Zend OPcache - it was already loaded
PHP Warning:  PHP Startup: Unable to load dynamic library 'imap' (tried: /usr/lib/php/20230831/imap (/usr/lib/php/20230831/imap: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/imap.so (/usr/lib/php/20230831/imap.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0

   INFO  Discovering packages.  

  barryvdh/laravel-dompdf ................................................................. DONE
  darkaonline/l5-swagger .................................................................. DONE
  laravel/fortify ......................................................................... DONE
  laravel/passport ........................................................................ DONE
  laravel/sail ............................................................................ DONE
  laravel/sanctum ......................................................................... DONE
  laravel/tinker .......................................................................... DONE
  maatwebsite/excel ....................................................................... DONE
  nesbot/carbon ........................................................................... DONE
  nunomaduro/collision .................................................................... DONE
  nunomaduro/termwind ..................................................................... DONE
  spatie/laravel-ignition ................................................................. DONE

98 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
root@tcargobr:/var/www/html# ls -la
total 536
drwxr-xr-x 15 root root   4096 Feb 28 15:36  .
drwxr-xr-x  4 root root   4096 Feb 28 15:33  ..
-rw-r--r--  1 root root    258 Feb 28 15:33  .editorconfig
-rw-r--r--  1 root root   1455 Feb 28 15:33 '.env copy'
-rw-r--r--  1 root root   1083 Feb 28 15:33  .env.Produccion
drwxr-xr-x  8 root root   4096 Feb 28 15:33  .git
-rw-r--r--  1 root root    152 Feb 28 15:33  .gitattributes
-rw-r--r--  1 root root    230 Feb 28 15:33  .gitignore
-rw-r--r--  1 root root    327 Feb 28 15:33  .htaccess
-rw-r--r--  1 root root    162 Feb 28 15:33  .styleci.yml
-rw-r--r--  1 root root   3958 Feb 28 15:33  README.md
drwxr-xr-x 11 root root   4096 Feb 28 15:33  app
-rw-r--r--  1 root root   1686 Feb 28 15:33  artisan
drwxr-xr-x  3 root root   4096 Feb 28 15:33  bootstrap
-rw-r--r--  1 root root   2042 Feb 28 15:33  composer.json
-rw-r--r--  1 root root 388335 Feb 28 15:33  composer.lock
drwxr-xr-x  2 root root   4096 Feb 28 15:33  config
drwxr-xr-x  5 root root   4096 Feb 28 15:33  database
-rw-r--r--  1 root root  16406 Feb 28 15:33  default.php
-rw-r--r--  1 root root  16406 Feb 28 15:33  default.php.old.php
drwxr-xr-x  3 root root   4096 Feb 28 15:33  lang
-rw-r--r--  1 root root      0 Feb 28 15:33  leeme.txt
drwxr-xr-x 12 root root   4096 Feb 28 15:33  mail
-rw-r--r--  1 root root    473 Feb 28 15:33  package.json
-rw-r--r--  1 root root   1175 Feb 28 15:33  phpunit.xml
-rw-r--r--  1 root root     17 Feb 28 15:33  prueba.txt
drwxr-xr-x  5 root root   4096 Feb 28 15:33  public
drwxr-xr-x  5 root root   4096 Feb 28 15:33  resources
drwxr-xr-x  2 root root   4096 Feb 28 15:33  routes
drwxr-xr-x  5 root root   4096 Feb 28 15:33  storage
drwxr-xr-x  4 root root   4096 Feb 28 15:33  tests
drwxr-xr-x 61 root root   4096 Feb 28 15:36  vendor
-rw-r--r--  1 root root    559 Feb 28 15:33  webpack.mix.js
root@tcargobr:/var/www/html# cp '.env copy' .env
root@tcargobr:/var/www/html# ls
README.md  composer.json  default.php          mail          public     tests
app        composer.lock  default.php.old.php  package.json  resources  vendor
artisan    config         lang                 phpunit.xml   routes     webpack.mix.js
bootstrap  database       leeme.txt            prueba.txt    storage
root@tcargobr:/var/www/html# ls  -la
total 540
drwxr-xr-x 15 root root   4096 Feb 28 15:37  .
drwxr-xr-x  4 root root   4096 Feb 28 15:33  ..
-rw-r--r--  1 root root    258 Feb 28 15:33  .editorconfig
-rw-r--r--  1 root root   1455 Feb 28 15:37  .env
-rw-r--r--  1 root root   1455 Feb 28 15:33 '.env copy'
-rw-r--r--  1 root root   1083 Feb 28 15:33  .env.Produccion
drwxr-xr-x  8 root root   4096 Feb 28 15:33  .git
-rw-r--r--  1 root root    152 Feb 28 15:33  .gitattributes
-rw-r--r--  1 root root    230 Feb 28 15:33  .gitignore
-rw-r--r--  1 root root    327 Feb 28 15:33  .htaccess
-rw-r--r--  1 root root    162 Feb 28 15:33  .styleci.yml
-rw-r--r--  1 root root   3958 Feb 28 15:33  README.md
drwxr-xr-x 11 root root   4096 Feb 28 15:33  app
-rw-r--r--  1 root root   1686 Feb 28 15:33  artisan
drwxr-xr-x  3 root root   4096 Feb 28 15:33  bootstrap
-rw-r--r--  1 root root   2042 Feb 28 15:33  composer.json
-rw-r--r--  1 root root 388335 Feb 28 15:33  composer.lock
drwxr-xr-x  2 root root   4096 Feb 28 15:33  config
drwxr-xr-x  5 root root   4096 Feb 28 15:33  database
-rw-r--r--  1 root root  16406 Feb 28 15:33  default.php
-rw-r--r--  1 root root  16406 Feb 28 15:33  default.php.old.php
drwxr-xr-x  3 root root   4096 Feb 28 15:33  lang
-rw-r--r--  1 root root      0 Feb 28 15:33  leeme.txt
drwxr-xr-x 12 root root   4096 Feb 28 15:33  mail
-rw-r--r--  1 root root    473 Feb 28 15:33  package.json
-rw-r--r--  1 root root   1175 Feb 28 15:33  phpunit.xml
-rw-r--r--  1 root root     17 Feb 28 15:33  prueba.txt
drwxr-xr-x  5 root root   4096 Feb 28 15:33  public
drwxr-xr-x  5 root root   4096 Feb 28 15:33  resources
drwxr-xr-x  2 root root   4096 Feb 28 15:33  routes
drwxr-xr-x  5 root root   4096 Feb 28 15:33  storage
drwxr-xr-x  4 root root   4096 Feb 28 15:33  tests
drwxr-xr-x 61 root root   4096 Feb 28 15:36  vendor
-rw-r--r--  1 root root    559 Feb 28 15:33  webpack.mix.js
root@tcargobr:/var/www/html# nano .env.Produccion 
root@tcargobr:/var/www/html# rm .env
root@tcargobr:/var/www/html# cp .env.Produccion .env
root@tcargobr:/var/www/html# ls
README.md  composer.json  default.php          mail          public     tests
app        composer.lock  default.php.old.php  package.json  resources  vendor
artisan    config         lang                 phpunit.xml   routes     webpack.mix.js
bootstrap  database       leeme.txt            prueba.txt    storage
root@tcargobr:/var/www/html# ls -la
total 540
drwxr-xr-x 15 root root   4096 Feb 28 15:37  .
drwxr-xr-x  4 root root   4096 Feb 28 15:33  ..
-rw-r--r--  1 root root    258 Feb 28 15:33  .editorconfig
-rw-r--r--  1 root root   1083 Feb 28 15:37  .env
-rw-r--r--  1 root root   1455 Feb 28 15:33 '.env copy'
-rw-r--r--  1 root root   1083 Feb 28 15:33  .env.Produccion
drwxr-xr-x  8 root root   4096 Feb 28 15:33  .git
-rw-r--r--  1 root root    152 Feb 28 15:33  .gitattributes
-rw-r--r--  1 root root    230 Feb 28 15:33  .gitignore
-rw-r--r--  1 root root    327 Feb 28 15:33  .htaccess
-rw-r--r--  1 root root    162 Feb 28 15:33  .styleci.yml
-rw-r--r--  1 root root   3958 Feb 28 15:33  README.md
drwxr-xr-x 11 root root   4096 Feb 28 15:33  app
-rw-r--r--  1 root root   1686 Feb 28 15:33  artisan
drwxr-xr-x  3 root root   4096 Feb 28 15:33  bootstrap
-rw-r--r--  1 root root   2042 Feb 28 15:33  composer.json
-rw-r--r--  1 root root 388335 Feb 28 15:33  composer.lock
drwxr-xr-x  2 root root   4096 Feb 28 15:33  config
drwxr-xr-x  5 root root   4096 Feb 28 15:33  database
-rw-r--r--  1 root root  16406 Feb 28 15:33  default.php
-rw-r--r--  1 root root  16406 Feb 28 15:33  default.php.old.php
drwxr-xr-x  3 root root   4096 Feb 28 15:33  lang
-rw-r--r--  1 root root      0 Feb 28 15:33  leeme.txt
drwxr-xr-x 12 root root   4096 Feb 28 15:33  mail
-rw-r--r--  1 root root    473 Feb 28 15:33  package.json
-rw-r--r--  1 root root   1175 Feb 28 15:33  phpunit.xml
-rw-r--r--  1 root root     17 Feb 28 15:33  prueba.txt
drwxr-xr-x  5 root root   4096 Feb 28 15:33  public
drwxr-xr-x  5 root root   4096 Feb 28 15:33  resources
drwxr-xr-x  2 root root   4096 Feb 28 15:33  routes
drwxr-xr-x  5 root root   4096 Feb 28 15:33  storage
drwxr-xr-x  4 root root   4096 Feb 28 15:33  tests
drwxr-xr-x 61 root root   4096 Feb 28 15:36  vendor
-rw-r--r--  1 root root    559 Feb 28 15:33  webpack.mix.js
root@tcargobr:/var/www/html# php -v
Cannot load Zend OPcache - it was already loaded
PHP Warning:  PHP Startup: Unable to load dynamic library 'imap' (tried: /usr/lib/php/20230831/imap (/usr/lib/php/20230831/imap: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/imap.so (/usr/lib/php/20230831/imap.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/var/www/html# sudo apt update
sudo apt install php-imap php-mysqli php-xsl
Hit:1 http://archive.canonical.com/ubuntu jammy InRelease                                        
Hit:2 http://archive.ubuntu.com/ubuntu jammy InRelease                                           
Get:3 http://security.ubuntu.com/ubuntu jammy-security InRelease [110 kB]                        
Get:4 http://archive.ubuntu.com/ubuntu jammy-updates InRelease [119 kB]                          
Hit:5 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy InRelease                         
Get:6 http://security.ubuntu.com/ubuntu jammy-security/main amd64 Packages [1202 kB]
Get:7 http://security.ubuntu.com/ubuntu jammy-security/main Translation-en [218 kB]
Fetched 1649 kB in 5s (306 kB/s)                               
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
20 packages can be upgraded. Run 'apt list --upgradable' to see them.
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
Package php-mysqli is a virtual package provided by:
  php8.3-mysql 8.3.3-1+ubuntu22.04.1+deb.sury.org+1
  php8.2-mysql 8.2.15-1+ubuntu22.04.1+deb.sury.org+1
  php8.1-mysql 8.1.27-1+ubuntu22.04.1+deb.sury.org+1
  php8.0-mysql 1:8.0.30-2+ubuntu22.04.1+deb.sury.org+1
  php7.4-mysql 1:7.4.33-8+ubuntu22.04.1+deb.sury.org+1
  php7.3-mysql 7.3.33-14+ubuntu22.04.1+deb.sury.org+1
  php7.2-mysql 7.2.34-43+ubuntu22.04.1+deb.sury.org+1
  php7.1-mysql 7.1.33-56+ubuntu22.04.1+deb.sury.org+1
  php7.0-mysql 7.0.33-68+ubuntu22.04.1+deb.sury.org+2
  php5.6-mysql 5.6.40-68+ubuntu22.04.1+deb.sury.org+1
You should explicitly select one to install.

Package php-xsl is a virtual package provided by:
  php8.3-xml 8.3.3-1+ubuntu22.04.1+deb.sury.org+1
  php8.2-xml 8.2.15-1+ubuntu22.04.1+deb.sury.org+1
  php8.1-xml 8.1.27-1+ubuntu22.04.1+deb.sury.org+1
  php8.0-xml 1:8.0.30-2+ubuntu22.04.1+deb.sury.org+1
  php7.4-xml 1:7.4.33-8+ubuntu22.04.1+deb.sury.org+1
  php7.3-xml 7.3.33-14+ubuntu22.04.1+deb.sury.org+1
  php7.2-xml 7.2.34-43+ubuntu22.04.1+deb.sury.org+1
  php7.1-xml 7.1.33-56+ubuntu22.04.1+deb.sury.org+1
  php7.0-xml 7.0.33-68+ubuntu22.04.1+deb.sury.org+2
  php5.6-xml 5.6.40-68+ubuntu22.04.1+deb.sury.org+1
You should explicitly select one to install.

E: Package 'php-mysqli' has no installation candidate
E: Package 'php-xsl' has no installation candidate
root@tcargobr:/var/www/html# php -v
Cannot load Zend OPcache - it was already loaded
PHP Warning:  PHP Startup: Unable to load dynamic library 'imap' (tried: /usr/lib/php/20230831/imap (/usr/lib/php/20230831/imap: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/imap.so (/usr/lib/php/20230831/imap.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/var/www/html# client_loop: send disconnect: Broken pipe
pablorio@MacBook-Air-de-Pablo .ssh % ssh root@185.137.92.229
root@185.137.92.229's password: 
Welcome to Ubuntu 22.04.4 LTS (GNU/Linux 5.2.0 x86_64)

 * Documentation:  https://help.ubuntu.com
 * Management:     https://landscape.canonical.com
 * Support:        https://ubuntu.com/pro
Last login: Wed Feb 28 14:18:21 2024 from 38.51.27.1
root@tcargobr:~# php --ini
Cannot load Zend OPcache - it was already loaded
PHP Warning:  PHP Startup: Unable to load dynamic library 'imap' (tried: /usr/lib/php/20230831/imap (/usr/lib/php/20230831/imap: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/imap.so (/usr/lib/php/20230831/imap.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
Configuration File (php.ini) Path: /etc/php/8.3/cli
Loaded Configuration File:         /etc/php/8.3/cli/php.ini
Scan for additional .ini files in: /etc/php/8.3/cli/conf.d
Additional .ini files parsed:      /etc/php/8.3/cli/conf.d/10-opcache.ini,
/etc/php/8.3/cli/conf.d/10-pdo.ini,
/etc/php/8.3/cli/conf.d/15-xml.ini,
/etc/php/8.3/cli/conf.d/20-calendar.ini,
/etc/php/8.3/cli/conf.d/20-ctype.ini,
/etc/php/8.3/cli/conf.d/20-curl.ini,
/etc/php/8.3/cli/conf.d/20-dom.ini,
/etc/php/8.3/cli/conf.d/20-exif.ini,
/etc/php/8.3/cli/conf.d/20-ffi.ini,
/etc/php/8.3/cli/conf.d/20-fileinfo.ini,
/etc/php/8.3/cli/conf.d/20-ftp.ini,
/etc/php/8.3/cli/conf.d/20-gd.ini,
/etc/php/8.3/cli/conf.d/20-gettext.ini,
/etc/php/8.3/cli/conf.d/20-iconv.ini,
/etc/php/8.3/cli/conf.d/20-intl.ini,
/etc/php/8.3/cli/conf.d/20-mbstring.ini,
/etc/php/8.3/cli/conf.d/20-phar.ini,
/etc/php/8.3/cli/conf.d/20-posix.ini,
/etc/php/8.3/cli/conf.d/20-readline.ini,
/etc/php/8.3/cli/conf.d/20-shmop.ini,
/etc/php/8.3/cli/conf.d/20-simplexml.ini,
/etc/php/8.3/cli/conf.d/20-sockets.ini,
/etc/php/8.3/cli/conf.d/20-sysvmsg.ini,
/etc/php/8.3/cli/conf.d/20-sysvsem.ini,
/etc/php/8.3/cli/conf.d/20-sysvshm.ini,
/etc/php/8.3/cli/conf.d/20-tokenizer.ini,
/etc/php/8.3/cli/conf.d/20-xmlreader.ini,
/etc/php/8.3/cli/conf.d/20-xmlwriter.ini,
/etc/php/8.3/cli/conf.d/20-xsl.ini,
/etc/php/8.3/cli/conf.d/20-zip.ini

root@tcargobr:~# nano /etc/php/8.3/cli/
root@tcargobr:~# cd /etc/php/8.3/cli/
root@tcargobr:/etc/php/8.3/cli# ls
conf.d  php.ini
root@tcargobr:/etc/php/8.3/cli# nano php.ini 
root@tcargobr:/etc/php/8.3/cli# 
root@tcargobr:/etc/php/8.3/cli# vim php.ini 
root@tcargobr:/etc/php/8.3/cli# sudo systemctl restart apache2
root@tcargobr:/etc/php/8.3/cli# php -v
Cannot load Zend OPcache - it was already loaded
PHP Warning:  PHP Startup: Unable to load dynamic library 'imap' (tried: /usr/lib/php/20230831/imap (/usr/lib/php/20230831/imap: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/imap.so (/usr/lib/php/20230831/imap.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/etc/php/8.3/cli# nano php.ini 
root@tcargobr:/etc/php/8.3/cli# vim php.ini 
root@tcargobr:/etc/php/8.3/cli# sudo systemctl restart apache2
root@tcargobr:/etc/php/8.3/cli# php -v
PHP Warning:  PHP Startup: Unable to load dynamic library 'imap' (tried: /usr/lib/php/20230831/imap (/usr/lib/php/20230831/imap: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/imap.so (/usr/lib/php/20230831/imap.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: cannot open shared object file: No such file or directory)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/etc/php/8.3/cli# sudo apt install php-imap php-mysqli php-xsl
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
Package php-mysqli is a virtual package provided by:
  php8.3-mysql 8.3.3-1+ubuntu22.04.1+deb.sury.org+1
  php8.2-mysql 8.2.15-1+ubuntu22.04.1+deb.sury.org+1
  php8.1-mysql 8.1.27-1+ubuntu22.04.1+deb.sury.org+1
  php8.0-mysql 1:8.0.30-2+ubuntu22.04.1+deb.sury.org+1
  php7.4-mysql 1:7.4.33-8+ubuntu22.04.1+deb.sury.org+1
  php7.3-mysql 7.3.33-14+ubuntu22.04.1+deb.sury.org+1
  php7.2-mysql 7.2.34-43+ubuntu22.04.1+deb.sury.org+1
  php7.1-mysql 7.1.33-56+ubuntu22.04.1+deb.sury.org+1
  php7.0-mysql 7.0.33-68+ubuntu22.04.1+deb.sury.org+2
  php5.6-mysql 5.6.40-68+ubuntu22.04.1+deb.sury.org+1
You should explicitly select one to install.

Package php-xsl is a virtual package provided by:
  php8.3-xml 8.3.3-1+ubuntu22.04.1+deb.sury.org+1
  php8.2-xml 8.2.15-1+ubuntu22.04.1+deb.sury.org+1
  php8.1-xml 8.1.27-1+ubuntu22.04.1+deb.sury.org+1
  php8.0-xml 1:8.0.30-2+ubuntu22.04.1+deb.sury.org+1
  php7.4-xml 1:7.4.33-8+ubuntu22.04.1+deb.sury.org+1
  php7.3-xml 7.3.33-14+ubuntu22.04.1+deb.sury.org+1
  php7.2-xml 7.2.34-43+ubuntu22.04.1+deb.sury.org+1
  php7.1-xml 7.1.33-56+ubuntu22.04.1+deb.sury.org+1
  php7.0-xml 7.0.33-68+ubuntu22.04.1+deb.sury.org+2
  php5.6-xml 5.6.40-68+ubuntu22.04.1+deb.sury.org+1
You should explicitly select one to install.

E: Package 'php-mysqli' has no installation candidate
E: Package 'php-xsl' has no installation candidate
root@tcargobr:/etc/php/8.3/cli# sudo apt install php8.3-imap php8.3-mysqli php8.3-xsl
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
Note, selecting 'php8.3-mysql' instead of 'php8.3-mysqli'
php8.3-xsl is already the newest version (8.3.3-1+ubuntu22.04.1+deb.sury.org+1).
The following package was automatically installed and is no longer required:
  php8.1-mbstring
Use 'sudo apt autoremove' to remove it.
The following additional packages will be installed:
  libc-client2007e mlock
Suggested packages:
  uw-mailutils
The following NEW packages will be installed:
  libc-client2007e mlock php8.3-imap php8.3-mysql
0 upgraded, 4 newly installed, 0 to remove and 20 not upgraded.
Need to get 824 kB of archives.
After this operation, 2104 kB of additional disk space will be used.
Do you want to continue? [Y/n] Y
Get:1 http://archive.ubuntu.com/ubuntu jammy/universe amd64 mlock amd64 8:2007f~dfsg-7build1 [12.2 kB]
Get:2 http://archive.ubuntu.com/ubuntu jammy/universe amd64 libc-client2007e amd64 8:2007f~dfsg-7build1 [645 kB]
Get:3 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 php8.3-imap amd64 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 [36.5 kB]
Get:4 https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy/main amd64 php8.3-mysql amd64 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 [131 kB]
Fetched 824 kB in 2s (390 kB/s)                                          
Selecting previously unselected package mlock.
(Reading database ... 31685 files and directories currently installed.)
Preparing to unpack .../mlock_8%3a2007f~dfsg-7build1_amd64.deb ...
Unpacking mlock (8:2007f~dfsg-7build1) ...
Selecting previously unselected package libc-client2007e.
Preparing to unpack .../libc-client2007e_8%3a2007f~dfsg-7build1_amd64.deb ...
Unpacking libc-client2007e (8:2007f~dfsg-7build1) ...
Selecting previously unselected package php8.3-imap.
Preparing to unpack .../php8.3-imap_8.3.3-1+ubuntu22.04.1+deb.sury.org+1_amd64.deb ...
Unpacking php8.3-imap (8.3.3-1+ubuntu22.04.1+deb.sury.org+1) ...
Selecting previously unselected package php8.3-mysql.
Preparing to unpack .../php8.3-mysql_8.3.3-1+ubuntu22.04.1+deb.sury.org+1_amd64.deb ...
Unpacking php8.3-mysql (8.3.3-1+ubuntu22.04.1+deb.sury.org+1) ...
Setting up mlock (8:2007f~dfsg-7build1) ...
Setting up php8.3-mysql (8.3.3-1+ubuntu22.04.1+deb.sury.org+1) ...

Creating config file /etc/php/8.3/mods-available/mysqlnd.ini with new version

Creating config file /etc/php/8.3/mods-available/mysqli.ini with new version

Creating config file /etc/php/8.3/mods-available/pdo_mysql.ini with new version
Setting up libc-client2007e (8:2007f~dfsg-7build1) ...
Setting up php8.3-imap (8.3.3-1+ubuntu22.04.1+deb.sury.org+1) ...

Creating config file /etc/php/8.3/mods-available/imap.ini with new version
Processing triggers for man-db (2.10.2-1) ...
Processing triggers for libc-bin (2.35-0ubuntu3.6) ...
Processing triggers for php8.3-cli (8.3.3-1+ubuntu22.04.1+deb.sury.org+1) ...
root@tcargobr:/etc/php/8.3/cli# php -v
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: undefined symbol: mysqlnd_global_stats)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/etc/php/8.3/cli# sudo apt install php8.3-mysqli
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
Note, selecting 'php8.3-mysql' instead of 'php8.3-mysqli'
php8.3-mysql is already the newest version (8.3.3-1+ubuntu22.04.1+deb.sury.org+1).
The following package was automatically installed and is no longer required:
  php8.1-mbstring
Use 'sudo apt autoremove' to remove it.
0 upgraded, 0 newly installed, 0 to remove and 20 not upgraded.
root@tcargobr:/etc/php/8.3/cli# sudo apt install php8.3-xsl
Reading package lists... Done
Building dependency tree... Done
Reading state information... Done
php8.3-xsl is already the newest version (8.3.3-1+ubuntu22.04.1+deb.sury.org+1).
The following package was automatically installed and is no longer required:
  php8.1-mbstring
Use 'sudo apt autoremove' to remove it.
0 upgraded, 0 newly installed, 0 to remove and 20 not upgraded.
root@tcargobr:/etc/php/8.3/cli# php -v
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: undefined symbol: mysqlnd_global_stats)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/etc/php/8.3/cli# sudo systemctl restart apache2
root@tcargobr:/etc/php/8.3/cli# php -v
PHP Warning:  PHP Startup: Unable to load dynamic library 'mysqli' (tried: /usr/lib/php/20230831/mysqli (/usr/lib/php/20230831/mysqli: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/mysqli.so (/usr/lib/php/20230831/mysqli.so: undefined symbol: mysqlnd_global_stats)) in Unknown on line 0
PHP Warning:  PHP Startup: Unable to load dynamic library 'xsl' (tried: /usr/lib/php/20230831/xsl (/usr/lib/php/20230831/xsl: cannot open shared object file: No such file or directory), /usr/lib/php/20230831/xsl.so (/usr/lib/php/20230831/xsl.so: undefined symbol: dom_node_class_entry)) in Unknown on line 0
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
PHP Warning:  Module "zip" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/etc/php/8.3/cli# vim php.ini 
root@tcargobr:/etc/php/8.3/cli# sudo systemctl restart apache2
root@tcargobr:/etc/php/8.3/cli# php -v
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
PHP 8.3.3-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Feb 15 2024 18:38:52) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.3-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
root@tcargobr:/etc/php/8.3/cli# composer -v
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? yes
   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 2.7.1 2024-02-09 15:26:28

Usage:
  command [options] [arguments]

Options:
  -h, --help                     Display help for the given command. When no command is given display help for the list command
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi|--no-ansi           Force (or disable --no-ansi) ANSI output
  -n, --no-interaction           Do not ask any interactive question
      --profile                  Display timing and memory usage information
      --no-plugins               Whether to disable plugins.
      --no-scripts               Skips the execution of all scripts defined in composer.json file.
  -d, --working-dir=WORKING-DIR  If specified, use the given directory as working directory.
      --no-cache                 Prevent use of the cache
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  about                Shows a short information about Composer
  archive              Creates an archive of this composer package
  audit                Checks for security vulnerability advisories for installed packages
  browse               [home] Opens the package's repository URL or homepage in your browser
  bump                 Increases the lower limit of your composer.json requirements to the currently installed versions
  check-platform-reqs  Check that platform requirements are satisfied
  clear-cache          [clearcache|cc] Clears composer's internal package cache
  completion           Dump the shell completion script
  config               Sets config options
  create-project       Creates new project from a package into given directory
  depends              [why] Shows which packages cause the given package to be installed
  diagnose             Diagnoses the system to identify common errors
  dump-autoload        [dumpautoload] Dumps the autoloader
  exec                 Executes a vendored binary/script
  fund                 Discover how to help fund the maintenance of your dependencies
  global               Allows running commands in the global composer dir ($COMPOSER_HOME)
  help                 Display help for a command
  init                 Creates a basic composer.json file in current directory
  install              [i] Installs the project dependencies from the composer.lock file if present, or falls back on the composer.json
  licenses             Shows information about licenses of dependencies
  list                 List commands
  outdated             Shows a list of installed packages that have updates available, including their latest version
  prohibits            [why-not] Shows which packages prevent the given package from being installed
  reinstall            Uninstalls and reinstalls the given package names
  remove               [rm] Removes a package from the require or require-dev
  require              [r] Adds required packages to your composer.json and installs them
  run-script           [run] Runs the scripts defined in composer.json
  search               Searches for packages
  self-update          [selfupdate] Updates composer.phar to the latest version
  show                 [info] Shows information about packages
  status               Shows a list of locally modified packages
  suggests             Shows package suggestions
  update               [u|upgrade] Updates your dependencies to the latest version according to composer.json, and updates the composer.lock file
  validate             Validates a composer.json and composer.lock
root@tcargobr:/etc/php/8.3/cli# sudo composer install
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? yes
Composer could not find a composer.json file in /etc/php/8.3/cli
To initialize a project, please create a composer.json file. See https://getcomposer.org/basic-usage
root@tcargobr:/etc/php/8.3/cli# cd --
root@tcargobr:~# ls
api-btz-tcargo
root@tcargobr:~# cd api-btz-tcargo/
root@tcargobr:~/api-btz-tcargo# ls
README.md  artisan    composer.json  config    default.php          lang       mail          phpunit.xml  public     routes   tests   webpack.mix.js
app        bootstrap  composer.lock  database  default.php.old.php  leeme.txt  package.json  prueba.txt   resources  storage  vendor
root@tcargobr:~/api-btz-tcargo# composer self-update
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
You are already using the latest available Composer version 2.7.1 (stable channel).
root@tcargobr:~/api-btz-tcargo# composer clear-cache
composer clearcache
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? ye
Please answer yes, y, no, or n.
Continue as root/super user [yes]? yes
Cache directory does not exist (cache-vcs-dir): 
Cache directory does not exist (cache-repo-dir): 
Clearing cache (cache-files-dir): /root/.cache/composer/files
Clearing cache (cache-dir): /root/.cache/composer
All caches cleared.
PHP Warning:  Module "curl" is already loaded in Unknown on line 0
PHP Warning:  Module "gd" is already loaded in Unknown on line 0
PHP Warning:  Module "imap" is already loaded in Unknown on line 0
Do not run Composer as root/super user! See https://getcomposer.org/root for details
Continue as root/super user [yes]? yes
Cache directory does not exist (cache-vcs-dir): 
Cache directory does not exist (cache-repo-dir): 
Cache directory does not exist (cache-files-dir): 
Clearing cache (cache-dir): /root/.cache/composer
All caches cleared.
root@tcargobr:~/api-btz-tcargo# cd /var/www/
root@tcargobr:/var/www# mkdir info
root@tcargobr:/var/www# ls
html  html2  info
root@tcargobr:/var/www# cd info/
root@tcargobr:/var/www/info# nano index.php
root@tcargobr:/var/www/info# 
