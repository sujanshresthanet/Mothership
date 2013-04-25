<?php namespace Stwt\Mothership;

class Arr
{

    /**
     * Alias of element
     *
     * @param array $array
     * @param mixed $index
     * @param mixed $array
     *
     * @return mixed
     */
    public static function e($array, $index, $default = null)
    {
        return static::element($array, $index, $default);
    }

    /**
     * Attempts to return the value of an array element at a
     * given index. If index dosn't exist a default value is 
     * returned.
     *
     * @param array $array
     * @param mixed $index
     * @param mixed $array
     *
     * @return mixed
     */
    public static function element($array, $index, $default = null)
    {
        return (isset($array[$index]) ? $array[$index] : $default);
    }
}
