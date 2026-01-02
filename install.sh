#!/bin/bash

# WizWiz XUI TimeBot Installation Script
# Modified and maintained by ErfanXRay
# GitHub: https://github.com/Erfan-XRay/wizwizxui-timebot
# Supports: Sanaei XUI Panel Only

if [ "$(id -u)" -ne 0 ]; then
    echo -e "\033[33mPlease run as root\033[0m"
    exit
fi

wait

echo -e "\e[32m
██     ██ ██ ███████ ██     ██ ██ ███████     ██   ██ ██    ██ ██ 
██     ██ ██    ███  ██     ██ ██    ███       ██ ██  ██    ██ ██ 
██  █  ██ ██   ███   ██  █  ██ ██   ███         ███   ██    ██ ██ 
██ ███ ██ ██  ███    ██ ███ ██ ██  ███         ██ ██  ██    ██ ██ 
 ███ ███  ██ ███████  ███ ███  ██ ███████     ██   ██  ██████  ██ 
\033[0m"
echo -e "    \e[31mModified by: \e[34mErfanXRay\033[0m"
echo -e "    \e[31mGitHub: \e[34mhttps://github.com/Erfan-XRay/wizwizxui-timebot\033[0m"
echo -e "    \e[31mSupports: \e[34mSanaei XUI Panel Only\033[0m\n"

echo -e "\e[32mInstalling WizWiz XUI TimeBot ... \033[0m\n"
sleep 3

# Update system
sudo apt update && apt upgrade -y
echo -e "\e[92mSystem updated successfully ...\033[0m\n"

# Install required packages
PKG=(
    lamp-server^
    libapache2-mod-php 
    mysql-server 
    apache2 
    php-mbstring 
    php-zip 
    php-gd 
    php-json 
    php-curl 
)

echo -e "\e[92mInstalling required packages ...\033[0m\n"
for i in "${PKG[@]}"
do
    dpkg -s $i &> /dev/null
    if [ $? -eq 0 ]; then
        echo "$i is already installed"
    else
        apt install $i -y
        if [ $? -ne 0 ]; then
            echo "Error installing $i"
            exit 1
        fi
    fi
done

echo -e "\n\e[92mPackages installed successfully ...\033[0m\n"

# Install phpMyAdmin
randomdbpasstxt69=$(openssl rand -base64 10 | tr -dc 'a-zA-Z0-9' | cut -c1-20)

echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password $randomdbpasstxt69" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password $randomdbpasstxt69" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password $randomdbpasstxt69" | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
sudo apt-get install phpmyadmin -y
sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-available/phpmyadmin.conf
sudo a2enconf phpmyadmin.conf
sudo systemctl restart apache2

wait

# Install additional PHP packages
sudo apt-get install -y php-soap
sudo apt-get install -y libapache2-mod-php
sudo apt-get install -y php-ssh2
sudo apt-get install -y libssh2-1-dev libssh2-1

# Enable and start services
sudo systemctl enable mysql.service
sudo systemctl start mysql.service
sudo systemctl enable apache2
sudo systemctl start apache2

echo -e "\n\e[92mConfiguring firewall ...\033[0m\n"
ufw allow 'Apache'
sudo systemctl restart apache2

echo -e "\n\e[92mInstalling additional tools ...\033[0m\n"
sudo apt-get install -y git
sudo apt-get install -y wget
sudo apt-get install -y unzip
sudo apt install curl -y

sudo systemctl restart apache2.service

wait

# Clone repository
echo -e "\n\e[92mDownloading WizWiz XUI TimeBot ...\033[0m\n"
git clone https://github.com/Erfan-XRay/wizwizxui-timebot.git /var/www/html/wizwizxui-timebot
sudo chown -R www-data:www-data /var/www/html/wizwizxui-timebot/
sudo chmod -R 755 /var/www/html/wizwizxui-timebot/
echo -e "\n\033[33mWizWiz config and script have been installed successfully\033[0m"

wait

# Create random directory for panel
RANDOM_CODE=$(LC_CTYPE=C tr -dc 'a-zA-Z0-9' < /dev/urandom | head -c 40)
mkdir "/var/www/html/${RANDOM_CODE}"
echo "Directory created: ${RANDOM_CODE}"

cd /var/www/html/
wget -O wizwizpanel.zip https://github.com/Erfan-XRay/wizwizxui-timebot/releases/download/latest/wizwizpanel.zip 2>/dev/null || echo "Warning: Could not download panel zip"

if [ -f "wizwizpanel.zip" ]; then
    file_to_transfer="/var/www/html/wizwizpanel.zip"
    destination_dir=$(find /var/www/html -type d -name "*${RANDOM_CODE}*" -print -quit)

    if [ -z "$destination_dir" ]; then
        echo "Error: Could not find directory"
        exit 1
    fi

    mv "$file_to_transfer" "$destination_dir/" && yes | unzip "$destination_dir/wizwizpanel.zip" -d "$destination_dir/" && rm "$destination_dir/wizwizpanel.zip" && sudo chmod -R 755 "$destination_dir/" && sudo chown -R www-data:www-data "$destination_dir/"
fi

wait

# Setup database root password
if [ ! -d "/root/confwizwiz" ]; then
    sudo mkdir /root/confwizwiz
    sleep 1
    
    touch /root/confwizwiz/dbrootwizwiz.txt
    sudo chmod -R 777 /root/confwizwiz/dbrootwizwiz.txt
    sleep 1
    
    randomdbpasstxt=$(openssl rand -base64 10 | tr -dc 'a-zA-Z0-9' | cut -c1-30)

    ASAS="$"
    echo "${ASAS}user = 'root';" >> /root/confwizwiz/dbrootwizwiz.txt
    echo "${ASAS}pass = '${randomdbpasstxt}';" >> /root/confwizwiz/dbrootwizwiz.txt
    
    sleep 1

    passs=$(cat /root/confwizwiz/dbrootwizwiz.txt | grep '$pass' | cut -d"'" -f2)
    userrr=$(cat /root/confwizwiz/dbrootwizwiz.txt | grep '$user' | cut -d"'" -f2)

    sudo mysql -u $userrr -p$passs -e "alter user '$userrr'@'localhost' identified with mysql_native_password by '$passs';FLUSH PRIVILEGES;" 2>/dev/null
    echo "SELECT 1" | mysql -u$userrr -p$passs 2>/dev/null
fi

clear

echo " "
echo -e "\e[32m
██     ██ ██ ███████ ██     ██ ██ ███████     ███████ ███████ ██      
██     ██ ██    ███  ██     ██ ██    ███      ██      ██      ██      
██  █  ██ ██   ███   ██  █  ██ ██   ███       ███████ ███████ ██      
██ ███ ██ ██  ███    ██ ███ ██ ██  ███             ██      ██ ██      
 ███ ███  ██ ███████  ███ ███  ██ ███████     ███████ ███████ ███████ 
\033[0m"

echo -e "\e[92m
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║  ✅ WizWiz XUI TimeBot Installation Completed!              ║
║                                                              ║
║  📋 Next Steps:                                              ║
║                                                              ║
║  1. Open your browser and go to:                            ║
║     http://YOUR_SERVER_IP/wizwizxui-timebot/install/         ║
║                                                              ║
║  2. Follow the installation wizard                           ║
║                                                              ║
║  3. Make sure you have Sanaei XUI panel installed           ║
║                                                              ║
║  📝 Important Notes:                                         ║
║  • This version supports ONLY Sanaei XUI panel              ║
║  • Make sure your Sanaei XUI panel is up to date            ║
║  • You need a Telegram bot token from @BotFather            ║
║                                                              ║
║  🔗 GitHub: https://github.com/Erfan-XRay/wizwizxui-timebot  ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
\033[0m"

echo ""
echo -e "\e[33mInstallation script completed. Please continue with web installation.\033[0m"
echo ""

