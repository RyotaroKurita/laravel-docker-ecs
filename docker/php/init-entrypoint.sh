#!/bin/bash
set -e

# Laravel未初期化なら create-project を実行
if [ ! -f /var/www/html/artisan ]; then
  echo "Laravel プロジェクトが存在しません。初期化します..."
  composer create-project --prefer-dist laravel/laravel:12 .
  chown -R apache:apache .
fi

# Laravelが既に存在するが、vendorが無ければcomposer install
if [ -f /var/www/html/artisan ] && [ ! -d /var/www/html/vendor ]; then
  echo "依存パッケージをインストールします..."
  composer install
  chown -R apache:apache .
fi

# マイグレーション実行（失敗してもスキップ）
if [ -f /var/www/html/artisan ]; then
  set +e
  echo "MySQL の起動を待っています..."
  for i in {1..10}; do
    if mysqladmin ping -h"$DB_HOST" --silent; then
      echo "MySQL に接続できました"
      break
    fi
    echo "接続失敗、再試行中 ($i/10)..."
    sleep 3
  done
  echo "マイグレーションを実行します..."
  php artisan migrate --force || echo "マイグレーション失敗"
  set -e
fi

# Maildir ディレクトリの作成（postfix + Maildir対応ユーザー用）
if [ ! -d /etc/skel/Maildir ]; then
  mkdir -p /etc/skel/Maildir/{new,cur,tmp}
  chmod -R 700 /etc/skel/Maildir/
fi

# FPMディレクトリが必要
mkdir -p /run/php-fpm

composer dump-autoload

# サービス起動
php-fpm --daemonize
postfix start
exec httpd -DFOREGROUND
