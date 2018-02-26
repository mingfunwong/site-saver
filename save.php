<?php 
$site_url = 'http://www.swiper.com.cn';
$redirect_url = from($_SERVER, 'REDIRECT_URL');
$pathinfo = pathinfo($redirect_url);
$dirname = from($pathinfo, 'dirname');
$basename = from($pathinfo, 'basename', 'index.html');
$fullpath = ".{$dirname}/{$basename}";

if (file_exists($fullpath)) {
	exit(file_get_contents($fullpath));
}

$target_url = "{$site_url}{$redirect_url}";
$contents = curl($target_url);
$contents = str_replace($site_url, "", $contents);
mk_dir(".{$dirname}");
file_put_contents("{$fullpath}", $contents);
exit($contents);

/**
 * 递归创建目录
 * 
 * @access public
 * @param mixed $dir
 * @param int $mode
 * @return bool
 */
function mk_dir($dir, $mode = 0755) 
{ 
    if (is_dir($dir) || @mkdir($dir, $mode)) return true; 
    if (!mk_dir(dirname($dir), $mode)) return false; 
    return @mkdir($dir, $mode); 
}

/**
* 获得数组指定键的值
*
* @param array,object $array
* @param string $key
* @param mixed $default
* @return mixed
*/
function from($array, $key, $default = FALSE)
{
	$return = $default;
	if (is_object($array)) $return = (isset($array->$key) === TRUE && empty($array->$key) === FALSE) ? $array->$key : $default;
	if (is_array($array)) $return = (isset($array[$key]) === TRUE && empty($array[$key]) === FALSE) ? $array[$key] : $default;

	return $return;
} 

// curl 数据
function curl($url, $method = "GET", $data = ''){
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 

    if ($data) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $info = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Errno'.curl_error($ch);
    }
    curl_close($ch);
    return $info;
}