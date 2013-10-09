<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sekjun9878
 * Date: 1/10/13
 * Time: 3:05 PM
 * To change this template use File | Settings | File Templates.
 */

require 'vendor/autoload.php';

define("HOME", $_SERVER['DOCUMENT_ROOT']."/_SERVERS/");

if(!is_dir(HOME))
{
	mkdir(HOME);
}

$app = new \Slim\Slim(array(
	'mode' => 'development',
	'debug' => true,
	'cookies.encrypt' => true,
	'cookies.lifetime' => '20 minutes',
	'cookies.secret_key' => 'ThisIsNotSoSecretSoChangeIt'
));
$app->setName('MCPECake Daemon');
if(file_exists(HOME.'init.lock'))
{
	$Database = new PDO('sqlite:'.HOME.'database.sqlite3');
	$Database->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);
	$AuthKeys = $Database->query("SELECT * FROM authkeys");
	$AuthKeys = $AuthKeys->fetch(PDO::FETCH_ASSOC);//Get the first row
	$app->add(new \Slim\Extras\Middleware\HttpBasicAuth($AuthKeys['key1'], hash('sha512', $AuthKeys['key2'].':'.idate('i').':'.$AuthKeys['key3'])));
}

$app->get('/ping', function () {
	echo "pong";
});

$app->get('/init', function () use ($app) {
	set_time_limit(180);

	if(file_exists(HOME.'init.lock'))
	{
		$app->halt(403, 'Daemon Already Initialised');
	}
	$Database = new PDO('sqlite:'.HOME.'database.sqlite3');

	//TODO: Consider removing below in production
	$Database->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);

	//TODO: remove IF NOT EXISTS for safety purposes
	$Database->exec("CREATE TABLE servers (
                    id INTEGER PRIMARY KEY AUTOINCREMENT ,
                    pm_version TEXT,
                    authkey TEXT
                    )");

	$Database->exec("CREATE TABLE authkeys (
                    key1 TEXT PRIMARY KEY,
                    key2 TEXT,
                    key3 TEXT
                    )");

	$key = array();
	$key[0] = strtoupper(bin2hex(mcrypt_create_iv(128, MCRYPT_DEV_URANDOM)));
	sleep(1);
	$key[1] = strtoupper(bin2hex(mcrypt_create_iv(128, MCRYPT_DEV_URANDOM)));
	sleep(1);
	$key[2] = strtoupper(bin2hex(mcrypt_create_iv(128, MCRYPT_DEV_URANDOM)));

	$stmt = $Database->prepare("INSERT INTO authkeys (key1, key2, key3)
					VALUES (:key1, :key2, :key3)
					");

	$stmt->bindValue(':key1', $key[0]);
	$stmt->bindValue(':key2', $key[1]);
	$stmt->bindValue(':key3', $key[2]);
	$stmt->execute();

	//Create an init.lock file requiring authentication on next attempts.
	file_put_contents(HOME.'init.lock', '');

	mkdir(HOME.'bin');
	mkdir(HOME.'data');
	file_put_contents(HOME.'bin/PocketMine_Ctrl.sh', file_get_contents('http://gist.github.com/sekjun9878/ee8b939484c6e4fcd95b/raw/PocketMine_Ctrl.sh'));
	//_put_contents(HOME.'bin/php', file_get_contents('http://gist.github.com/sekjun9878/ee8b939484c6e4fcd95b/raw/PocketMine_Ctrl.sh'));

	$app->response->write(base64_encode(json_encode(array(
		'status' => true,
		'authkeys' => $key,
	))));
});

$app->post('/servers/create', function () use ($app) {//TODO: Set up an automatic system to fix file permissions
	$Database = new PDO('sqlite:'.HOME.'database.sqlite3');
	$Database->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);/*
	$stmt = $Database->query("SELECT * FROM servers WHERE id=:id LIMIT 0,1");
	$stmt->execute();

	$stmt->bindValue(':id', $_POST['new_server_id']);
	if($stmt->fetch(PDO::FETCH_ASSOC))
	{
		$app->halt(409, 'Server Already Exists');
	}*/

	/*$serverprop = json_decode(base64_decode($_POST['server-properties']));
	file_put_contents(HOME."data/".$_POST['new_server_id'].'/server.properties', \Spyc::YAMLDump($serverprop));*/

	//exec('cp -r '.HOME.'templates/PocketMine/'.$_POST['PocketMine_Version'].'/');

	if(!isset($_POST['PocketMine-Version']))
	{
		$_POST['PocketMine-Version'] = '1.3.10dev';
	}

	$authkey = strtoupper(bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)));

	$stmt = $Database->prepare("INSERT INTO servers (pm_version, authkey) VALUES (:pm_version, :authkey)");
	$stmt->bindValue(':pm_version', $_POST['PocketMine-Version']);
	$stmt->bindValue(':authkey', $authkey);//TODO: Catch Exception
	$stmt->execute();

	$id = $Database->lastInsertId();

	mkdir(HOME."data/".$id);

	$app->response->setStatus(201);
	$app->response->write(base64_encode(json_encode(array(
		'id' => $id,
		'authkey' => $authkey,
	))));
});

