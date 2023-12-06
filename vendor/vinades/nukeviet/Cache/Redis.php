<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2023 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

namespace NukeViet\Cache;

use Redis as CRedis;

/**
 * NukeViet\Cache\Redis
 *
 * @package NukeViet\Cache
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @version 4.5.00
 * @access public
 */
class Redis
{
    private $_Lang = 'vi';

    private $_Cache_Prefix = '';

    private $_Db;

    private $_Cache;

    /**
     * __construct()
     *
     * @param string $Host
     * @param int    $Port
     * @param int    $Timeout
     * @param string $Password
     * @param int    $DBnumber
     * @param string $Lang
     * @param string $Cache_Prefix
     */
    public function __construct($Host, $Port, $Timeout, $Password, $DBnumber, $Lang, $Cache_Prefix)
    {
        $this->_Lang = $Lang;
        $this->_Cache_Prefix = $Cache_Prefix;

        $redis = new CRedis();

        $connected = false;
        if ($redis->pconnect($Host, $Port, $Timeout) === true) {
            $connected = true;
        } elseif ($redis->connect($Host, $Port, $Timeout) === true) {
            $connected = true;
        }
        if ($connected !== true) {
            trigger_error('Can not connect to Redis server!', 256);
        }

        if (!empty($Password) and $redis->auth($Password) !== true) {
            trigger_error('Can not Authenticate Redis server!', 256);
        }

        if ($redis->select($DBnumber) !== true) {
            trigger_error('Can not connect to Redis DB!', 256);
        }

        $checkOptions = [];
        $checkOptions[] = $redis->setOption(CRedis::OPT_PREFIX, $Cache_Prefix);
        $checkOptions[] = $redis->setOption(CRedis::OPT_SERIALIZER, CRedis::SERIALIZER_PHP);

        foreach ($checkOptions as $opt) {
            if ($opt !== true) {
                trigger_error('Can not set Redis option!', 256);
            }
        }

        $this->_Cache = $redis;
    }

    /**
     * delAll()
     *
     * @param bool $sys
     */
    public function delAll($sys = true)
    {
        $this->_Cache->flushDb();
    }

    /**
     * delMod()
     *
     * @param string $module_name
     * @param string $lang
     */
    public function delMod($module_name, $lang = '')
    {
        $AllKeys = $this->_Cache->keys(str_replace('-', '\-', $module_name) . '*');

        foreach ($AllKeys as $key) {
            $this->_Cache->del(substr($key, strlen($this->_Cache_Prefix)));
        }
    }

    /**
     * getItem()
     *
     * @param string $module_name
     * @param string $filename
     * @param int    $ttl
     * @return mixed
     */
    public function getItem($module_name, $filename, $ttl = 0)
    {
        // Note: $ttl not check in Redis cache
        return $this->_Cache->get($module_name . '_' . md5($filename));
    }

    /**
     * setItem()
     *
     * @param string $module_name
     * @param string $filename
     * @param string $content
     * @param int    $ttl
     */
    public function setItem($module_name, $filename, $content, $ttl = 0)
    {
        return $this->set($module_name . '_' . md5($filename), $content, $ttl);
    }

    /**
     * setDb()
     *
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->_Db = $db;
    }

    /**
     * db()
     *
     * @param string $sql
     * @param string $key
     * @param string $modname
     * @param string $lang
     * @param int    $ttl
     * @return array
     */
    public function db($sql, $key, $modname, $lang = '', $ttl = 0)
    {
        if (empty($sql)) {
            return [];
        }

        if (empty($lang)) {
            $lang = $this->_Lang;
        }

        $cache_key = $modname . '_' . $lang . '_' . md5($sql . '_' . $this->_Cache_Prefix);
        if (($cache = $this->_Cache->get($cache_key)) !== false) {
            return $cache;
        }

        if (($result = $this->_Db->query($sql)) === false) {
            return [];
        }

        $a = 0;
        $cache = [];
        while ($row = $result->fetch()) {
            $key2 = (!empty($key) and isset($row[$key])) ? $row[$key] : $a;
            $cache[$key2] = $row;
            ++$a;
        }
        $result->closeCursor();
        $this->set($cache_key, $cache, $ttl);

        return $cache;
    }

    /**
     * set()
     *
     * @param string $key
     * @param string $value
     * @param int    $ttl
     */
    private function set($key, $value, $ttl = 0)
    {
        $this->_Cache->set($key, $value);

        if ($ttl > 0) {
            $this->_Cache->expire($key, $ttl);
        }
    }
}
