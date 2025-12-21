Laravel OTP Demo

**Getting started**

```bash
git clone git@github.com:mtvbrianking/laravel-otp.git

cd laravel-otp

# Laravel v8 works best on PHP v7.4
php7.4 -v

# Fix this to 7.4
composer update

# post-autoload-dump
php7.4 artisan package:discover --ansi

# post-update-cmd
php7.4 artisan vendor:publish --tag=laravel-assets --ansi --force

npm install

cp -rp .env.example .env

vi .env

php7.4 artisan key:generate

php7.4 artisan migrate

php7.4 artisan serve
```

**Tutorials**

- https://dev.to/roxie/how-to-add-google-s-two-factor-authentication-to-a-laravel-8-application-4jjp

- https://shouts.dev/laravel-two-factor-authentication-with-google-authenticator

- https://www.youtube.com/watch?v=4osPfTw-FB0

- https://www.youtube.com/watch?v=Gj-fHq4NPXk