$app->post('/servers/edit/:id/:config', function ($id, $config) use ($app) {//TODO: Set up an automatic system to fix file permissions
	$Database = new PDO('sqlite:'.HOME.'database.sqlite3');
	$Database->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);
	$stmt = $Database->query("SELECT * FROM servers WHERE id=:id LIMIT 0,1");
	$stmt->execute();

	$stmt->bindValue(':id', $id);
	$server = $stmt->fetch(PDO::FETCH_ASSOC);
	if(!$server)
	{
		$app->halt(404, 'Server Does Not Exist');
	}

	if($_POST['authkey'] != $server['authkey'])
	{
		$app->halt(403, 'Invalid Authkey');
	}

	switch(strtolower($config))
	{
		case 'serverconfig':
			file_put_contents(HOME."data/".$id.'/server.properties', \Spyc::YAMLDump(json_decode(base64_decode($_POST['data']))));
			break;
		default:
			$app->halt(400, 'Unknown Config');
	}

	$app->response->setStatus(200);
});

$app->delete('/servers/delete/:id', function ($id) use ($app) {
	try
	{
		$Database = new PDO('sqlite:'.HOME.'database.sqlite3');
		$Database->setAttribute(PDO::ATTR_ERRMODE,
			PDO::ERRMODE_EXCEPTION);

		$stmt = $Database->prepare("SELECT * FROM servers WHERE id=:id");
		$stmt->bindValue(':id', $id);//TODO: Catch Exception
		$stmt->execute();

		$server = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!$server)
		{
			$app->halt(404, 'Server Not Found');
		}

		if($server['authkey'] != $_POST['authkey'])
		{
			$app->halt(403, 'Invalid Authkey');
		}

		foreach (scandir(HOME.'data/'.$id) as $item) {
			if ($item == '.' || $item == '..') continue;
			unlink(HOME.'data/'.$id.DIRECTORY_SEPARATOR.$item);
		}
		rmdir(HOME.'data/'.$id);

		$stmt = $Database->prepare("DELETE FROM servers WHERE id=:id AND authkey=:authkey");
		$stmt->bindValue(':id', $id);
		$stmt->execute();
	}
	catch (Exception $e)
	{
		$app->halt(500, 'Server Delete Unsuccessful');
	}

	$app->response->write("Server Delete Successful");
});

$app->post('/servers/start/:id', function ($id) use ($app) {
	$Database = new PDO('sqlite:'.HOME.'database.sqlite3');
	$Database->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);

	$stmt = $Database->prepare("SELECT * FROM servers WHERE id=:id");
	$stmt->bindValue(':id', $id);//TODO: Catch Exception
	$stmt->execute();

	$server = $stmt->fetch(PDO::FETCH_ASSOC);

	if(!$server)
	{
		$app->halt(404, 'Server Not Found');
	}

	if($server['authkey'] != $_POST['authkey'])
	{
		$app->halt(403, 'Invalid Authkey');
	}

	if(!isset($_POST['PocketMine-Version']))
	{
		$_POST['PocketMine-Version'] = '1.3.10dev';
	}

	foreach (scandir(HOME.'bin') as $item) {
		if ($item == '.' || $item == '..') continue;
		chmod(HOME.'bin'.DIRECTORY_SEPARATOR.$item, 0777);
	}
	chmod(HOME.'bin', 0777);

	foreach (scandir(HOME.'data/'.$id) as $item) {//TODO: Remove this line
		if ($item == '.' || $item == '..') continue;
		chmod(HOME.'data/'.$id.DIRECTORY_SEPARATOR.$item, 0777);
	}
	chmod(HOME.'data/'.$id, 0777);

	$options = array(
		'PHP' => HOME.'bin/php',
		'PHP_OPTS' => "--data-path ".HOME.'data/'.$id.'/'." -d enable_dl=On -d date.timezone=Australia/Adelaide",
		'PMMP' => HOME.'bin/'.$_POST['PocketMine-Version'].'/PocketMine-MP.php'
	);
	$string = "";
	foreach($options as $key => $value)
	{
		$string .= $key.'="'.$value.'" ';
	}
	if(exec($string.HOME."bin/PocketMine_Ctrl.sh ".$id." start") == "Start: Done.")
	{
		$app->response->write("Server Start Successful");
	}
	else
	{
		$app->halt(500, 'Server Start Unsuccessful');
	}
});

$app->post('/servers/stop/:id', function ($id) use ($app) {
	$Database = new PDO('sqlite:'.HOME.'database.sqlite3');
	$Database->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION);

	$stmt = $Database->prepare("SELECT * FROM servers WHERE id=:id");
	$stmt->bindValue(':id', $id);//TODO: Catch Exception
	$stmt->execute();

	if(!$stmt->fetch(PDO::FETCH_ASSOC))
	{
		$app->halt(404, 'Server Not Found');
	}

	$options = array(
		'PHP' => HOME.'bin/php',
		'PHP_OPTS' => "--data-path ".HOME.'data/'.$id.'/'." -d enable_dl=On -d date.timezone=Australia/Adelaide",
		'PMMP' => HOME.'bin/'.$_POST['PocketMine-Version'].'/PocketMine-MP.php'
	);
	$string = "";
	foreach($options as $key => $value)
	{
		$string .= $key.'="'.$value.'" ';
	}
	if(exec($string.HOME."bin/PocketMine_Ctrl.sh ".$id." stop") == "Stop: Done.")
	{
		$app->response->write("Server Stop Successful");
	}
	else
	{
		$app->halt(500, 'Server Stop Unsuccessful');
	}
});

$app->run();
