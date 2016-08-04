<?php
/**
 * Created by PhpStorm.
 * User: zhxia84@gmail.com
 * Date: 8/3/16
 * Time: 10:04 AM
 */

namespace App\Service;

use App\Log;

ini_set('default_socket_timeout', -1);

class Prefetch
{
    const SECRET_KEY = '2A7PRWQ1CK0OIVG648NB9FMSHELXD5UJT3YZ';
    const TRY_COUNT = 3;
    private static $counter = array();
    /**
     * @var \Redis
     */
    private static $redis = null;

    public static function doPrefetch($params)
    {
        if (empty($params)) {
            return false;
        }
        Log::trace('Request data arrived:' . var_export($params, true));
        $url = self::buildPrefetchURL($params);
        return self::doHttpRequest($url);
    }

    private static function doHttpRequest($url)
    {
        $URLInfo = self::parseURL($url);
        $shc = new swoole_http_client($URLInfo['host'], $URLInfo['port']);
        $shc->get($URLInfo['path'], function ($cli) use ($url) {
            Log::trace('Request response:' . $cli->body);
            $key = md5($url);
            $cnt = Prefetch::getValue($key);
            if ($cli->statusCode == 200) {
                $resp = json_decode($cli->body);
                $data = array('url' => $url, 'try_count' => $cnt, 'resp' => $resp);
                if (isset($resp['code']) && $resp['code'] === 0) {
                    Prefetch::updateData($data);
                } else {
                    if ($cnt >= Prefetch::TRY_COUNT) {
                        Prefetch::updateData($data);
                    } else {
                        Prefetch::incrValue($key);
                        Prefetch::doHttpRequest($url);
                    }
                }

            } else {
                if ($cnt >= Prefetch::TRY_COUNT) {
                    Prefetch::updateData(array('url' => $url, 'try_count' => $cnt));
                } else {
                    Prefetch::incrValue($key);
                    Prefetch::doHttpRequest($url);
                }
            }
        });
        return true;
    }

    private static function updateData($data)
    {
        Log::trace('update data:' . var_export($data, true));
        $key = md5($data['url']);
        unset(self::$counter[$key]); //释放掉 计数器
        $tryCount = (int)$data['try_count'];
        $arrUrl = parse_url($data['url']);
        $api = ltrim($arrUrl['path'], '/');
        $success = false;
        $hasData = false;
        $hasPush = false;
        if (isset($data['resp']['code']) && $data['resp']['code'] === 0) {
            $success = true;
            if ($data['resp']['data']) {
                $hasData = true;
            }
            if (isset($data['resp']['has_new']) && $data['resp']['has_new']) {
                $hasPush = true;
            }
        }
        if (self::$redis == null) {
            $config = \swoole::$php->config['commom'];
            self::$redis = new \Redis();
            self::$redis->pconnect($config['redis']['host'], $config['redis']['port'], 5);
            self::$redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
        }
        $now = time();
        $apiDayTotalKey = sprintf('800_80_01_%s_%s', date('md', $now), $api);
        self::$redis->hIncrBy($apiDayTotalKey, 'try_count', $tryCount);
        self::$redis->hIncrBy($apiDayTotalKey, 'succ_total', $success ? 1 : 0);
        self::$redis->hIncrBy($apiDayTotalKey, 'has_data', $hasData ? 1 : 0);
        self::$redis->hIncrBy($apiDayTotalKey, 'has_push', $hasPush ? 1 : 0);
        self::$redis->expireAt($apiDayTotalKey, $now + 86400);

    }

    private static function parseURL($url)
    {
        $arrURL = parse_url($url);
        $arr = array(
            'host' => $arrURL['host'],
            'port' => $arrURL['port'],
            'path' => $arrURL['path']
        );
        if ($arrURL['query']) {
            $arr['path'] .= '?' . $arrURL['query'];
        }
        return $arr;
    }

    public static function getValue($key)
    {
        return isset(self::$counter[$key]) ? self::$counter[$key] : 1;
    }

    public static function incrValue($key)
    {
        if (isset(self::$counter[$key])) {
            self::$counter[$key] += 1;
        } else {
            self::$counter[$key] = 1;
        }
    }

    private static function buildPrefetchURL($params)
    {
        $api = $params['api'];
        $params['prefetch'] = 1;
        $params['appid'] = 1;
        $params['reqfrom'] = 1;
        $url = 'http://127.0.0.1/' . $api . '?' . http_build_query($params);
        return $url . '&sign=' . self::getSign($url);
    }

    private static function getSign($url)
    {
        $requestUrl = preg_replace('#http://([\w\W])*?/#', '', $url);
        return md5('/violation2/' . $requestUrl . self::SECRET_KEY);
    }
}