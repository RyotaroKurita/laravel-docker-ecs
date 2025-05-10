# Laravel 12 Docker Base

このリポジトリは、新規プロジェクトのための Laravel 12 環境を Docker を使用して構築するためのベースプロジェクトです。

## 必要要件

- Docker
- Docker Compose

## 技術構成

- PHP
- Apache
- MySQL
- Postfix

## セットアップ手順

1. Docker コンテナをビルドして起動します。
   初回起動時に Laravel のアプリケーションが自動的に作成されます。

    ```bash
    docker-compose up -d --build
    ```

2. `.env` ファイルはプロジェクトオーナーが用意したものを使用してください。<br>
   もし `.env` ファイルがない場合は、プロジェクトルートにある.env.init ファイルを利用してください。`.env.init`を利用する場合は、配置後に必ず下記コマンドを実行してください。 <br>

    ```bash
    # コンテナ内で実行
    php artisan key:generate
    ```

    ⚠️ `.env` ファイルの取り扱いについては、後述の注意事項に気をつけてください。⚠️

3. 必要に応じてデータベースのマイグレーションを実行します。

    ```bash
    # コンテナ内で実行
    php artisan migrate
    ```

## 使用方法

- アプリケーションは `http://localhost:3000` でアクセス可能です。
- `php artisan ~`や`composer ~`コマンドを実行する場合は、コンテナに （擬似的に）ssh で接続すると実行しやすいです。

  ```bash
  docker-compose exec -it コンテナ名 /bin/bash
  ```

## 各コンテナのブラウザ URL

- メイン: `http://localhost:3000`
- phpMyAdmin: `http://localhost:8080`
- Mailpit: `http://localhost:4000`

## ディレクトリ構成

```
laravel12-docker-base/
├── .github/            # GitHub関連の設定ファイル
├── docker/             # Docker関連の設定ファイル
├── mailpit/            # Mailpit関連（初回コンテナ起動後）
├── src/                # Laravelのソースコード（初回コンテナ起動後）
├── .env.init           # 初期環境変数ファイル
├── .gitignore          # Git管理から除外するファイル
├── docker-compose.yml  # Docker Compose設定ファイル
└── README.md           # このファイル
```

## 運用時の決まり事

環境変数を管理するために、`.env` ファイルを使用しています。セキュリティ上の理由から、このファイルは必ず暗号化/複合化して管理/運用してください。
環境別にファイル名を分けて管理します。
- 開発環境　　　　：`.env.local`
- ステージング環境：`.env.staging`
- 本番環境　　　　：`.env.production`

暗号化/復号化のためのコマンドは以下の通りです。<br>
（`.env.key`は Git 管理外なので、プロジェクトオーナーからもらってください。各個人で生成しないでください。）

※ プロジェクトオーナーは、プロジェクトルートで下記コマンドを実行して`.env.key`を生成してください。

```bash
head -c 32 /dev/urandom | base64 | sed 's/^/base64:/' > .env.key
```

暗号化（例：`.env.local`を暗号化して`.env.local.encrypted`を生成）

```bash
php artisan env:encrypt --key=$(cat .env.key) --env=local (--force)
```

復号化（例：`.env.local.encrypted`を復号化して`.env.local`を生成）

```bash
php artisan env:decrypt --key=$(cat .env.key) --env=local
```

## 余談

Postfix の送信テスト

```bash
php artisan tinker --execute="Mail::raw('Postfix 経由のテストです', function(\$m){ \$m->to('you@example.com')->subject('Postfix メールテスト'); });"
```
