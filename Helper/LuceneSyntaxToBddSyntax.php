<?php

namespace OpenOrchestra\MongoBundle;

use OpenOrchestra\Helper\LuceneSyntaxToBddSyntaxInterface;

/**
 * Class LuceneSyntaxToBddSyntax
 */
class LuceneSyntaxToBddSyntax implements LuceneSyntaxToBddSyntaxInterface
{
	CONST OPERATOR = '+- ';
	CONST RESERVED = '+- )(';
	CONST DELIMITER = '##';

    /**
     * @param string $condition
     *
     * @return string
     */
	public function transformConditionStringToBddCondition($field, $condition, $count = 0, $aliases = array())
	{
		$result = array();
		$operator = preg_quote(LuceneSyntaxToBddSyntax::OPERATOR);
		$reserved = preg_quote(LuceneSyntaxToBddSyntax::RESERVED);
		$findEncapsuledCondition = '/\(((['.$operator.']{0,1}[^'.$reserved.']+)+)\)/';
		$explodeCondition = '/(['.$operator.']{0,1})([^'.$reserved.']+)/';

		$encapsuledElements = array();
		preg_match_all($findEncapsuledCondition, $condition, $encapsuledElements);

		foreach ($encapsuledElements[0] as $key => $encapsuledElement) {
			$alias = LuceneSyntaxToBddSyntax::DELIMITER.$count.LuceneSyntaxToBddSyntax::DELIMITER;
			$condition = preg_replace('/'.preg_quote($encapsuledElement).'/', $alias, $condition, 1);
			$aliases[$alias] = $this->transformConditionArrayToMongoCondition($field, $explodeCondition, $encapsuledElements[1][$key], $aliases);
			$count++;
		}

		if (count($encapsuledElements[0]) > 0) {
			$result = $this->transformConditionStringToBddCondition($field, $condition, $count, $aliases);
		} else {
		    $result = json_encode($this->transformConditionArrayToMongoCondition($field, $explodeCondition, $condition, $aliases));
		}

		return $result;
	}

    /**
     * @param string $regExp
     * @param array  $condition
     * @param array  $aliases
     *
     * @return array
     */
	protected function transformConditionArrayToMongoCondition($field, $regExp, $condition, &$aliases)
	{

	    $conditionElements = array();
	    preg_match_all($regExp, $condition, $conditionElements);

		foreach ($conditionElements[2] as $key => $alias) {
			if (array_key_exists($alias, $aliases)) {
				$conditionElements[2][$key] = $aliases[$alias];
				unset($aliases[$alias]);
			}
		}

	    if (count($conditionElements[2]) == 1) {
	        $result = array($field => array('$eq' => $conditionElements[2][0]));
	    } else {
    	    $result = array();
    		foreach ($conditionElements[1] as $key => $operator) {
    			if ($operator == '-') {
    				$conditionElements[2][$key] = array($field => array('$ne' => $conditionElements[2][$key]));
    				$conditionElements[1][$key] = '+';
    			} else {
    			    $conditionElements[2][$key] = array($field => array('$eq' => $conditionElements[2][$key]));
    			}
    			if ($operator == '') {
    				$conditionElements[1][$key] = ' ';
    			}
    		}
    		$operatorType = array_unique($conditionElements[1]);
    		if (count($operatorType) == 1) {
    			if ($operatorType[0] == '+') {
    				$result = array('$and' => $conditionElements[2]);
    			} elseif ($operatorType[0] == ' ') {
    				$result = array('$or' => $conditionElements[2]);
    			}
    		} else {
    			$result = array('$and' => array(array('$or' => array())));
    			foreach ($conditionElements[1] as $key => $operator) {
    				if ($operator == '+') {
    					array_push($result['$and'], $conditionElements[2][$key]);
    				} elseif ($operator == ' ') {
    					array_push($result['$and'][0]['$or'], $conditionElements[2][$key]);
    				}
    			}
    		}
	    }

		return $result;
	}
}

$luceneSyntaxToBddSyntax = new LuceneSyntaxToBddSyntax();
print_r($luceneSyntaxToBddSyntax->transformConditionStringToBddCondition('keyword', '+(cat:X1 cat:X2)+(author:AAA)+(T1 T2-T3)'));
?>
