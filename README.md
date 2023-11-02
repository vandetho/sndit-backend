# Sndit

<a href="https://kromb.io">
<img src="public/images/icon-256x256.png" width="256" alt="Kromb Logo" />
</a>

Sndit is a system design for package delivery tracking which can be used by small businesses and people who want to
understand software architecture.

Check the result https://sndit.io

## Sponsored by :

<a href="https://kromb.io">
<img src="public/images/kromb_logo.png" width="150" alt="Kromb Logo" />
</a>

## Enhanced Entity Relationship (EER) Model

This section will provide you the visual representation of the relationships among the tables in the project model.

Coming Soon...

## Workflows

- Below is the package tracking workflow
![package.svg](workflows/package.svg)

- Below is the helpdesk ticketing workflow
![ticket.svg](workflows/ticket.svg)

## How To Use This Package:

In this section, you will know how to install and use this package.

### Debian :

#### Prerequisites:

- A server running Debian 10+.
- A root password is configured on your server.

1. Getting Started

- Before starting, it is recommended to update your server with the latest version using the following command:
``` 
sudo apt-get update -y
sudo apt-get upgrade -y
```

- Install wget package.
```
sudo apt install wget
```


2. Install Nginx

- Install the Nginx web server.

```
sudo apt install build-essential libpcre3-dev libssl-dev zlib1g-dev libgd-dev
wget https://nginx.org/download/nginx-1.25.1.tar.gz
tar -xzvf nginx-1.25.1.tar.gz
cd nginx-1.25.1
./configure --prefix=/var/www/html --sbin-path=/usr/sbin/nginx --conf-path=/etc/nginx/nginx.conf --http-log-path=/var/log/nginx/access.log --error-log-path=/var/log/nginx/error.log --with-pcre  --lock-path=/var/lock/nginx.lock --pid-path=/var/run/nginx.pid --with-http_ssl_module --with-http_image_filter_module=dynamic --modules-path=/etc/nginx/modules --with-http_v2_module --with-http_v3_module --with-stream=dynamic --with-http_addition_module --with-http_mp4_module
```

Here’s what the options mean:
  - --prefix=/var/www/html: Sets the root directory for the install.
  - --sbin-path=/usr/sbin/nginx: Sets where the nginx program goes.
  - --conf-path=/etc/nginx/nginx.conf: Chooses the main NGINX configuration file location.
  - --http-log-path=/var/log/nginx/access.log and --error-log-path=/var/log/nginx/error.log: Define where the log files are.
  - --with-pcre: Turns on PCRE (Perl Compatible Regular Expressions) for configuration files.
  - --lock-path=/var/lock/nginx.lock and --pid-path=/var/run/nginx.pid: Set locations for the lock and pid files.
  - --with-http_ssl_module: Activates the SSL module for secure web connections.
  - --with-http_image_filter_module=dynamic: Turns on the image filter module.
  - --modules-path=/etc/nginx/modules: Defines where dynamic modules go.
  - --with-http_v2_module: Turns on the HTTP/2 module.
  - --with-stream=dynamic: Dynamically activates the stream module.
  - --with-http_addition_module and --with-http_mp4_module: Turn on the addition and MP4 modules.

If you don’t want to use the HTTP/3 module, just leave out --with-http_v3_module. HTTP/3 offers faster, more reliable web browsing.

After configuring the options for building NGINX from the source, it’s time to compile and install NGINX. This is a two-step process:

First, the make command compiles the NGINX source code using the options specified in the ./configure script. This creates the NGINX binary executable:

```
make
sudo make install
```

- Start the Nginx service.
Create a new systemd service file:

```
sudo nano /etc/systemd/system/nginx.service
```
Add the following lines:
```
[Unit]
Description=The NGINX HTTP and reverse proxy server
After=syslog.target network-online.target remote-fs.target nss-lookup.target
Wants=network-online.target

[Service]
Type=forking
PIDFile=/var/run/nginx.pid
ExecStartPre=/usr/sbin/nginx -t
ExecStart=/usr/sbin/nginx
ExecReload=/usr/sbin/nginx -s reload
ExecStop=/bin/kill -s QUIT $MAINPID
PrivateTmp=true

[Install]
WantedBy=multi-user.target
```

Reload daemon
```
sudo systemctl daemon-reload
```

Start nginx service
```
sudo systemctl start nginx
```

- Enable the Nginx service to start at system reboot.

```
sudo systemctl enable nginx
```

- Check the Nginx version to verify the installation.

```
sudo nginx -v
```

- You should see output like this:

```
nginx version: nginx/1.25.1
```

3. Configure the Firewall

- Install the firewall

```
sudo apt install ufw
```

- List the available application profiles.

```
sudo ufw app list
```

- Among the other entries, you should see the following profiles:
  - The Nginx **Full** profile opens both HTTPS (443) and HTTP (80) ports.
  - The Nginx **HTTP** profile opens the HTTP (80) port only.
  - The Nginx **HTTPS** profile opens the HTTPS (443) port only.

```
Nginx Full
Nginx HTTP
Nginx HTTPS
```

- Allow the Nginx Full profile in the firewall. Certbot requires ports 80 and 443 to install a Let's Encrypt TLS/SSL
  certificate.

```
sudo ufw allow 'Nginx Full'
sudo ufw allow 'OpenSSH'
sudo ufw allow 'SSH'
```

To activate ufw
```
sudo ufw enable
```

- Check the Firewall status.

```
sudo ufw status
```

- You should see output like this:

