# yii2-jwt-storage-redis

Redis storage component for kozlovsv/yii2-jwt-auth.

Component provides token storage in redis cache

This component should be used if your application uses Redis cache, and you need functionality that allows you
to delete all saved access and refresh tokens for a specific user.

The component remembers the cache keys of saved tokens, in
list for a specific user. This allows you to remove all issued tokens for this user from the storage.
This will close access to all services for all issued tokens

For example, when you need to log out of all authorized devices for a specific user.

The default key storage component does not allow you to perform this functionality.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist kozlovsv/yii2-jwt-storage-redis "@dev"
```

or add

```
"kozlovsv/yii2-jwt-storage-redis": "@dev"
```

to the `require` section of your `composer.json` file.

## Dependencies

- PHP 7.2+
- [kozlovsv/yii2-jwt-auth](https://github.com/kozlovsv/yii2-jwt-auth)
- [yiisoft/yii2-redis 2.0.6+](https://github.com/yiisoft/yii2-redis)

## Basic usage

Edit `jwt` component to your configuration file,

```php
'components' => [
    'jwt' => [
        'class' => \kozlovsv\jwtauth\Jwt::class,
        ...
        'tokenStorage' => \kozlovsv\jwtredis\TokenStorageRedis::class, //Set Redis storage class
    ],
],
```

For examle add method  `afterSave()` to model `app\models\User`

    ```php
      coming soon
    ```