FROM php:5.6-apache

RUN a2enmod rewrite

COPY . /var/www/html/

RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    DirectoryIndex controller.php\n\
</Directory>' >> /etc/apache2/apache2.conf