FROM ubuntu:latest

# Environment variable for tzdata to avoid interactive prompt
ENV DEBIAN_FRONTEND=noninteractive


# Install OpenSSL, Apache and ondrej/php PPA
RUN apt-get update && apt-get install -y openssl apache2 && \
    apt-get install -y software-properties-common dos2unix && \
    add-apt-repository ppa:ondrej/php
RUN apt update -y && apt install -y php8.0 libapache2-mod-php8.0 php8.0-mysql php8.0-common php8.0-curl

# Enable SSL
RUN a2enmod ssl && a2enmod rewrite

# Set php.ini max upload size to 100MB and max post size to 100MB
RUN sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 100M/g' /etc/php/8.0/apache2/php.ini
RUN sed -i 's/post_max_size = 8M/post_max_size = 100M/g' /etc/php/8.0/apache2/php.ini


# Copy script and make it executable
COPY ./start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh && dos2unix /usr/local/bin/start.sh


# Expose port 80 and 443
EXPOSE 80 443

# Run script on container start
CMD ["/bin/bash", "/usr/local/bin/start.sh"]

