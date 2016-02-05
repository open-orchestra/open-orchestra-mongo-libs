<?php

namespace OpenOrchestra\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class LuceneToBddTransformer
 */
class LuceneToBddTransformer implements DataTransformerInterface
{
    protected $field = null;

    /**"
     * @param string $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        if (is_array($value) && array_key_exists($this->field, $value)) {
            $value[$this->field] = $this->transformField(json_decode($value[$this->field], true));
        }

        return $value;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        if (is_array($value) && array_key_exists($this->field, $value)) {
            $value[$this->field] = $this->reverseTransformField(preg_replace('/ *(\+|-|\(|\)) */', '$1', $value[$this->field]));
        }

        return $value;
    }

    /**
     * @param string $conditions
     *
     * @return array|string
     */
    protected function transformField($conditions)
    {
        if (!is_null($conditions)) {
            foreach($conditions as $key => $condition) {
                if(is_array($condition)){
                    $conditions[$key] = $this->transformField($condition);
                }
                if ($key === '$eq' || $key === $this->field) {
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
        }

        return $conditions;
    }

    /**
     * @param string $condition
     * @param int    $count
     * @param array  $aliases
     * @param string $delimiter
     *
     * @return string|null
     */
    protected function reverseTransformField($condition, $count = 0, $aliases = array(), $delimiter = '##')
    {
        if (!is_null($condition)) {
            $result = array();
            $operator = preg_quote('+- ');
            $reserved = preg_quote('+- )(');
            $findEncapsuledCondition = '/\(((['.$operator.']{0,1}[^'.$reserved.']+)+)\)/';
            $explodeCondition = '/(['.$operator.']{0,1})([^'.$reserved.']+)/';

            $encapsuledElements = array();
            preg_match_all($findEncapsuledCondition, $condition, $encapsuledElements);

            foreach ($encapsuledElements[0] as $key => $encapsuledElement) {
                $alias = $delimiter.$count.$delimiter;
                $condition = preg_replace('/'.preg_quote($encapsuledElement).'/', $alias, $condition, 1);
                $aliases[$alias] = $this->transformConditionArrayToMongoCondition($explodeCondition, $encapsuledElements[1][$key], $aliases);
                $count++;
            }

            if (count($encapsuledElements[0]) > 0) {
                $result = $this->reverseTransformField($condition, $count, $aliases, $delimiter);
            } else {
                $result = json_encode($this->transformConditionArrayToMongoCondition($explodeCondition, $condition, $aliases));
            }
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @param string $regExp
     * @param string $condition
     * @param array  $aliases
     *
     * @return array
     */
    protected function transformConditionArrayToMongoCondition($regExp, $condition, array &$aliases)
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
            $comparison = ($operator == '-') ? '$ne' : '$eq';
            if ($operator == '-') {
                $conditionElements[1][$key] = '+';
            } elseif ($operator == '') {
                $conditionElements[1][$key] = ' ';
            }
            if (!is_array($conditionElements[2][$key])) {
                $conditionElements[2][$key] = array($this->field => array($comparison => $conditionElements[2][$key]));
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
