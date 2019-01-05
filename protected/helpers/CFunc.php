<?php
/**
 * Created by PhpStorm.
 * User: korol
 * Date: 20.02.17
 * Time: 11:27
 */

class CFunc {

    /*
     * получаем только цифры из строки – для телефона, например
     *
     * @param string исходная строка
     * @return string строка, состоящая только из цифр исходной строки
     */
    public static function get_numbers($string)
    {
        $return = '';
        if(strlen($string) > 0){
            $num_arr = array();
            preg_match_all('#\d#', $string, $num_arr);
            if(!empty($num_arr))
                $return = implode('', $num_arr[0]);
        }
        return $return;
    }

    /*
     * форматируем стоимость товара
     *
     * @param integer стоимость товара
     * @return string отформатированная строка стоимости товара
     */
    public static function show_price($price, $currency = 'Р')
    {
        $price = floatval($price);
        return number_format($price, 0, '.', ' ') . ((!empty($currency)) ? ' ' . $currency : '');
    }

    /*
     * форматируем дату выпуска товара
     *
     * @param string дата выпуска
     * @return string отформатированная дата выпуска
     */
    public static function show_date($date, $template = 'd.m.Y')
    {
        if(!empty($template))
            $date = date($template, strtotime($date));

        return $date;
    }

    /*
     * аналог ucfirst для multi-byte encodings
     */
    public static function my_mb_ucfirst($string) {
        $string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
        return $string;
    }

    /**
     * @param int $n - количество
     * @param string $form1
     * @param string $form2
     * @param string $form5
     * @return string
     */
    public static function pluralForm($n = 0, $form1 = 'просмотр', $form2 = 'просмотра', $form5 = 'просмотров')
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $form5;
        if ($n1 > 1 && $n1 < 5) return $form2;
        if ($n1 == 1) return $form1;
        return $form5;
    }

    /**
     * @param $var
     * @param string $label
     * @param bool $echo
     * @return mixed|string
     */
    public static function dump($var, $label = 'Dump', $echo = TRUE)
    {
        // Store dump in variable
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // Add formatting
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">' . $label . ' => ' . $output . '</pre>';

        // Output
        if ($echo == TRUE) {
            echo $output;
        }
        else {
            return $output;
        }
    }

    /**
     * @param $var
     * @param string $label
     * @param bool $echo
     */
    public static function dump_exit($var, $label = 'Dump', $echo = TRUE) {
        self::dump($var, $label, $echo);
        exit;
    }

    /**
     * Generate a random string.
     * get_random_string() will return a random string with length 6-8 of lowercase letters only.
     *
     * @param  integer  $chars_min the minimum length of string (optional, default 6)
     * @param  integer  $chars_max the maximum length of string (optional, default 8)
     * @param  boolean  $use_upper_case boolean use upper case for letters, means stronger string (optional, default false)
     * @param  boolean  $include_numbers boolean include numbers, means stronger string (optional, default false)
     * @param  boolean  $include_special_chars include special characters, means stronger string (optional, default false)
     * @return string   random string
     */
    public static function get_random_string($chars_min=6, $chars_max=8, $use_upper_case=false, $include_numbers=false, $include_special_chars=false)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
        if($include_special_chars) {
            $selection .= "!@\"#$%&[]{}?|";
        }
        $string = "";
        for($i=0; $i<$length; $i++) {
            $current_letter = $use_upper_case
                ? (
                    rand(0,1)
                    ? strtoupper($selection[(rand() % strlen($selection))])
                    : $selection[(rand() % strlen($selection))]
                    )
                : $selection[(rand() % strlen($selection))];
            $string .=  $current_letter;
        }
        return $string;
    }
} 