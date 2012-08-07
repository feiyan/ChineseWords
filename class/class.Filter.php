<?php
class Filter {
	// 本方法用于返回一个已经过滤的，可以直接进行数据库操作的字符串，从 HTTP GET 方法
	// the function can filter a string from the method GET of HTTP.
	// public
	function http_get($var_name = null, $len = false) {
		// 如果没有相应的字符变量，则返回一个空字符串
		if (is_null($var_name) or !isset($_GET[$var_name])) return '';

		$hg = Filter :: filter_invalid($_GET[$var_name]);
		$hg = trim($hg);
		if ($len) {
			$hg = Filter :: m_cutstr($hg, $len);
		} 
		return $hg;
	} 
	// 本方法用于返回一个已经过滤的，可以直接进行数据库操作的字符串，从 HTTP POST 方法
	// the function can filter a string from the method POST of HTTP.
	// public
	function http_post($var_name = null, $len = false, $escape = false) {
		// 如果没有相应的字符变量，则返回一个空字符串
		if (is_null($var_name) or !isset($_POST[$var_name])) return '';

		$hg = Filter :: filter_invalid($_POST[$var_name]);
		$hg = trim($hg);

		if ($escape) {
			$hg = Filter :: js_unescape($hg);
		} 
		if ($len) {
			$hg = Filter :: m_cutstr($hg, $len);
		} 
		return $hg;
	} 
	// 本方法用于过滤一串给定的字符.
	// the function can filter a string from argument.
	// public
	function filter_invalid($fi = null) {
		// 如果没有相应的字符变量，则返回一个空字符串
		if (is_null($fi)) return ''; 
		// 转义特殊的 HTML 字符
		$fi = htmlspecialchars($fi); 
		// 转义 \n 成 <br>
		$fi = nl2br($fi); 
		// 如果没有魔术参数,那么就增加斜线
		if (!get_magic_quotes_gpc()) {
			$fi = addslashes($fi);
		} 

		return $fi;
	} 
	function br2nl($text) {
		$text = preg_replace('/<br\\\\s*?\\/??>/i', "\\n", $text);
		return str_replace("<br />", "\n", $text);
	} 

	/**
	 * 本方法用于解开 JS escape 传过来的参数
	 * public
	 * 
	 * 参数:
	 * $str : 需要 unescape 的字符串 
	 * 
	 * 返回：unescape 的值
	 */
	function js_unescape($str = '', $char = 'utf-8') {
		$str = trim($str);
		if (empty($str)) return $str;

		$ret = '';
		$len = strlen($str); 
		// 先把 · 替换成 . 要不然后边的内容会没有
		$str = str_replace('%B7' , '.' , $str);

		for ($i = 0; $i < $len; $i++) {
			if ($str[$i] == '%' && $str[$i + 1] == 'u') {
				$val = hexdec(substr($str, $i + 2, 4));

				if ($val < 0x7f) $ret .= chr($val);
				else if ($val < 0x800) $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val&0x3f));
				else $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6)&0x3f)) . chr(0x80 | ($val&0x3f));

				$i += 5;
			} else if ($str[$i] == '%') {
				$ret .= urldecode(substr($str, $i, 3));
				$i += 2;
			} else $ret .= $str[$i];
		} 
		if($char == 'gbk'){
			$ret = iconv("utf8" , 'gbk' , $ret);
		}
		return $ret;
	} 

	/**
	 * 截字，一个中国字算两个，一个英文字算一个
	 * 参数：$string 要截的字符,$length 截的长度
	 * 返回: 截好的字符串
	 */
	function cutstr($string, $length, $dot = ' ...', $charset = "utf-8") {
		if (strlen($string) <= $length) {
			return $string;
		} 

		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

		$strcut = '';
		if (strtolower($charset) == 'utf-8') {
			$n = $tn = $noc = 0;
			while ($n < strlen($string)) {
				$t = ord($string[$n]);
				if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1;
					$n++;
					$noc++;
				} elseif (194 <= $t && $t <= 223) {
					$tn = 2;
					$n += 2;
					$noc += 2;
				} elseif (224 <= $t && $t <= 239) {
					$tn = 3;
					$n += 3;
					$noc += 2;
				} elseif (240 <= $t && $t <= 247) {
					$tn = 4;
					$n += 4;
					$noc += 2;
				} elseif (248 <= $t && $t <= 251) {
					$tn = 5;
					$n += 5;
					$noc += 2;
				} elseif ($t == 252 || $t == 253) {
					$tn = 6;
					$n += 6;
					$noc += 2;
				} else {
					$n++;
				} 

				if ($noc >= $length) {
					break;
				} 
			} 
			if ($noc > $length) {
				$n -= $tn;
			} 

			$strcut = substr($string, 0, $n);
		} else {
			for($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
			} 
		} 

		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		return $strcut . $dot;
	} 
	// 转换为便携式UTF-8
	function portable_utf8($str = '', $char = 'utf-8') {
		$sublen = strlen($str);
		$retrunString = "";
		for ($i = 0;$i < $sublen;$i++) {
			if (ord($str[$i]) >= 127) {
				$tmpString = bin2hex(iconv($char, "ucs-2", substr($str, $i, 2)));
				$tmpString = substr($tmpString, 2, 2) . substr($tmpString, 0, 2); //window下可能要打开此项
				$retrunString .= "&#x" . $tmpString . ';';
				$i++;
			} else {
				$retrunString .= $str[$i];
			} 
		} 
		return $retrunString;
	} 
} 
// ====[ test segment.  ]=======
// var_dump(Filter::http_get('abc'));
// $str = "\n' %20 &nbsp;\n";
// var_dump(Filter::filter_invalid($str));
?>
