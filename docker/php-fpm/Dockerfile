ARG environment=development

##############################
#     Build dependencies     #
##############################
FROM gamingplatform/php-fpm:7.4-development as builder

ARG environment=development

WORKDIR /project

COPY /docker/php-fpm/composer-install.sh /docker/php-fpm/composer-install-after-code-copy.sh /
COPY /composer.json /composer.lock /project/
RUN /composer-install.sh

COPY / /project
RUN /composer-install-after-code-copy.sh

##############################
#       Build php-fpm        #
##############################
FROM gamingplatform/php-fpm:7.4-$environment

ARG environment=development

WORKDIR /project

COPY /docker/php-fpm/entrypoint.sh /docker/php-fpm/cache-warmup.sh /

COPY --from=builder /project /project

RUN /cache-warmup.sh

COPY /docker/php-fpm/${environment}.ini /usr/local/etc/php/conf.d/

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
