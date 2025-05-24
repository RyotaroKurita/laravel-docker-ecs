FROM amazonlinux:2023

# DNF による必要パッケージのインストール
RUN dnf update -y && \
    dnf install -y \
    php php-fpm php-cli php-mbstring php-pdo php-mysqlnd php-xml php-common php-json php-gd php-opcache php-zip \
    httpd unzip git postfix mariadb105 && \
    dnf clean all

#Composerのインストール
COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/composer
ENV PATH=$PATH:/composer/vendor/bin


# 各種設定ファイルの配置（後述のファイルを COPY、上書きさせるために接頭語にz-をつける）
COPY php-fpm-custom.conf /etc/php-fpm.d/z-custom.conf
COPY apache-custom.conf /etc/httpd/conf.d/z-custom.conf
COPY main.cf /etc/postfix/

# 旧 php.conf を無効化（削除はせず待避）
RUN if [ -f /etc/httpd/conf.d/php.conf ]; then mv /etc/httpd/conf.d/php.conf /etc/httpd/conf.d/php.conf.bak; fi

WORKDIR /var/www/html

# 起動スクリプト
COPY init-entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]