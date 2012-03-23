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
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=zostatus">Zend Optimizer����״̬</a>  |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=apcstatus">APC����״̬</a>  |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=info">PHP info</a> |
		<a href="<?=$_SERVER['SCRIPT_NAME'];?>?act=redis">Redis info</a> 
<?php
if (isset($_GET['act']) && 'functions' === $_GET['act']) {
	echo '<pre>';
	print_r(get_defined_functions());
	echo '</pre>';
} else if (isset($_GET['act']) && 'zostatus' === $_GET['act']) {
	echo '<pre>';
	if (function_exists('accelerator_get_status')) {
	    print_r(accelerator_get_status());
	} else {
		echo 'ϵͳû�а�װZend optimizer';
	}
	echo '</pre>';
} else if (isset($_GET['act']) && 'apcstatus' === $_GET['act']) {
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
} else if (isset($_GET['act']) && 'info' === $_GET['act']) {
	phpinfo();
} else if (isset($_GET['act']) && 'redis' === $_GET['act']) {
	$redis  = new Redis();
	$redis->connect('127.0.0.1', 6379);
	if (isset($_GET['do']) && 'clear' === $_GET['do']) {
		$redis->flushAll();
		header('location:' . $_SERVER['HTTP_REFERER']);
		exit();
	}
	// set a test value
	$redis->setex('testkey', 720, 'i am value');
	$all_keys = $redis->keys('*');
	echo '<h2><a href="' . $_SERVER['SCRIPT_NAME'] . '?act=redis&do=clear">������л���</a></h2>';
	echo '<h2>Redis Info</h2>';
	$info = $redis->info();
	echo '<table border="1">';
	foreach ($info as $k => $v) 
		echo '<tr><td>' . $k . "</td><td>" . $v . "</td></tr>";
	echo '</table>';

	echo '<h2>���еĻ�������</h2>';
	echo '<table border="1">';
	foreach ($all_keys as $v) 
		echo '<tr><td>' . $v . "</td><td>" . $redis->get($v) . "</td></tr>";
	echo '</table>';
}
?>
	</body>
</html>
