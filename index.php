<?php
header( 'Content-Type: text/html; charset=UTF-8' );
/**
 * @author : FeiYan.info
 * @copyright： free
 * @example：index.php?format=json&word=ni  输出“你、尼”等
 * 
 * @param : format: json/xml(default)  	--默认输出XML格式
 * @param : act: accurate/fuzzy(default)	--模糊查询参数可以是拼音、unicode或者汉字(用于反查)，精确查询使用search_index字段
 * @param : page: default 1				--分页页码
 * @param : size: default 10				--分页数量
 * @return : array(
 * 				"word"		=> 汉字
				"pinyin"	=> 拼音
				"pinyin_s"	=> 声母
				"pinyin_y"	=> 韵母
				"type"		=> 声调(1,2,3,4)
				"unicode"	=> 汉字unicode
 * 		   );
 */

define('IM_FEIYAN', true);
include_once 'config.php';	

$data['words'] = null;
$data_root = &$data['words'];

$word = strtolower(urldecode(Filter::http_get("word")));
if( empty($word) ){
	$data_root['result_code'] = 0;
	$data_root['desc'] = "Parameter Word Can't be Empty";
} else {
	$page = Filter::http_get("page");
	$page = !empty($page) && intval($page)>=1 ? intval($page) : 1;
	$size = Filter::http_get("size");
	$size = !empty($size) && intval($size)>=1 ? intval($size) : 10;
	$set = ( $page-1 )*$size;
	//fuzzy:模糊查询	accurate:精确查询
	$act  = Filter::http_get("act");
	$act = in_array($act,array("fuzzy","accurate")) ? $act : "fuzzy";
	if( $act == "fuzzy" ) {
		$sql = "SELECT COUNT(id) FROM words WHERE search_index LIKE '%{$word}%'";
		$total = $db->getone($sql);
		$pages = $total%$size==0 ? intval($total/$size) : intval($total/$size)+1;
		$sql = "SELECT * FROM words WHERE search_index LIKE '%{$word}%' LIMIT $set,$size";
		
	} elseif ( $act == "accurate" ) {
		$column = preg_match("/^[a-z]+$/",$word) ? "pinyin"
				: ( preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$word) ? "word" : "unicode");
		$sql = "SELECT COUNT(id) FROM words WHERE $column LIKE '%{$word}%'";
		$total = $db->getone($sql);
		$pages = $total%$size==0 ? intval($total/$size) : intval($total/$size)+1;
		$sql = "SELECT * FROM words WHERE $column LIKE '%{$word}%' LIMIT $set,$size";
	}
	$res = $db->getall($sql);
	if( empty($res) ){
		$data_root['result_code'] = 1;
		$data_root['desc'] = 'Data is Empty';
	} else {
		foreach ( $res as &$row )
		{
			$data_root["word"][] = array(
				"word"		=> $row["word"],
				"pinyin"	=> $row["pinyin"],
				"pinyin_s"	=> $row["pinyin_s"],
				"pinyin_y"	=> $row["pinyin_y"],
				"type"		=> $row["type"],
				"unicode"	=> "\"".$row["unicode"]."\""
			);
		}
	}
}

//导出XML或者json
output( $data );
?>