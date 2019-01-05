<?php
/**
 * Array helper class.
 *
 * $Id: arr.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class CArray {

	/**
	 * Return a callback array from a string, eg: limit[10,20] would become
	 * array('limit', array('10', '20'))
	 *
	 * @param   string  callback string
	 * @return  array
	 */
	public static function callback_string($str)
	{
		// command[param,param]
		if (preg_match('/([^\[]*+)\[(.+)\]/', (string) $str, $match))
		{
			// command
			$command = $match[1];

			// param,param
			$params = preg_split('/(?<!\\\\),/', $match[2]);
			$params = str_replace('\,', ',', $params);
		}
		else
		{
			// command
			$command = $str;

			// No params
			$params = NULL;
		}

		return array($command, $params);
	}

	/**
	 * Rotates a 2D array clockwise.
	 * Example, turns a 2x3 array into a 3x2 array.
	 *
	 * @param   array    array to rotate
	 * @param   boolean  keep the keys in the final rotated array. the sub arrays of the source array need to have the same key values.
	 *                   if your subkeys might not match, you need to pass FALSE here!
	 * @return  array
	 */
	public static function rotate($source_array, $keep_keys = TRUE)
	{
		$new_array = array();
		foreach ($source_array as $key => $value)
		{
			$value = ($keep_keys === TRUE) ? $value : array_values($value);
			foreach ($value as $k => $v)
			{
				$new_array[$k][$key] = $v;
			}
		}

		return $new_array;
	}

	/**
	 * Removes a key from an array and returns the value.
	 *
	 * @param   string  key to return
	 * @param   array   array to work on
	 * @return  mixed   value of the requested array key
	 */
	public static function remove($key, & $array)
	{
		if ( ! array_key_exists($key, $array))
			return NULL;

		$val = $array[$key];
		unset($array[$key]);

		return $val;
	}

	
	/**
	 * Extract one or more keys from an array. Each key given after the first
	 * argument (the array) will be extracted. Keys that do not exist in the
	 * search array will be NULL in the extracted data.
	 *
	 * @param   array   array to search
	 * @param   string  key name
	 * @return  array
	 */
	public static function extract(array $search, $keys)
	{
		// Get the keys, removing the $search array
		$keys = array_slice(func_get_args(), 1);

		$found = array();
		foreach ($keys as $key)
		{
			if (isset($search[$key]))
			{
				$found[$key] = $search[$key];
			}
			else
			{
				$found[$key] = NULL;
			}
		}

		return $found;
	}

	/**
	 * Because PHP does not have this function.
	 *
	 * @param   array   array to unshift
	 * @param   string  key to unshift
	 * @param   mixed   value to unshift
	 * @return  array
	 */
	public static function unshift_assoc( array & $array, $key, $val)
	{
		$array = array_reverse($array, TRUE);
		$array[$key] = $val;
		$array = array_reverse($array, TRUE);

		return $array;
	}

	/**
	 * Because PHP does not have this function, and array_walk_recursive creates
	 * references in arrays and is not truly recursive.
	 *
	 * @param   mixed  callback to apply to each member of the array
	 * @param   array  array to map to
	 * @return  array
	 */
	public static function map_recursive($callback, array $array)
	{
		foreach ($array as $key => $val)
		{
			// Map the callback to the key
			$array[$key] = is_array($val) ? arr::map_recursive($callback, $val) : call_user_func($callback, $val);
		}

		return $array;
	}

	/**
	 * Binary search algorithm.
	 *
	 * @param   mixed    the value to search for
	 * @param   array    an array of values to search in
	 * @param   boolean  return false, or the nearest value
	 * @param   mixed    sort the array before searching it
	 * @return  integer
	 */
	public static function binary_search($needle, $haystack, $nearest = FALSE, $sort = FALSE)
	{
		if ($sort === TRUE)
		{
			sort($haystack);
		}

		$high = count($haystack);
		$low = 0;

		while ($high - $low > 1)
		{
			$probe = ($high + $low) / 2;
			if ($haystack[$probe] < $needle)
			{
				$low = $probe;
			}
			else
			{
				$high = $probe;
			}
		}

		if ($high == count($haystack) OR $haystack[$high] != $needle)
		{
			if ($nearest === FALSE)
				return FALSE;

			// return the nearest value
			$high_distance = $haystack[ceil($low)] - $needle;
			$low_distance = $needle - $haystack[floor($low)];

			return ($high_distance >= $low_distance) ? $haystack[ceil($low)] : $haystack[floor($low)];
		}

		return $high;
	}

	/**
	 * Emulates array_merge_recursive, but appends numeric keys and replaces
	 * associative keys, instead of appending all keys.
	 *
	 * @param   array  any number of arrays
	 * @return  array
	 */
	public static function merge()
	{
		$total = func_num_args();

		$result = array();
		for ($i = 0; $i < $total; $i++)
		{
			foreach (func_get_arg($i) as $key => $val)
			{
				if (isset($result[$key]))
				{
					if (is_array($val))
					{
						// Arrays are merged recursively
						$result[$key] = arr::merge($result[$key], $val);
					}
					elseif (is_int($key))
					{
						// Indexed arrays are appended
						array_push($result, $val);
					}
					else
					{
						// Associative arrays are replaced
						$result[$key] = $val;
					}
				}
				else
				{
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;
	}

	/**
	 * Overwrites an array with values from input array(s).
	 * Non-existing keys will not be appended!
	 *
	 * @param   array   key array
	 * @param   array   input array(s) that will overwrite key array values
	 * @return  array
	 */
	public static function overwrite($array1)
	{
		foreach (array_slice(func_get_args(), 1) as $array2)
		{
			foreach ($array2 as $key => $value)
			{
				if (array_key_exists($key, $array1))
				{
					$array1[$key] = $value;
				}
			}
		}

		return $array1;
	}

	/**
	 * Fill an array with a range of numbers.
	 *
	 * @param   integer  stepping
	 * @param   integer  ending number
	 * @return  array
	 */
	public static function range($step = 10, $max = 100)
	{
		if ($step < 1)
			return array();

		$array = array();
		for ($i = $step; $i <= $max; $i += $step)
		{
			$array[$i] = $i;
		}

		return $array;
	}

	/**
	 * Recursively convert an array to an object.
	 *
	 * @param   array   array to convert
	 * @return  object
	 */
	public static function to_object(array $array, $class = 'stdClass')
	{
		$object = new $class;

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				// Convert the array to an object
				$value = arr::to_object($value, $class);
			}

			// Add the value to the object
			$object->{$key} = $value;
		}

		return $object;
	}

    /*
     * Индексирует массив по ключу
     * $key – уникальное значение в выборке (напр. ID)
     *
     * @param array исходный массив для индексации
     * @param string название поля, по которому индексируем
     * @return array
     */
    public static function toolIndexArrayBy($arr, $key){

        $result = array();
        foreach($arr as $item){
            if(is_object($item))
                $result[$item->$key] = $item;
            else if(is_array($item))
                $result[$item[$key]] = $item;
        }
        return $result;
    }

    /**
     * Список параметров для <select>
     * @param $arr
     * @param $key
     * @param $title
     * @return array
     */
    public static function for_select($arr, $key, $title){
        $result = array();
        foreach($arr as $item){
            if(is_object($item))
                $result[$item->$key] = $item->$title;
            else if(is_array($item))
                $result[$item[$key]] = $item[$title];
        }
        return $result;
    }

    /*
     * Получение массива значений определенного поля - для WHERE IN
     *
     * @param array исходный массив для индексации
     * @param string название поля, по которому индексируем
     * @return array
     */
    public static function get_keys_array($arr, $key){
        $result = array();
        foreach($arr as $item){
            if(is_object($item) && isset($item->$key)){
                $result[] = $item->$key;
            }
            elseif(is_array($item) && isset($item[$key])){
                $result[] = $item[$key];
            }
        }
        return $result;
    }

    /*
     * Группировка элементов массива по указанному полю $group_field
     *
     * @param array исходный массив для группировки
     * @param string название поля, по которому происходит группировка
     * @param string если $result_field указан = в группу попадают только значения этого поля
     */
    public static function get_grouped_array($arr, $group_field, $result_field = ''){
        $result = array();
        foreach($arr as $item){
            if(is_object($item) && isset($item->$group_field)){
                if(!empty($result_field))
                    $result[$item->$group_field][] = $item->$result_field;// только поле
                else
                    $result[$item->$group_field][] = $item;// вся сторока
            }
            elseif(is_array($item) && isset($item[$group_field])){
                if(!empty($result_field))
                    $result[$item[$group_field]][] = $item[$result_field];// только поле
                else
                    $result[$item[$group_field]][] = $item;// вся строка
            }
        }
        return $result;
    }

    /*
     * Построение дерева
     *
     * @param array ссылка на массив категорий
     * @param integer ID родительского элемента
     * @return array
     */
    public static function build_tree(&$rs,$parent)
    {
        $out = array();
        if (!isset($rs[$parent]))
        {
            return $out;
        }
        foreach ($rs[$parent] as $row)
        {
            $chidls = build_tree($rs,$row['id']);
            if ($chidls)
                $row['childs'] = $chidls;

            $out[] = $row;
        }
        return $out;
    }

    public static function table_to_tree_array($arr, $mk = 'id', $sk = 'parent_id', $child = 'child') {
        if(!$arr) {
            return array();
        }

        $l = count($arr);
        for($i = 0; $i < $l; $i++) {
            $mas[ $arr[$i][$mk] ] = &$arr[$i];
        }

        foreach($mas as $k => $v) {
            $mas[ $v[$sk] ][$child][] = &$mas[$k];
        }

        $res = array();
        foreach($arr as $v) {
            if(isset($v[$sk]) && $v[$sk] == 0) {
                $res[] = $v;
            }
        }
        $arr = $res;
        return $arr;
    }

    /**
     * Сортирока массива по нескольким полям
     * @return mixed
     */
    // Usage: $sorted = array_order_by($data, 'points', SORT_DESC, 'time', SORT_ASC, 'friends', SORT_DESC);
    public static function array_order_by()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp[$field] = array();
                foreach ($data as $key => $row)
                    $tmp[$field][$key] = $row[$field];
                $args[$n] = &$tmp[$field];
            } else {
                $args[$n] = &$args[$n];
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

} // End arr