<?php
/**
 * Auto Load Classes
 * @param object $class
 * @return boolean
 */
function __autoload($class) {
	$path = str_replace ( "_", "/", $class );
	$path = ROOTPATH . 'class/class.' . $path . ".php";
	if (file_exists ( $path )) {
		require_once $path;
		return true;
	}
	return false;
}

function spiltSpell( $spell,$length )
{
	$sm = array("b","p","m","f","d","t","n","l","g","k","h","j","q","x","zh","ch","sh","r","z","c","s","y","w");
	$ym = array("i","u","ü","a","ia","ua","o","uo","e","ie","üe","ai","uai","ei","er","uei","ao","iao","ou","iou",
		"an","ian","uan","üan","en","in","uen","ün","ang","iang","uang","eng","ing","ueng","ong","iong");
	//var_dump(count($sm),count($ym));exit;
	if( in_array($spell, $ym) ) return array("spell_x"=>"","spell_y"=>$spell);
	$res = array();
	$res["spell_x"] = iconv_substr($spell, 0, $length);
	$res["spell_y"] = iconv_substr($spell, $length);
	if( in_array($res["spell_x"], $sm) )
	{
		return $res;
	}
	else 
	{
		return spiltSpell($spell, 1);
	}
}


function output( $darr ) {
    global $G_actor;
    if( empty( $darr ) ) {
        exit;
    }
    
    if( isset( $_GET['format'] ) && $_GET['format'] == 'json' ) {
        echo json_encode( $darr );
    } else {
        list( $root, $v ) = each( $darr );
        $xml = new simpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><'.$root.' />' );
        !empty( $v ) && is_array( $v ) && array2xml( $xml, $v );
        echo $xml->asXML();
    }
    exit;
}

function array2xml( &$xml, $darr ) {
    $isvector = array_keys( $darr ) === range( 0, count($darr) -1 );
    if( $isvector ) {
        foreach( $darr as $k=>$r ) {
            if( is_array( $r ) ) {
                $xml[$k] = null;
                @array2xml( $xml[$k], $r );
            } else {
                $xml[$k] = $r;
            }
        }
    } else {
        foreach( $darr as $k=>$r ) {
            if( is_array( $r ) ) {
                $xml->$k = null;
                @array2xml( $xml->$k, $r );
            } else {
                $xml->$k = $r;
            }
        }
    }
}
 
?> 