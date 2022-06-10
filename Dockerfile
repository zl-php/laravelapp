FROM richarvey/nginx-php-fpm:1.10.4

RUN sed -i "s/pm.max_children = [0-9]\+/pm.max_children = 64/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/pm.start_servers = [0-9]\+/pm.start_servers = 8/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/pm.min_spare_servers = [0-9]\+/pm.min_spare_servers = 8/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/pm.max_spare_servers = [0-9]\+/pm.max_spare_servers = 32/g" /usr/local/etc/php-fpm.d/www.conf \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && sed -i 's/session.save_handler = files/session.save_handler = redis\nsession.save_path = "tcp:\/\/redis:6379"/g' /usr/local/etc/php/php.ini \
    && sed -i 's/session.gc_maxlifetime = 1440/session.gc_maxlifetime = 14400/g' /usr/local/etc/php/php.ini \
    && sed -i 's/memory_limit = 128M/memory_limit = 1024M/g' /usr/local/etc/php/conf.d/docker-vars.ini \
    && sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
    && apk add --no-cache lua-resty-core nginx-mod-http-lua \
    && docker-php-ext-install sockets bcmath \
    && composer self-update

# Copy our nginx config
RUN rm -Rf /etc/nginx/nginx.conf
COPY conf/nginx.conf /etc/nginx/nginx.conf
COPY conf/nginx-site.conf /etc/nginx/conf.d/default.conf

# Copy our nginx ssl
COPY conf/ssl /etc/nginx/ssl

# Copy our supervisord config
COPY conf/supervisord.conf /etc/supervisord.conf

# Copy our shell
COPY start.sh /start.sh

COPY . /var/www/html

RUN cd /var/www/html \
    && composer config repos.packagist composer https://mirrors.cloud.tencent.com/composer/ \
    && composer install \
    && cp .env.local .env \
    && chown -Rf nginx.nginx /var/www/html \
    && chmod +x /start.sh

EXPOSE 80 443

WORKDIR /var/www/html

CMD ["/start.sh"]
