## Laravel-2cache 一款基于yac/apcu的laravel二级缓存扩展包
二级缓存器，基于APCu/Yac。
[Yac官方文档](https://www.php.net/manual/zh/book.yac)
[APCu官方文档](https://www.php.net/manual/zh/book.apcu)
#### 安装
```
    composer require lysice/laravel-smartcache
```
将服务提供者添加入`app.php` 
```
    'providers' => [
    ...
    \Lysice\Cache\CacheServiceProvider::class
    ]   
```

#### 配置
- `data_connection` Redis的数据连接,指定要同步数据到哪个Redis连接。
- `cache_type` 
内存缓存选择两种模式
```
  \Lysice\Cache\Constants::CACHE_TYPE_YAC 基于Yac 该选项需要安装php的yac扩展
  \Lysice\Cache\Constants::CACHE_TYPE_APCU 基于APCu 该选项需要安装apcu的扩展
```
- `sync_mode` 同步模式
```
    const SYNC_MODE_PUBSUB = 1; 异步订阅模式， 使用Redis的订阅来同步数据到Redis 该选项需要执行`php artisan 2cache:sync &` 且保证该命令高可用。
    const SYNC_MODE_SYNC = 2;   同步模式，当设置缓存时直接设置Redis数据。
    const SYNC_MODE_JOB = 3;    队列模式 将Redis的缓存设置任务分配到队列 该模式需要您开启队列且保证队列高可用。一般使用`supervisor`
```
- `pub_connection` 
Redis的订阅连接 当同步模式为`SYNC_MODE_PUBSUB`时使用。注意，Redis的订阅连接为阻塞连接 需要保证数据连接`data_connection`与`pub_connection`不是同一个连接。
这种模式的缺点是会浪费一个`Redis`的`DB`
- `redis_channel` 
Redis的订阅渠道 当同步模式为`SYNC_MODE_PUBSUB`时使用。
- `log` 是否记录简单日志

#### 使用
`CacheManager`的方法定义如下
```
remember(string $key, int $ttl, Callable $callback){}
```
如果您习惯使用`Facade`模式 首先加入Facade
```
    'aliases' => [
            'SecondaryCache' => \Lysice\Cache\SecondaryCache::class
        ],
```
然后使用:
```
        $result = SecondaryCache::remember('wa_p_' . $id, 100, function () {
            return  [
                'cached' => true
            ];
        });
```
或者您也可以直接按照如下使用
```
        app(CacheManager::class)->remember('wa_p_' . $id, getRandTtl(), function () use ($path) {
            return  [
                'cached' => 1
            ];
        });
```
需要注意的是 首次返回的数据为您自己定义的 `$callback`中的数据。如果您返回的是数组 则缓存时会将数据 `json_encode`后存储。
当您第二次访问，取到的数据为缓存数据，此时您应该将数据反序列化为数组。
另外，在laravel中大量使用了 collection，由于collection数据量太大，因此在开发中并未考虑`callback`返回`collection`的情况。建议使用者直接返回数组。
#### 单独使用
如果您觉得本扩展的remember方法不好用可以基于本扩展提供的类直接操作。
以下本扩展提供的类
- RedisInstance
- YacInstance
- APCu
以上三个类实现了`CacheConcern`接口 因此提供方法:
```
/**
    public function set($key, $value, $ttl); 设置缓存 支持数组。$key = ['key' => 'value'] $value = null

    public function update($key, $old, $new); 更新缓存

    public function clear(); 清除缓存

    public function decrease($key = '', $step = 1, $ttl = 0); 计数减少

    public function delete($keys); 删除缓存 

    public function exists($keys); 是否存在

    public function get($key); 获取缓存

    public function increase($key = '', $step = 1, $ttl = 0); 计数增加

    public function info(); 返回缓存信息
```

#### 如果本扩展帮助到了你 欢迎star。

#### 如果本扩展有任何问题或有其他想法 欢迎提 issue与pull request。
