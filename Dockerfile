FROM php:7.2-cli
ENV COMPOSER_ALLOW_SUPERUSER 1


VOLUME ["/app"]
WORKDIR /app
RUN apt-get update && apt-get install -y git

RUN mkdir /composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
  && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig
RUN php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }"
RUN php /tmp/composer-setup.php --install-dir=/composer --filename=composer



CMD ["-"]
#ENTRYPOINT [ "php", "./bin/publisher.php" ]
