<?php

namespace kozlovsv\jwtredis;

use kozlovsv\jwtauth\TokenStorageCache;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\redis\Connection;

/**
 * Service provides token storage in redis cache
 *
 * When the [[set]] method is called, the cache key by which the token was stored in a special list in redis chache.
 * @see https://redis.io/docs/data-types/lists/
 *
 * The list is associated with a specific user by its ID.
 * This list will allow you to delete all tokens for a specific user when calling the [[deleteAllForUser]] method
 * This functionality will allow you to log out from all user devices that have been authorized
 *
 * @package kozlovsv\jwtauth
 * @author Kozlov Sergey <kozlovsv78@gmail.com>
 */
class TokenStorageRedis extends TokenStorageCache
{
    /**
     * @var yii\redis\Connection|string|array
     */
    protected $redis = 'redis';

    /**
     * @inheritDoc
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->redis = Instance::ensure($this->redis, Connection::class);
    }


    /**
     * Build formated cache key and with MD5 hashed
     *
     * @param int $userId User Id
     * @param string $tokenId Unique token ID, for example hash MD5 or SHA-1, store in jti claim JWT tocken
     * @return string MD5 formated hashed cache key
     */
    protected function buildKey(int $userId, string $tokenId): string
    {
        return  md5(parent::buildKey($userId, $tokenId));
    }

    /**
     * Build MD5 hashed key for user keys list.
     *
     * @param int $userId User Id
     * @return string
     */
    protected function buildKeyForList(int $userId): string
    {
        return  md5(parent::buildKeyForUser($userId) . 'kl');
    }

    /**
     * Save token to storage
     * @param int $userId User Id
     * @param string $tokenId Unique token ID, for example hash MD5 or SHA-1, store in jti claim JWT tocken
     * @param int $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * @return bool True if token exists
     */
    public function set(int $userId, string $tokenId, int $duration = 0): bool
    {
        $key = $this->buildKey($userId, $tokenId);
        //store key in  token ids list for user
        $listKey = $this->buildKeyForList($userId);
        $this->removeNonExistKeys($listKey);
        //Add token key to the end of the list for user.
        $this->redis->rpush($listKey, $key);
        return parent::set($userId, $duration);
    }


    /**
     * Delete all tokens for user from storage
     * @param int $userId User Id
     * @return bool True if token deleted
     */
    public function deleteAllForUser(int $userId): bool
    {
        //Not implemented because the standard Cache component does not have required functionality
        //todo implemet coming soon
        return false;
    }

    /**
     * Checking, if the key not exists in the cache, delete it from list
     * @param string $listKey
     * @return void
     */
    protected function removeNonExistKeys(string $listKey)
    {
        $list = $this->redis->lrange($listKey, 0, -1);
        //todo implemet coming soon
    }
}