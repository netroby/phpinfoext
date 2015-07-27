<?php
$base_url = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset=UTF-8>
    <title>PHP info extensive</title>
    <style>
        body, pre {font-size: 12px}
        .main-menu li {width: 100px;float: left;list-style: none;margin: 2px;border: #CCC 1px solid;}
        .main-menu li > a {padding: 5px 10px;display: block; background-color: #EEE; }
        .main-menu li > a:hover {color: #FFF;background-color: #019;}
        .clearfix:after {content: ".";display: block;clear: both;visibility: hidden;line-height: 0;height: 0;}
        .clearfix {width:100%;}
        html[xmlns] .clearfix {display: block;}
        * html .clearfix {height: 1%;}
    </style>
</head>
<body>
<ul class="main-menu">
    <li><a href="<?=$base_url;?>?act=extensions">Extensions</a></li>
    <li><a href="<?=$base_url;?>?act=functions">Functions</a></li>
    <li><a href="<?=$base_url;?>?act=ocstatus">OPCache</a></li>
    <li><a href="<?=$base_url;?>?act=info">PHP info</a></li>
    <li><a href="<?=$base_url;?>?act=memcache">Memcached</a></li>
    <li><a href="<?=$base_url;?>?act=redis">Redis</a></li>
</ul>
<div class="clearfix"></div>
<?php
if (isset($_GET['act'])) {
    switch ($_GET['act']) {
    case 'extensions':
        act_extensions();
        break;
    case 'functions':
        act_functions();
        break;
    case 'ocstatus':
        act_ocstatus();
        break;
    case 'info':
        act_info();
        break;
    case 'memcache':
        act_memcache();
        break;
    case 'redis':
        act_redis();
        break;
    default:
        act_home();
        break;
    }
} else {
    act_home();
}

function act_home() {
    echo '<p>';
    echo 'Click links above to see detail! Folk me on github: <a href="https://github.com/netroby/phpinfoext">https://github.com/netroby/phpinfoext</a>';
    echo 'Powered by netroby <a href="https://twitter.com/netroby">@twitter</a> <a href="http://weibo.com/netroby">@weibo</a>';
    echo '</p>';
}

function act_extensions()
{
    echo '<pre>';
    foreach (get_loaded_extensions() as $k => $v) {
        echo ($k+1) . ".\t<a href=\"http://php.net/" . $v . "\">" . $v . "</a>" . PHP_EOL;
    }
    echo '</pre>';
}

function act_functions()
{
    echo '<pre>';
    foreach (get_defined_functions() as $k => $v) {
        echo $k ." => <pre>";
        if (is_array($v)) {
            foreach ($v as $ck => $cv) {
                $k = $ck+1;
                echo $k . ".\t<a href=\"http://php.net/" . $cv . "\">" . $cv . "</a>" . PHP_EOL;
            }
        } else {
            echo   "\t<a href=\"http://php.net/" . $v . "\">" . $v . "</a>" . PHP_EOL;
        }
        echo "</pre>";
    }
    echo '</pre>';
}

function act_ocstatus()
{
    echo '<pre>';
    if (function_exists('opcache_get_status')) {
        print_r(opcache_get_status());
    } else {
        echo 'OPCache not loaded';
    }
    echo '</pre>';
}

function act_memcache()
{
    echo '<pre>';
    if (!class_exists('Memcached'))
        exit('Memcached not loaded');
    $m = new Memcached();
    $m->addServer('127.0.0.1', 11211);
    $a = $m->getAllKeys();
    print_r($a);
    if (is_array($a)) {
        foreach ($a as $k) {
            echo "\n\n" . $k . ":" . var_export($m->get($k), TRUE);
        }
        echo "\n\n";
    }
    $s = $m->getStats();
    print_r($s);
    echo '</pre>';
}

function act_info()
{
    echo '<div>';
    phpinfo();
    echo '</div>';
}

function act_redis()
{
    if (!class_exists('Redis')) {
        exit('<p>Redis not exists</p>');
    }
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    if (isset($_GET['do']) && 'clear' === $_GET['do']) {
        $redis->flushAll();
        header('location:' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    // set a test value
    $redis->setex('testkey', 720, 'i am value');
    echo '<h2><a href="' . $_SERVER['SCRIPT_NAME'] . '?act=redis&do=clear">fluch all redis cache</a></h2>';
    echo '<h2>Redis Info</h2>';
    $info = $redis->info();
    echo '<table border="1">';
    $db_idx = array();
    foreach ($info as $k => $v) {
        echo '<tr><td>' . $k . "</td><td>" . $v . "</td></tr>";
        if (preg_match("/db(\d+)/is", $k, $m)) {
            $db_idx[] = $m[1];
        }
    }
    echo '</table>';


    foreach ($db_idx as $idx) {
        $redis->select($idx);
        $all_keys = $redis->keys('*');
        echo "<h2>Database{$idx} Caches:</h2>";
        echo '<table border="1">';
        foreach ($all_keys as $v)
            echo '<tr><td>' . $v . "</td><td>" . $redis->get($v) . "</td></tr>";
        echo '</table>';
    }
}

?>
</body>
</html>
