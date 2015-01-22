<?php

namespace Foolz\Plugin;

/**
 * Allows adding plugin hooks for before and after with ease, change input parameters, override methods
 *
 * @author   Foolz <support@foolz.us>
 * @package  Foolz\Plugin
 * @license  http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
trait PlugSuit
{
    /**
     * The methods with 'p_' prefix will respond to plugins before and after
     *
     * @param  string  $name        The name of the method without the prefix
     * @param  array   $parameters  The parameters passed by the user
     */
    public function __call($name, $parameters)
    {
        $class = get_class($this);

        if (!method_exists($this, 'p_'.$name)) {
            throw new \BadMethodCallException('Method "'.$name.'" does not exist in "'.$class.'".');
        }

        $before = Hook::forge($class.'::'.$name.'#call.beforeMethod')
            ->setObject($this)
            ->setParams($parameters)
            ->execute();

        $parameters = $before->getParams();

        // if it's not void it means we've replaced the return
        if (!$before->get() instanceof Void) {
            $return = $before->get();
        } else {
            switch (count($parameters)) {
                case 0:
                    $return = $this->{'p_'.$name}();
                    break;
                case 1:
                    $return = $this->{'p_'.$name}($parameters[0]);
                    break;
                case 2:
                    $return = $this->{'p_'.$name}($parameters[0], $parameters[1]);
                    break;
                case 3:
                    $return = $this->{'p_'.$name}($parameters[0], $parameters[1], $parameters[2]);
                    break;
                case 4:
                    $return = $this->{'p_'.$name}($parameters[0], $parameters[1], $parameters[2], $parameters[3]);
                    break;
                case 5:
                    $return = $this->{'p_'.$name}($parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4]);
                    break;
                default:
                    $return = call_user_func_array(array(&$this, 'p_'.$name), $parameters);
                    break;
            }
        }

        // in the after, the last parameter passed will be the result
        $after = \Foolz\Plugin\Hook::forge($class.'::'.$name.'#call.afterMethod')
            ->setParams($parameters)
            ->execute();

        if (!$after->get() instanceof \Foolz\Plugin\Void) {
            return $after->get();
        }

        return $return;
    }

    /**
     * The static methods with 'p_' prefix will respond to plugins before and after
     *
     * @param  string  $name        The name of the method without the prefix
     * @param  array   $parameters  The parameters passed by the user
     */
    public static function __callStatic($name, $parameters)
    {
        $class = get_called_class();

        if (!method_exists($class, 'p_'.$name)) {
            throw new \BadMethodCallException('Static method "'.$name.'" does not exist in "'.$class.'".');
        }

        $before = \Foolz\Plugin\Hook::forge($class.'.'.$name.'#call.before')
            ->setParams($parameters)
            ->execute();

        $parameters = $before->getParams();

        // if it's not void it means we've replaced the return
        if (!$before->get() instanceof \Foolz\Plugin\Void) {
            $return = $before->get();
        } else {
            $pname = 'p_'.$name;
            switch (count($parameters)) {
                case 0:
                    $return = static::$pname();
                    break;
                case 1:
                    $return = static::$pname($parameters[0]);
                    break;
                case 2:
                    $return = static::$pname($parameters[0], $parameters[1]);
                    break;
                case 3:
                    $return = static::$pname($parameters[0], $parameters[1], $parameters[2]);
                    break;
                case 4:
                    $return = static::$pname($parameters[0], $parameters[1], $parameters[2], $parameters[3]);
                    break;
                case 5:
                    $return = static::$pname($parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4]);
                    break;
                default:
                    $return = call_user_func_array(get_called_class().'::'.$pname, $parameters);
                    break;
            }
        }

        // in the after, the last parameter passed will be the result
        $after = \Foolz\Plugin\Hook::forge($class.'.'.$name.'#call.after')
            ->setParams($parameters)
            ->execute();

        if (!$after->get() instanceof Void) {
            return $after->get();
        }

        return $return;
    }
}
