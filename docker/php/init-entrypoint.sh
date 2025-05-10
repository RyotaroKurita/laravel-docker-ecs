#!/bin/bash
set -e

# Laravel未初期化なら create-project を実行
if [ ! -f /var/www/html/artisan ]; then
  echo "Laravel プロジェクトが存在しません。初期化します..."
  composer create-project --prefer-dist laravel/laravel:12 .
  chown -R apache:apache .
fi

# Maildir ディレクトリの作成（postfix + Maildir対応ユーザー用）
if [ ! -d /etc/skel/Maildir ]; then
  mkdir -p /etc/skel/Maildir/{new,cur,tmp}
  chmod -R 700 /etc/skel/Maildir/
fi

# FPMディレクトリが必要
mkdir -p /run/php-fpm


# サービス起動
php-fpm --daemonize
postfix start
exec httpd -DFOREGROUND