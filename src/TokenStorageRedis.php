<?php

namespace kozlovsv\jwtredis;

use kozlovsv\jwtauth\TokenStorageCache;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\redis\Connection;

/**
 * Service provides token storage in redis cache
 *
 * The standard methods of working with the cache are overridden, since in the basic cache component
 * the keys are encoded by the MD5 algorithm when writing and reading.
 * Because of this, there is no way to get all the keys for a specific user by the search mask.
 * Now writing-reading to the cache occurs directly with Redis commands.
 *
 * Thanks to this, it became possible to implement the deleteAllForUser function
 *
 * @package kozlovsv\jwtauth
 * @author Kozlov Sergey <kozlovsv78@gmail.com>
 */
class TokenStorageRedis extends TokenStorageCache
{
    /**
     * @var yii\redis\Connection
     */
    protected $redis ;

    /**
     * @inheritDoc
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->cache->redis) || ! ($this->cache->redis instanceof Connection)) {
            throw new InvalidConfigException('The application cache component  "Yii::$app->cache" must be instance of yii\redis\Cache.');
        }
        $this->redis = $this->cache->redis;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _exists(string $key): bool{
        return (bool) $this->redis->executeCommand('EXISTS', [$key]);
    }

    /**
     * @inheritDoc
     */
    protected function setValue(string $key, $value, int $duration): bool
    {
        if ($duration == 0) {
            return (bool) $this->redis->executeCommand('SET', [$key, $value]);
        }
        $duration = (int) ($duration * 1000);
        return (bool) $this->redis->executeCommand('SET', [$key, $value, 'PX', $duration]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function deleteValue(string $key): bool {
        return (bool) $this->redis->executeCommand('DEL', [$key]);
    }

    /**
     * Delete all tokens for user from storage
     * @param int $userId User Id
     * @return bool True if token deleted
     */
    public function deleteAllForUser(int $userId): bool
    {
        //todo implemet coming soon
        return false;
    }
}