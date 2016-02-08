<?php

namespace OpenOrchestra\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class SqlToBddTransformer
 */
class SqlToBddTransformer implements DataTransformerInterface
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
            $value[$this->field] = $this->reverseTransformField($value[$this->field]);
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
                    $conditions = 'NOT '.$conditions[$key];
                    break;
                } elseif ($key === '$and') {
                    $conditions = '('.implode(' AND ', $conditions[$key]).')';
                    break;
                } elseif ($key === '$or') {
                    $conditions = '('.implode(' OR ', $conditions[$key]).')';
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
            $reserved = preg_quote(')(');
            $findEncapsuledCondition = '/\(([^'.$reserved.']+)\)/';
            $encapsuledElements = array();
            preg_match_all($findEncapsuledCondition, $condition, $encapsuledElements);
            foreach ($encapsuledElements[0] as $key => $encapsuledElement) {
                $alias = $delimiter.$count.$delimiter;
                $condition = preg_replace('/'.preg_quote($encapsuledElement).'/', $alias, $condition, 1);
                $aliases[$alias] = $this->transformConditionArrayToMongoCondition($encapsuledElements[1][$key], $aliases);
                $count++;
            }

            if (count($encapsuledElements[0]) > 0) {
                $result = $this->reverseTransformField($condition, $count, $aliases, $delimiter);
            } else {
                $result = json_encode($this->transformConditionArrayToMongoCondition($condition, $aliases));
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
     */
    protected function transformConditionArrayToMongoCondition($condition, array &$aliases)
    {
        $operator = '$or';
        $conditionElements = explode(' OR ', $condition);

        if (count($conditionElements) == 1) {
            $operator = '$and';
            $conditionElements = explode(' AND ', $condition);
        }

        foreach ($conditionElements as $key => $element) {
            $comparison = '$eq';
            if(strpos($element, 'NOT ') !== false) {
                $comparison = '$ne';
                $conditionElements[$key] = substr($element, 4);
            }
            if (array_key_exists($element, $aliases)) {
                $conditionElements[$key] = $aliases[$element];
                unset($aliases[$element]);
            }
            if (!is_array($conditionElements[$key])) {
                $conditionElements[$key] = array($this->field => array($comparison => $conditionElements[$key]));
            }
        }

        return array($operator => $conditionElements);
    }
}
