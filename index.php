<?php

// PascalToUnderline XxxYyy to xxx_yyy
function PascalToUnderline(string $str) : string {
    $tmp = '';
	$j = false;
	$num = strlen($str);
	for ($i = 0; $i < $num; $i++ ){
        $d = substr($str,$i,1);
		if ($i > 0 && $d >= 'A' && d <= 'Z' && $j) {
            $tmp .= '_';
		}
		if ($d != '_') {
            $j = true;
		}
		$tmp .= $d;
	}
	return strtolower($tmp);
}

// UnderlineToPascal xxx_yyy to XxxYyy
function UnderlineToPascal(string $str) : string {
    $tmp = '';
    $length = strlen($str);
    $nextLetterNeedToUpper = true;
    for ($i = 0; $i < $length; $i++ ){
        $d = substr($str,$i,1);
        if ($d == '_'){
            $nextLetterNeedToUpper = true;
            continue;
        }
        if ($nextLetterNeedToUpper && $d >= 'a' && $d <= 'z'){
            $tmp .= strtoupper($d);
        }else{
            $tmp .= $d;
        }
        $nextLetterNeedToUpper = false;
    }
    return $tmp;
}


/**
 * 使用PDO连接数据库
 * @param string $host
 * @param int $port
 * @param string $dbname
 * @param string $username
 * @param string $password
 * @param array $options
 * @return PDO
 */
function db(string $host = '127.0.0.1',int $port = 3306, string $dbname = 'test', string $username = 'root', string $password = '', array $options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]) : PDO {
    return new PDO('mysql:host='.$host.';port='.$port.';dbname='.$dbname,$username,$password,$options);
}

$host = '127.0.0.1';
$port = 3306;
$dbname = 'test';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
// 模型文件写入磁盘地址, 注意以 '/' 结尾方便拼接模型文件路径
$model_write_dir = './model/';
$pdo = db($host,$port,$dbname,$username,$password,$options);

$GetAllTableSql = sprintf("SELECT * FROM `information_schema`.`TABLES` WHERE ( `TABLE_SCHEMA` = '%s' AND `TABLE_TYPE` = 'BASE TABLE' );", $dbname);
$tables = $pdo->query($GetAllTableSql)->fetchAll();

// var_dump($tables);

$ModelTemplate = file_get_contents('./model.tmp');

if (!is_dir($model_write_dir)){
    mkdir($model_write_dir,0755,true);
}

foreach ($tables as $t){
    $table = $t['TABLE_NAME'];
    $comment = $t['TABLE_COMMENT'];
    $TableUnderlineToPascal = UnderlineToPascal($table);
    // 模型模板内容 注释表名 注释表注释 类名注释 类名 属性表名
    $content = sprintf($ModelTemplate,$table,$comment,$TableUnderlineToPascal,$TableUnderlineToPascal,$table);
    file_put_contents($model_write_dir.$TableUnderlineToPascal.'.php',$content);
}
