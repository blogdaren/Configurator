<?php
/**
 * @script   Configurator.php
 * @brief    This file is part of Configurator
 * @author   blogdaren<blogdaren@163.com>
 * @link     http://www.phpcreeper.com
 * @version  1.0.0
 * @modify   2019-04-01
 */
namespace Configurator;

class Configurator
{
    /**
     * 应用程序设置
     *
     * @var array
     */
    private static $_config = array();

    /**
     * 对象注册表
     *
     * @var array
     */
    private static $_objects = array();

    /**
     * 获取指定的设置内容
     *
     * $option 参数指定要获取的设置名。
     * 如果设置中找不到指定的选项，则返回由 $default 参数指定的值。
     *
     * @begincode
     * $option_value = Configurator::get('my_option');
     * @endcode
     *
     * 对于层次化的设置信息，可以通过在 $option 中使用 "/" 符号来指定。
     *
     * 例如有一个名为 option_group 的设置项，其中包含三个子项目。
     * 现在要查询其中的 my_option 设置项的内容。
     *
     * @begincode
     * //+--- option_group
     * //  +-- my_option1 = this is my_option1
     * //  +-- my_option2 = this is my_option2
     * //  \-- my_option3 = this is my_option3
     *
     * //查询 option_group 设置组里面的 my_option 项
     * //将会显示 this is my_option
     * echo Configurator::get('option_group/my_option');
     * @endcode
     *
     * 要读取更深层次的设置项，可以使用更多的 "/" 符号，但太多层次会导致读取速度变慢。
     *
     * 如果要获得所有设置项的内容，将 $option 参数指定为 '/' 即可：
     *
     * @begincode
     * //获取所有设置项的内容
     * $all = Configurator::get('/');
     * @endcode
     *
     * @param string $option 要获取设置项的名称
     * @param mixed $default 当设置不存在时要返回的设置默认值
     *
     * @return mixed 返回设置项的值
     */
    static function get($option = "/", $default = null)
    {
		//默认获取所有项的配置信息
        if($option == '/') return self::$_config;

		//单层次
        if(strpos($option, '/') === false)
        {
            return array_key_exists($option, self::$_config)
                ? self::$_config[$option]
                : $default;
        }

        //多层次
		$parts = explode('/', $option);
        $pos =& self::$_config;
        foreach ($parts as $part)
        {
            if(!isset($pos[$part])) return $default;
            $pos =& $pos[$part];
        }

        return $pos;
    }

    /**
     * 修改指定设置的内容
     *
     * >> 当 $option 参数是字符串时:
     * $option  指定了要修改的设置项。
     * $data    则是要为该设置项指定的新数据。
     *
     * @begincode
     * //修改一个设置项
     * Configurator::set('option_group/my_option2', 'new value');
     * @endcode
     *
     * >> 如果 $option 是一个数组，则假定要修改多个设置项。
     * 那么 $option 则是一个由设置项名称和设置值组成的名值对，或者是一个嵌套数组。
     *
     * @begincode
     * //假设已有的设置为
     * //+--- option_1 = old value
     * //+--- option_group
     * //  +-- option1 = old value
     * //  +-- option2 = old value
     * //  \-- option3 = old value
     *
     * //修改多个设置项
     * $arr = array(
     *      'option_1' => 'value_1',
     *      'option_2' => 'value_2',
     *      'option_group/option2' => 'new value',
     * );
     * Configurator::set($arr);
     *
     * //修改后
     * //+--- option_1 = value_1
     * //+--- option_2 = value_2
     * //+--- option_group
     * //  +-- option1 = old value
     * //  +-- option2 = new value
     * //  \-- option3 = old value
     * @endcode
     *
     * 上述代码展示了 Configurator::set() 的一个重要特性：保持已有设置的层次结构。
     *
     * 因此如果要完全替换某个设置项和其子项目，应该使用 Configurator::reset() 方法。
     *
     * @param string|array  $option 要修改的设置项名称，或包含多个设置项目的数组
     * @param mixed         $data   指定设置项的新值
     *
     * return null
     */
    static function set($option, $data = null)
    {
        if(is_array($option))
        {
            foreach ($option as $key => $value)
            {
                self::set($key, $value);
            }
            return;
        }

        if(!is_array($data))
        {
            if(strpos($option, '/') === false)
            {
                self::$_config[$option] = $data;
                return;
            }

            $parts = explode('/', $option);
            $max = count($parts) - 1;
            $pos =& self::$_config;
            for ($i = 0; $i <= $max; $i ++)
            {
                $part = $parts[$i];
                if($i < $max)
                {
                    if(!isset($pos[$part]))
                    {
                        $pos[$part] = array();
                    }
                    $pos =& $pos[$part];
                }
                else
                {
                    $pos[$part] = $data;
                }
            }
        }
        else
        {
            foreach ($data as $key => $value)
            {
                self::set($option . '/' . $key, $value);
            }
        }
    }

