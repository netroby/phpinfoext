<!DOCTYPE html>
<html>
	<head>
		<meta charset=GBK>
		<title>������Ϣ</title>
		<style>
			body, pre {font-size:10pt}
		</style>
	</head>
	<body>
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=functions">PHP���ú���</a> |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=ocstatus">opcache����״̬</a>  |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=apcstatus">APC����״̬</a>  |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=info">PHP info</a> |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=memcache">Memcached</a> |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=redis">Redis</a> 
  
<?php
if (isset($_GET['act'])) {
	switch ($_GET['act']) {
	case 'functions':
		act_functions();
		break;
	case 'ocstatus':
		act_ocstatus();
		break;
	case 'apcstatus':
		act_apcstatus();
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
}
function act_functions()  {
	echo '<pre>';
	print_r(get_defined_functions());
	echo '</pre>';
} 
function act_ocstatus() {
	echo '<pre>';
	if (function_exists('opcache_get_status')) {
	    print_r(opcache_get_status());
	} else {
		echo 'ϵͳû�а�װZend optimizer';
	}
	echo '</pre>';
} 
function act_apcstatus() {
	echo '<pre>';
	if (function_exists('apc_cache_info')) {
		echo '<h2>ϵͳ����</h2>';
	    print_r(apc_cache_info());
		echo '<h2>�û�����</h2>';
		print_r(apc_cache_info('user'));
	} else {
		echo 'ϵͳû�а�װAPC';
	}
	echo '</pre>';
} 
function act_memcache() {
	echo '<pre>';
	$m = new Memcached();
	$m->addServer('127.0.0.1', 11211); 
	$a = $m->getAllKeys();
    print_r($a);
	if (is_array($a)) {
	foreach ($a as $k)
		echo "\n\n".$k.":".var_export($m->get($k), TRUE);
	    echo "\n\n";
	}
	$s = $m->getStats();
	print_r($s);
	echo '</pre>';
}
function act_info() {
	phpinfo();
} 
function act_redis() {
	$redis  = new Redis();
	$redis->connect('127.0.0.1', 6379);
	if (isset($_GET['do']) && 'clear' === $_GET['do']) {
		$redis->flushAll();
		header('location:' . $_SERVER['HTTP_REFERER']);
		exit();
	}
	// set a test value
	$redis->setex('testkey', 720, 'i am value');
	echo '<h2><a href="' . $_SERVER['SCRIPT_NAME'] . '?act=redis&do=clear">������л���</a></h2>';
	echo '<h2>Redis Info</h2>';
	$info = $redis->info();
	echo '<table border="1">';
	$db_idx = array();
	foreach ($info as $k => $v)  {
		echo '<tr><td>' . $k . "</td><td>" . $v . "</td></tr>";
		if (preg_match("/db(\d+)/is", $k, $m)) {
			$db_idx[] = $m[1];
		}
	}
	echo '</table>';

	

	foreach ($db_idx as $idx) {
	$redis->select($idx);
	$all_keys = $redis->keys('*');
	echo "<h2>���ݿ�{$idx}��������</h2>";
	echo '<table border="1">';
	foreach ($all_keys as $v) 
		echo '<tr><td>' . $v . "</td><td>" . $redis->get($v) . "</td></tr>";
	echo '</table>';
	}
}
?>
	</body>
</html>
