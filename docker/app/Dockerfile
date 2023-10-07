FROM php:8.1-fpm

ARG user=web
ARG uid=1000

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli -y


RUN apt-get update
#RUN apt-get install -y python3
#RUN apt-get -y install python3-pip
#RUN apt-get install libgl1 -y
#RUN pip3 install opencv-python
RUN apt install ffmpeg -y

RUN apt-get update  && apt-get -y install zip libzip-dev wget \
zlib1g-dev unzip libpq-dev git-core libmcrypt-dev vim libfcgi0ldbl gnupg \
libfreetype6-dev libjpeg62-turbo-dev libpng-dev libicu-dev libmagickwand-dev libmagickcore-dev

#NODE & NPM
RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs
RUN apt-get install -y aptitude
RUN aptitude install -y npm
RUN npm install --global yarn

#INSTALL COMPOSER
RUN echo "memory_limit = -1" > /usr/local/etc/php/conf.d/memory_limit.ini
RUN curl -sS http://getcomposer.org/installer | php -- --filename=composer && chmod a+x composer  && mv composer /usr/local/bin/composer
RUN echo 'export PATH=~/.composer/vendor/bin:$PATH' >> ~/.bashrc

#ENABLE DOCKER LIBS
RUN docker-php-ext-install zip pdo pdo_pgsql pdo_mysql pgsql opcache -j$(nproc) gd
RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-configure opcache --enable-opcache

#INSTALL XDEBUG
RUN pecl install xdebug-3.1.3 && docker-php-ext-enable xdebug
#XDEBUG

#INSTALL REDIS
RUN pecl install redis && docker-php-ext-enable redis
#REDIS

#IMAGICK
#RUN printf "\n" | pecl install imagick
#RUN docker-php-ext-enable imagick

#EXIF
RUN docker-php-ext-install exif

#NPM STUFF SETUP
RUN rm -fr ./node_modules \
RUN npm install

#MAKE tmp WRITABLE
RUN chmod 777 -R /tmp && chmod o+t -R /tmp

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/user user
RUN mkdir -p /home/user/.composer && \
    chown -R user:user /home/user

# Set working directory
WORKDIR /var/www

USER user