    /**
     * 替换已有的设置值
     *
     * Configurator::reset() 表面上看和 Configurator::set() 类似。
     * 但是 Configurator::reset() 不会保持已有设置的层次结构，
     * 而是直接替换到指定的设置项及其子项目。
     *
     * @begincode
     * //假设已有的设置为
     * //+--- option_1 = old value
     * //+--- option_group
     * //  +-- option1 = old value
     * //  +-- option2 = old value
     * //  \-- option3 = old value
     *
     * //替换多个设置项
     * $arr = array(
     *      'option_1' => 'value_1',
     *      'option_2' => 'value_2',
     *      'option_group/option2' => 'new value',
     * );
     * Configurator::reset($arr);
     *
     * //修改后
     * //+--- option_1 = value_1
     * //+--- option_2 = value_2
     * //+--- option_group
     * //  +-- option2 = new value
     * @endcode
     *
     * 从上述代码的执行结果可以看出 Configurator::reset() 和 Configurator::set() 的重要区别。
     *
     * 不过由于 Configurator::reset() 速度比 Configurator::set() 快很多，
     * 因此应该尽量使用 Configurator::reset() 来代替 Configurator::set()。
     *
     * @param string|array $option 要修改的设置项名称，或包含多个设置项目的数组
     * @param mixed $data 指定设置项的新值
     *
     * return null
     */
    static function reset($option, $data = null)
    {
        if(is_array($option))
        {
            self::$_config = array_merge(self::$_config, $option);
        }
        else
        {
            self::$_config[$option] = $data;
        }
    }

    /**
     * 删除指定的设置
     *
     * Configurator::remove() 可以删除指定的设置项目及其子项目。
     *
     * @param mixed $option 要删除的设置项名称
     *
     * return null
     */
    static function remove($option)
    {
        if(strpos($option, '/') === false)
        {
            unset(self::$_config[$option]);
        }
        else
        {
            $parts = explode('/', $option);
            $max = count($parts) - 1;
            $pos =& self::$_config;
            for ($i = 0; $i <= $max; $i ++)
            {
                $part = $parts[$i];
                if($i < $max)
                {
                    if(!isset($pos[$part]))
                    {
                        $pos[$part] = array();
                    }
                    $pos =& $pos[$part];
                }
                else
                {
                    unset($pos[$part]);
                }
            }
        }
    }

    /**
     * 返回指定对象的唯一实例
     *
     * Configurator::singleton() 完成下列工作：
     *
     * <ul>
     *   <li>在对象注册表中查找指定类名称的对象实例是否存在；</li>
     *   <li>如果存在，则返回该对象实例；</li>
     *   <li>如果不存在，则载入类定义文件，并构造一个对象实例；</li>
     *   <li>将新构造的对象以类名称作为对象名登记到对象注册表；</li>
     *   <li>返回新构造的对象实例。</li>
     * </ul>
     *
     * 使用 Configurator::singleton() 的好处在于多次使用同一个对象时不需要反复构造对象。
     *
     * @begincode
     * //在位置 A 处使用对象 My_Object
     * $obj = Configurator::singleton('My_Object');
     * ...
     * ...
     * //在位置 B 处使用对象 My_Object
     * $obj2 = Configurator::singleton('My_Object');
     * //$obj 和 $obj2 都是指向同一个对象实例，避免了多次构造，提高了性能
     * @endcode
     *
     * @param string $class_name 要获取的对象的类名字
     *
     * @return object 返回对象实例
     */
    static function singleton($class_name, $args = array())
    {
        $key = strtolower($class_name);

        if(!isset(self::$_objects[$key]))
        {
            self::$_objects[$key] = new $class_name($args);
        }

        return self::$_objects[$key];
    }

