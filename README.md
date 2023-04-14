# yii2-jwt-storage-redis

Redis storage component for kozlovsv/yii2-jwt-auth.

Component provides token storage in redis cache

This component should be used if your application uses Redis cache, and you need functionality that allows you
to delete all saved access and refresh tokens for a specific user.

The standard application cache component, which is used in the TokenStorageCache class,
converts all keys to an MD5 hash before writing or reading, so there is no way to get all the keys for a specific user.
All keys in the cache look like: 402849137963e1945d01315c4f61662f

The TokenStorageRedis component executes the Redis commands directly, and does not use the commands of the standard cache class.
Due to this, the keys are not formatted in MD5, but are stored in the cache in their original form.

Fotrat of the key api:token:user_id:token_id.

Example: api:token:1425:9f3898ca702c9c9f4991c63dc7eb0e13.

This formatting allows you to get all the keys for a specific user that are stored in the cache by the search mask.
For example, api:token:1425:* will retrieve all stored tokens for user ID 1425.

This makes it possible to remove all of that user's keys from the store.

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
public function afterSave($insert, $changedAttributes)
    // Purge the user tokens when the password is changed
    if (array_key_exists('password_hash', $changedAttributes)) {
        /** @var Jwt $jwtServ */
        $jwtServ = Yii::$app->get('jwt', false);
        $jwtServ?->tokenStorage->deleteAllForUser(1);
    }

    parent::afterSave($insert, $changedAttributes);
}
```