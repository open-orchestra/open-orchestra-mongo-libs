<?php

namespace OpenOrchestra\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\Exceptions\MalFormedConditionException;

/**
 * Class ConditionFromBooleanToMongoTransformer
 */
class ConditionFromBooleanToMongoTransformer implements DataTransformerInterface
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
        return $this->transformField(json_decode($value, true));
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
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
     * @throws MalFormedConditionException
     */
    protected function transformConditionToMongoCondition($condition, array &$aliases)
    {
        $isAndBooleanRegExp = '/^((NOT (?=.)){0,1}[^ \(\)]+( AND (?=.)){0,1})+$/';
        $isOrBooleanRegExp = '/^((NOT (?=.)){0,1}[^ \(\)]+( OR (?=.)){0,1})+$/';
        $getAndSubBooleanRegExp = '/(NOT (?=.)){0,1}([^ \(\)]+)( AND (?=.)){0,1}/';
        $getOrSubBooleanRegExp = '/(NOT (?=.)){0,1}([^ \(\)]+)( OR (?=.)){0,1}/';
        $subElements = array();

        if (preg_match($isAndBooleanRegExp, $condition)) {
            preg_match_all($getAndSubBooleanRegExp, $condition, $subElements);
        } elseif  (preg_match($isOrBooleanRegExp, $condition)) {
            preg_match_all($getOrSubBooleanRegExp, $condition, $subElements);
        }
        if (count($subElements) > 0) {
            $operator = ($subElements[3][0] == ' OR ') ? '$or' : '$and';
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

        throw new MalFormedConditionException();
    }
}
