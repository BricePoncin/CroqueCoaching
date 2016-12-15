<?PHP

function convertXmlObjToArr($obj, &$arr)
{
    $children = $obj->children();
    foreach ($children as $elementName => $node)
    {
        $nextIdx = count($arr);
        $arr[$nextIdx] = array();
        $arr[$nextIdx]['@name'] = strtolower((string)$elementName);
        $arr[$nextIdx]['@attributes'] = array();
        $attributes = $node->attributes();
        foreach ($attributes as $attributeName => $attributeValue)
        {
            $attribName = strtolower(trim((string)$attributeName));
            $attribVal = trim((string)$attributeValue);
            $arr[$nextIdx]['@attributes'][$attribName] = $attribVal;
        }
        $text = (string)$node;
        $text = trim($text);
        if (strlen($text) > 0)
        {
            $arr[$nextIdx]['@text'] = $text;
        }
        $arr[$nextIdx]['@children'] = array();
        convertXmlObjToArr($node, $arr[$nextIdx]['@children']);
    }
    return;
}

function json_format($json)
{
    $tab = "  ";
    $new_json = "";
    $indent_level = 0;
    $in_string = false;

    $json_obj = json_decode($json);

    if($json_obj === false)
        return false;

    $json = json_encode($json_obj);
    $len = strlen($json);

    for($c = 0; $c < $len; $c++)
    {
        $char = $json[$c];
        switch($char)
        {
            case '{':
            case '[':
                if(!$in_string)
                {
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                    $indent_level++;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '}':
            case ']':
                if(!$in_string)
                {
                    $indent_level--;
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ',':
                if(!$in_string)
                {
                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ':':
                if(!$in_string)
                {
                    $new_json .= ": ";
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '"':
                if($c > 0 && $json[$c-1] != '\\')
                {
                    $in_string = !$in_string;
                }
            default:
                $new_json .= $char;
                break;                   
        }
    }

    return $new_json;
} 

// Function to parse mbldata
function parseIntegerData($fromI) {
	global $data;
	$i = $fromI;
	$integer = '';
	while (($i < strlen($data)) && (is_numeric($data[$i]))) {
		$integer .= $data[$i];
		$i++;
	}
	return array($i, $integer);
}

function parseHashData($fromI) {
	global $data;
	$i = $fromI;
	$res = array();
	while ((0 <= $i) && ($i < strlen($data)) && ($data[$i] != "g")) {
		$result = parseElementData($i);
		$i = $result[0];
		$label = $result[1];
		if ($i >= 0) {
			$result = parseElementData($i);
			$i = $result[0];
			$res[$label] = $result[1];
		}
	}
	if ($data[$i] == "g") $i++;
	return array($i, $res);
}

function parseStringData($fromI) {
	global $data, $references;
	$result = parseIntegerData($fromI);
	$string = substr($data, $result[0] + 1, $result[1]);
	array_push($references, $string);
	return array($result[0] + strlen($string) + 1, $string);
}

function parseDateData($fromI) {
	global $data;
	return array($fromI + 19, substr($data, $fromI, 19));
}

function parseObjectData($fromI) {
	$nameRes = parseElementData($fromI);
	$typeRes = parseElementData($nameRes[0]);
	$paramCountRes = parseIntegerData($typeRes[0]+1);
	$i = $paramCountRes[0];
	$params = array();
	for ($j = 0; $j < $paramCountRes[1]; $j++) {
		$result = parseElementData($i);
		$i = $result[0];
		array_push($params, $result[1]);
	}
	return array($i, array("n" => $nameRes[1], "t" => $typeRes[1], "p" => $params ));
}

function parseListData($fromI) {
	global $data;
	$i = $fromI;
	$list = array();
	while ((0 <= $i) && ($i < strlen($data)) && ($data[$i] != "h")) {
		$result = parseElementData($i);
		$i = $result[0];
		array_push($list, $result[1]);
	}
	if (($data[$i] == "h") && ($i != -1)) $i++;
	return array($i, $list);
}

function parseReferenceData($fromI) {
	global $references;
	$result = parseIntegerData($fromI);
	return array($result[0], $references[(int)$result[1]]);
}

function parseElementData($fromI) {
	global $data;
	$c = $data[$fromI];
	     if ($c == 'z') return array($fromI + 1, 0);
	else if ($c == 't') return array($fromI + 1, TRUE);
	else if ($c == 'f') return array($fromI + 1, FALSE);
	else if ($c == 'n') return array($fromI + 1, NULL);
	else if ($c == 'w') return parseObjectData($fromI + 1);
	else if ($c == 'l') return parseListData($fromI + 1);
	else if ($c == 'v') return parseDateData($fromI + 1);
	else if ($c == 'o') return parseHashData($fromI + 1);
	else if ($c == 'i') return parseIntegerData($fromI + 1);
	else if ($c == 'y') return parseStringData($fromI + 1);
	else if ($c == 'R') return parseReferenceData($fromI + 1);
	else { print "error<br/>$c <=> $fromI " . $data[$fromI]; exit(); }
}

function parseData() {
	$result = parseElementData(0);
	return $result[1];
}




/* function dataToString(d) {
	var chaine = JSON.stringify(d.match) + JSON.stringify(d.actions);
	var chaine2 = "";
	var inc = 0;
	var br = true;
	for (var i = 0; i < chaine.length; i++) {
		if ((chaine[i] == ']') || (chaine[i] == '}')) {
			inc--;
			br = true;
		}
		if ((true) || ((chaine[i] != '}') && (chaine[i] != '{') && (chaine[i] != ']') && (chaine[i] != '[') && (chaine[i] != ','))) {
			if (br) {
				chaine2 += "<br/><br/>";
				for (var j = 0; j < inc; j++) chaine2 += "&#160;&#160;&#160;&#160;&#160;";
				br = false;
			}
			chaine2 += chaine[i];
		}
		if ((chaine[i] == '[') || (chaine[i] == '{')) {
			inc++;
			br = true;
		}
		if (chaine[i] == ',') {
			br = true;
		}
		
	}
	return chaine2;
}*/

?>