    /**
     * 读取指定的缓存内容，如果内容不存在或已经失效，则返回 false
     *
     * 在操作缓存数据时，必须指定缓存的 ID。每一个缓存内容都有一个唯一的 ID。
     * 例如数据 A 的缓存 ID 是 data-a，而数据 B 的缓存 ID 是 data-b。
     *
     * 在大量使用缓存时，应该采用一定的规则来确定缓存 ID。下面是一个推荐的方案：
     *
     * <ul>
     *   <li>首先按照缓存数据的性质确定前缀，例如 page、db 等；</li>
     *   <li>然后按照数据的唯一索引来确定后缀，并和前缀一起组合成完整的缓存 ID。</li>
     * </ul>
     *
     * 按照这个规则，缓存 ID 看上去类似 page.news.1、db.members.userid。
     *
     * Configurator::cache() 可以指定 $policy 参数来覆盖缓存数据本身带有的策略。
     * 具体哪些策略可以使用，请参考不同缓存服务的文档。
     *
     * $backend_class 用于指定要使用的缓存服务对象类名称。例如 Cache_File、Cache_APC 等。
     *
     * @begincode
     * $data = Configurator::cache($cache_id);
     * if($data === false)
     * {
     *     $data = ....
     *     Configurator::writeCache($cache_id, $data);
     * }
     * @endcode
     *
     * @param string $id            缓存ID
     * @param array  $policy        缓存策略
     * @param string $backend_class 缓存组件
     *
     * @return mixed 成功返回缓存内容，失败返回 false
     */
    static function cache($id, array $policy = null, $backend_class = null)
    {
        static $obj = null;

        if(is_null($backend_class))
        {
            $backend = self::get('cache_backend');
            if(empty($backend) || !class_exists($backend, true)) return false;
            is_null($obj) && $obj = self::singleton($backend);
        }
        else
        {
            $obj = self::singleton($backend_class);
        }

        return $obj->get($id, $policy);
    }

    /**
     * 将变量内容写入缓存，失败抛出异常
     *
     * $data 参数指定要缓存的内容。如果 $data 参数不是一个字符串，则必须将缓存策略 serialize 设置为 true。
     * $policy 参数指定了内容的缓存策略，如果没有提供该参数，则使用缓存服务的默认策略。
     *
     * 其他参数同 Configurator::cache()。
     *
     * @param string    $id            缓存的 ID
     * @param mixed     $data          要缓存的数据
     * @param array     $policy        缓存策略
     * @param string    $backend_class 要使用的缓存服务
     * @param array	    $extra	       额外信息
     *
     * return null | boolean
     */
    static function writeCache($id, $data, array $policy = null, $backend_class = null, array $extra = null)
    {
        static $obj = null;

        if(is_null($backend_class))
        {
            $backend = self::get('cache_backend');
            if(empty($backend) || !class_exists($backend, true)) return false;
            is_null($obj) && $obj = self::singleton($backend);
            $obj->set($id, $data, $policy);
        }
        else
        {
            $obj = self::singleton($backend_class);
        }

        $obj->set($id, $data, $policy, $extra);
    }

    /**
     * 删除指定的缓存内容
     *
     * 通常: 失效的缓存数据无需清理。但有时候，希望在某些操作完成后立即清除缓存。
     * 例如更新数据库记录后，希望删除该记录的缓存文件，以便在下一次读取缓存时重新生成缓存文件。
     *
     * Configurator::cleanCache($cache_id);
     *
     * @param string $id 缓存的 ID
     * @param array $policy 缓存策略
     * @param string $backend_class 要使用的缓存服务
     *
     * return null | boolean
     */
    static function cleanCache($id, array $policy = null, $backend_class = null)
    {
        static $obj = null;

        if(is_null($backend_class))
        {
            $backend = self::get('cache_backend');
            if(empty($backend) || !class_exists($backend, true)) return false;
            is_null($obj) && $obj = self::singleton($backend);
        }
        else
        {
            $obj = self::singleton($backend_class);
        }

        $obj->remove($id, $policy);
    }
}