```
To                         Action      From
--                         ------      ----
Nginx Full                 ALLOW       Anywhere
SSH                        ALLOW       Anywhere
OpenSSH                    ALLOW       Anywhere
22                         ALLOW       Anywhere
Nginx Full (v6)            ALLOW       Anywhere (v6)
SSH (v6)                   ALLOW       Anywhere (v6)
OpenSSH (v6)               ALLOW       Anywhere (v6)
22 (v6)                    ALLOW       Anywhere (v6)
```

4. Create an Nginx Virtual Host

- Remove the default Nginx configuration.

```
sudo rm -rf /etc/nginx/sites-enabled/default
sudo rm -rf /etc/nginx/sites-available/default
```

5. Install MySQL 8
- Download mysql package
```
wget https://dev.mysql.com/get/mysql-apt-config_0.8.25-1_all.deb
```

- Install the release package.
```
sudo apt install ./mysql-apt-config_0.8.25-1_all.deb
```

- Now you can install MySQL.
```
sudo apt update
sudo apt install mysql-server
```

- Once the installation is completed, the MySQL service will start automatically. To verify that the MySQL server is running.
```
sudo service mysql status
```

- The output should show that the service is enabled and running:
```
mysql.service - MySQL Community Server
     Loaded: loaded (/lib/systemd/system/mysql.service; enabled; vendor preset: enabled)
     Active: active (running) since Wed 2022-02-02 06:12:30 UTC; 17s ago
       Docs: man:mysqld(8)
             http://dev.mysql.com/doc/refman/en/using-systemd.html
   Main PID: 101929 (mysqld)
     Status: "Server is operational"
      Tasks: 38 (limit: 1148)
     Memory: 369.3M
        CPU: 805ms
     CGroup: /system.slice/mysql.service
             └─101929 /usr/sbin/mysqld

Feb 02 06:12:29 demo systemd[1]: Starting MySQL Community Server...
Feb 02 06:12:30 demo systemd[1]: Started MySQL Community Server.
```

- Secure Mysql
```
sudo mysql_secure_installation
```

6. Install PHP 8.2

- After updating packages, now install the dependencies:
```
sudo apt install software-properties-common ca-certificates lsb-release apt-transport-https 
```

- Enable SURY Repository
```
sudo sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
```

- Importing the GPG key for the repository
```
wget -qO - https://packages.sury.org/php/apt.gpg | sudo apt-key add - 
sudo apt update 
```

- Install PHP 8.2
```
sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-mysql php8.2-common php8.2-curl php8.2-intl php8.2-curl php8.2-zip php8.2-fpm php8.2-gd php8.2-imagick
```

- Checking php version
```
php -v 
```
```
Output
----------
PHP 8.2.12 (cli) (built: Jan 13 2023 10:43:08) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.12, Copyright (c) Zend Technologies
    with Zend OPCache v8.2.12, Copyright (c), by Zend Technologies
```

7. Set up symfony

- Update php.ini
```
sudo nano /etc/php/8.2/fpm/php.ini
```

- Change the following lines:
```
memory_limit = 256M
cgi.fix_pathinfo = 0
safe_mode = Off
max_execution_time = 120
max_input_time = 300
date.timezone = "Asia/Phnom_Penh"
```

- Install Composer
```
sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

- Install Git
```
sudo apt install git
```

- Clone the repo
```
cd /var/www
sudo git clone https://github.com/vandetho/sndit-backend
```
- Next, set proper permission on Sndit project:
```
sudo chown -R www-data: /var/www/sndit-backend
```

- Creating local environment
```
sudo cp .env .env.local
```

- Update the environments file to fit your settings and change
```
APP_ENV=prod
```

- Install vendor
```
sudo composer install --no-dev --optimize-autoloader
```

- Generate it php file to speed up it performance
```
sudo composer dump-env prod
```

- Install migration
```
sudo php bin/console doctrine:migrations:migrate 
```

- Build our interface
Refer to this https://github.com/nodesource/distributions#debian-versions for install nodejs and npm
```
sudo npm install --legacy-peer-deps
sudo npm run build
```

- Create a Nginx virtual host configuration file. Replace `sndit.io` with your domain
  name.

```
sudo nano /etc/nginx/sites-available/sndit.io
```

- Paste this into the file. Replace `sndit.io` with your domain name.

```
server {
    listen 80;
    listen [::]:80;

    server_name sndit.io;
    root /var/www/sndit-backend/public;
    index index.php;
    client_max_body_size 100m;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php {
        try_files $uri /index.php =404;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_index index.php;
        include fastcgi_params;
      }

    location ~ /\.(?:ht|git|svn) {
        deny all;
    }
}
```
- Check Nginx for any syntax error with the following command
```
sudo nginx -t
```

- Enable the new Nginx configuration. Replace `sndit.io` with your domain name.
```
sudo ln -s /etc/nginx/sites-available/sndit.io /etc/nginx/sites-enabled/sndit.io
```

- Reload the Nginx service and PHP-FPM.
```
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```
7. Install SSL for free
For this step, We will use `certbot`.

Certbot is a free, open source software tool for automatically using Let’s Encrypt certificates on manually-administrated websites to enable HTTPS.

For more information: https://certbot.eff.org/

- Install snapd
```
sudo apt update
sudo apt install snapd
```

- Install the `core` snap in order to get the latest `snapd`.
```
sudo snap install core
```

- Install certbot
```
sudo snap install --classic certbot
```

- Prepare the Certbot command
```
sudo ln -s /snap/bin/certbot /usr/bin/certbot
```
- Choose how you'd like to run Certbot
  
Either get and install your certificates...
```
sudo certbot --nginx
```
- Test automatic renewal
```
sudo certbot renew --dry-run
```
- The command to renew certbot is installed in one of the following locations:
   - `/etc/crontab/`
   - `/etc/cron.*/*`
   - `systemctl list-timers`
