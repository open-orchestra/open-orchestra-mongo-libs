<?php

namespace OpenOrchestra\Transformer;

use OpenOrchestra\Transformer\ConditionFromBooleanToBddTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
@trigger_error('The '.__NAMESPACE__.'\ConditionFromBooleanToMongoTransformer class is deprecated since version 1.2.0 and will be removed in 1.2.1', E_USER_DEPRECATED);

/**
 * Class ConditionFromBooleanToMongoTransformer
 */
class ConditionFromBooleanToMongoTransformer implements ConditionFromBooleanToBddTransformerInterface
{
    protected $field = '';

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function transform($value)
    {
        return $this->transformField(json_decode($value, true));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function reverseTransform($value)
    {
        $value = "( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )";
        return $this->reverseTransformField($value);
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
                    $conditions = 'NOT '.$conditions[$key];
                    break;
                } elseif ($key === '$and') {
                    $conditions = '( '.implode(' AND ', $conditions[$key]).' )';
                    break;
                } elseif ($key === '$or') {
                    $conditions = '( '.implode(' OR ', $conditions[$key]).' )';
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
            $getBalancedBracketsRegExp = '/\( ([^\(\)]*) \)/';
            $encapsuledElements = array();
            preg_match_all($getBalancedBracketsRegExp, $condition, $encapsuledElements);
            foreach ($encapsuledElements[0] as $key => $encapsuledElement) {
                $alias = $delimiter.$count.$delimiter;
                $condition = preg_replace('/'.preg_quote($encapsuledElement).'/', $alias, $condition, 1);
                $aliases[$alias] = $this->transformConditionToMongoCondition($encapsuledElements[1][$key], $aliases);
                $count++;
            }
            if (count($encapsuledElements[0]) > 0) {
                $result = $this->reverseTransformField($condition, $count, $aliases, $delimiter);
            } else {
                $result = json_encode($this->transformConditionToMongoCondition($condition, $aliases));
            }
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @param string $condition
     * @param array  $aliases
     *
     * @return array
     *
     * @throws TransformationFailedException
     */
    protected function transformConditionToMongoCondition($condition, array &$aliases)
    {
        $getSubBooleanRegExp = '/(NOT (?=.)){0,1}([^ \(\)]+)( (AND|OR) (?=.)){0,1}/';
        $subElements = array();

        if (preg_match_all($getSubBooleanRegExp, $condition, $subElements) !== false) {
            $operator = ($subElements[4][0] == 'OR') ? '$or' : '$and';
            $result = array();
            foreach($subElements[2] as $key => $subElement) {
                if (array_key_exists($subElement, $aliases)) {
                    if ($subElements[1][$key] != '') {
                        array_push($result, array('$not' => $aliases[$subElement]));
                    } else {
                        array_push($result, $aliases[$subElement]);
                    }
                    unset($aliases[$subElement]);
                } else {
                    $comparison = ($subElements[1][$key] == '') ? '$eq' : '$ne';
                    array_push($result, array($this->field => array($comparison => $subElement)));
                }
            }

            return (array($operator => $result));
        }

        throw new TransformationFailedException();
    }
}
