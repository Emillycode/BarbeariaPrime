# ============================================================
# Barbearia Prime — imagem Docker para deploy (Render, Railway,
# Fly.io, ou qualquer host compatível com containers)
# ============================================================
FROM php:8.2-apache

# Extensões PHP necessárias (conexão com MySQL)
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite headers

# Configurações de PHP recomendadas para produção
RUN { \
        echo 'expose_php = Off'; \
        echo 'display_errors = Off'; \
        echo 'log_errors = On'; \
        echo 'session.cookie_httponly = On'; \
        echo 'session.use_strict_mode = On'; \
    } > /usr/local/etc/php/conf.d/producao.ini

# Habilita o uso de .htaccess (é o que bloqueia o acesso direto às pastas
# config/, includes/ e sql/, e aplica os cabeçalhos de segurança)
COPY docker/htaccess-enable.conf /etc/apache2/conf-available/htaccess-enable.conf
RUN a2enconf htaccess-enable

# Script que ajusta o Apache para escutar na porta que o Render define
# em tempo de execução (variável $PORT). Fica fora do webroot.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copia apenas os arquivos da aplicação (não o Dockerfile, docker-compose,
# README etc. — assim nada de "bastidores" do deploy fica publicamente
# acessível pela web).
WORKDIR /var/www/html
COPY admin/    ./admin/
COPY api/      ./api/
COPY config/   ./config/
COPY assets/   ./assets/
COPY css/      ./css/
COPY includes/ ./includes/
COPY js/       ./js/
COPY sql/      ./sql/
COPY *.php     ./
COPY *.html    ./
COPY favicon.ico ./favicon.ico
COPY .htaccess ./.htaccess

# Garante que o Apache consiga ler os arquivos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
