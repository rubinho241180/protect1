FROM php:5.6-apache
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY . /var/www/html/
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    DirectoryIndex controller.php\n\
</Directory>' >> /etc/apache2/apache2.conf
RUN echo 'date.timezone = "America/Recife"' > /usr/local/etc/php/conf.d/timezone.ini
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/timezone.ini
RUN echo 'upload_max_filesize = 20M' >> /usr/local/etc/php/conf.d/timezone.ini
RUN echo 'post_max_size = 20M' >> /usr/local/etc/php/conf.d/timezone.ini