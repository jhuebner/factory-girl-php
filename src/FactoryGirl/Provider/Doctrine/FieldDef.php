<?php

namespace FactoryGirl\Provider\Doctrine;

/**
 * Contains static methods to define fields as sequences, references etc.
 */
class FieldDef
{
    /**
     * Defines a field to be a string based on an incrementing integer.
     *
     * This is typically used to generate unique names such as usernames.
     *
     * The parameter may be a function that receives a counter value
     * each time the entity is created or it may be a string.
     *
     * If the parameter is a string string containing "%d" then it will be
     * replaced by the counter value. If the string does not contain "%d"
     * then the number is simply appended to the parameter.
     *
     * @param callable|string $funcOrString The function or pattern to generate a value from.
     * @param int $firstNum The first number to use.
     * @return callable
     */
    public static function sequence($funcOrString, $firstNum = 1)
    {
        $n = $firstNum - 1;
        if (is_callable($funcOrString)) {
            return function() use (&$n, $funcOrString) {
                $n++;
                return call_user_func($funcOrString, $n);
            };
        } elseif (strpos($funcOrString, '%d') !== false) {
            return function() use (&$n, $funcOrString) {
                $n++;
                return str_replace('%d', $n, $funcOrString);
            };
        } else {
            return function() use (&$n, $funcOrString) {
                $n++;
                return $funcOrString . $n;
            };
        }
    }

    public static function past()
    {
        return new DateIntervalHelper(new \DateTime(), true);
    }

    public static function future()
    {
        return new DateIntervalHelper(new \DateTime(), false);
    }
    
    /**
     * Defines a field to `get()` a named entity from the factory.
     *
     * The normal semantics of `get()` apply.
     * Normally this means that the field gets a fresh instance of the named
     * entity. If a singleton has been defined, `get()` will return that.
     *
     * @param string $name The name of the entity to get.
     * @return callable
     */
    public static function reference($name)
    {
        return function(FixtureFactory $factory) use ($name) {
            return $factory->get($name);
        };
    }

    /**
     * Returns a custom value from the faker library
     *
     * @param $funcOrString
     * @param $params
     * @return callable
     */
    public static function faker($funcOrString, ...$params)
    {
        return function(FixtureFactory $factory) use ($funcOrString, $params){
            return call_user_func_array(array($factory->getFaker(), $funcOrString), $params);
        };
    }

    /**
     * Returns a custom value from the faker library or NULL
     *
     * @param $funcOrString
     * @param ...$params
     * @return callable
     */
    public static function fakerOrNull($funcOrString, ...$params)
    {
        $nullProbability = 0.3;

        if(rand(0, 100) <= $nullProbability*100) return null;

        return function(FixtureFactory $factory) use ($funcOrString, $params){
            return call_user_func_array(array($factory->getFaker(), $funcOrString), $params);
        };
    }
}