#!/bin/bash


for i in $(ls -d /var/www); do
    if [ ! -f /etc/apache2/sites-available/$i.conf ]; then 
        echo "Creating vhost for $i" 
        echo "<VirtualHost *:80>" > /etc/apache2/sites-available/$i.conf && \
        echo "    ServerName $i" >> /etc/apache2/sites-available/$i.conf && \
        echo "    DocumentRoot /var/www/$i" >> /etc/apache2/sites-available/$i.conf && \
        echo "    <Directory /var/www/$i>" >> /etc/apache2/sites-available/$i.conf && \
        echo "        Options Indexes FollowSymLinks MultiViews" >> /etc/apache2/sites-available/$i.conf && \
        echo "        AllowOverride All" >> /etc/apache2/sites-available/$i.conf && \
        echo "        Order allow,deny" >> /etc/apache2/sites-available/$i.conf && \
        echo "        allow from all" >> /etc/apache2/sites-available/$i.conf && \
        echo "    </Directory>" >> /etc/apache2/sites-available/$i.conf && \
        echo "</VirtualHost>" >> /etc/apache2/sites-available/$i.conf
    fi
    a2ensite $i.conf
done

mkdir -p /etc/apache2/ssl
for i in $(ls -d /var/www); do
    if [ ! -f /etc/apache2/ssl/$i.key ] || [ ! -f /etc/apache2/ssl/$i.crt ]; then
        echo "Creating ssl cert for $i"
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/apache2/ssl/$i.key -out /etc/apache2/ssl/$i.crt -subj "/C=ES/ST=Vitoria/L=Alava/O=ImAleex/CN=$i"
    fi

    if [ ! -f /etc/apache2/sites-available/$i-ssl.conf ]; then
        echo "Creating ssl vhost for $i"
        echo "<VirtualHost *:443>" > /etc/apache2/sites-available/$i-ssl.conf && \
        echo "    DocumentRoot /var/www/$i" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "    ServerName $i" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "    <Directory /var/www/$i>" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "        Options Indexes FollowSymLinks MultiViews" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "        AllowOverride All" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "        Order allow,deny" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "        allow from all" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "    </Directory>" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "    SSLEngine on" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "    SSLCertificateFile /etc/apache2/ssl/$i.crt" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "    SSLCertificateKeyFile /etc/apache2/ssl/$i.key" >> /etc/apache2/sites-available/$i-ssl.conf && \
        echo "</VirtualHost>" >> /etc/apache2/sites-available/$i-ssl.conf
    fi
    a2ensite $i-ssl.conf
done

/usr/sbin/apache2ctl -D FOREGROUND