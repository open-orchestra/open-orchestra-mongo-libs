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
	public function transformLuceneConditionToBddCondition($field, $condition, $count = 0, $aliases = array())
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
			$result = $this->transformLuceneConditionToBddCondition($field, $condition, $count, $aliases);
		} else {
		    $result = json_encode($this->transformConditionArrayToMongoCondition($field, $explodeCondition, $condition, $aliases));
		}

		return $result;
	}

    /**
     * @param string $condition
     *
     * @return array|string
     */
	public function transformBddConditionToLuceneCondition($field, $conditions)
	{
		foreach($conditions as $key => $condition) {
			if(is_array($condition)){
				$conditions[$key] = $this->transformBddConditionToLuceneCondition($field, $condition);
			}
			if ($key === '$eq' || $key === $field) {
				$conditions = $conditions[$key];
				break;
			} elseif ($key === '$ne') {
				$conditions = '-'.$conditions[$key];
				break;
			} elseif ($key === '$and') {
				$conditions = '(+'.implode('+', $conditions[$key]).')';
				$conditions = preg_replace('/\+-/', '-', $conditions);
				break;
			} elseif ($key === '$or') {
				$conditions = '('.implode(' ', $conditions[$key]).')';
				break;
			}
		}

		return $conditions;
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

	    $result = array();
	    foreach ($conditionElements[1] as $key => $operator) {
	        $comparison = '$eq';
	        if ($operator == '-') {
	            $comparison = '$ne';
	            $conditionElements[1][$key] = '+';
	        } elseif ($operator == '') {
	            $conditionElements[1][$key] = ' ';
	        }
	        if (!is_array($conditionElements[2][$key])) {
	            $conditionElements[2][$key] = array($field => array($comparison => $conditionElements[2][$key]));
	        }
	    }
		$operatorType = array_unique($conditionElements[1]);
	    $result = $conditionElements[2][0];
		if (count($conditionElements[2]) > 1) {
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
$bddCondition = $luceneSyntaxToBddSyntax->transformLuceneConditionToBddCondition('keyword', '+(cat:X1 (+cat:X2+cat:X3))+(author:AAA)+(T1 T2-T3)');
echo $bddCondition;
$luceneCondition = $luceneSyntaxToBddSyntax->transformBddConditionToLuceneCondition('keyword', json_decode($bddCondition, true));
echo $luceneCondition;
?>
