<?php
namespace itdq;

use Cache\Adapter\Memcached\MemcachedCachePool;

class Cache
{
    protected $client;
    protected $pool;

    function __construct()
    {
        if (extension_loaded('memcached')) {    
            $this->client = new \Memcached();
            // $memcache->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
            if (!count($this->client->getServerList())) {
                $this->client->addServers(array(
                    array($_ENV['memcached_ip'], $_ENV['memcached_port'])
                ));
            }
            $this->pool = new MemcachedCachePool($this->client);
        } else {
            throw new \Exception('No memcached module intalled.');
        }
    }

    function testConnection(){

        $testKey = $_ENV['environment'].'_test';

        if ($this->pool->setDirectValue($testKey, 'test')) {
            return true;
        } else {
            echo $this->client->getResultCode();
            echo $this->client->getResultMessage();
            throw new \Exception('Memcached call errored.');
        }
    }

    function getPool(){
        return $this->pool;
    }
}
?>