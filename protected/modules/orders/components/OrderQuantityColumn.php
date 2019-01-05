<?php

class OrderQuantityColumn extends CDataColumn
{
	public function renderHeaderCell()
	{
		$this->headerHtmlOptions['width']='30px';
		parent::renderHeaderCell();
	}

	public function renderDataCellContent($row, $data)
	{
		$data = array(
			'{name}'  => 'quantity['.$data->id.']',
			'{value}' => $data->quantity,
		);
		echo strtr('<input type="text" name="{name}" value="{value}" class="order_quantity_short">', $data);
	}
	
	function transliterate($input){
		$translit = array(
  
            'а' => 'a',   'б' => 'b',   'в' => 'v',
 
            'г' => 'g',   'д' => 'd',   'е' => 'e',
 
            'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',
 
            'и' => 'i',   'й' => 'j',   'к' => 'k',
 
            'л' => 'l',   'м' => 'm',   'н' => 'n',
 
            'о' => 'o',   'п' => 'p',   'р' => 'r',
 
            'с' => 's',   'т' => 't',   'у' => 'u',
 
            'ф' => 'f',   'х' => 'x',   'ц' => 'c',
 
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shh',
 
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'\'',
 
            'э' => 'e\'',   'ю' => 'yu',  'я' => 'ya',
         
 
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
 
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
 
            'Ё' => 'YO',   'Ж' => 'Zh',  'З' => 'Z',
 
            'И' => 'I',   'Й' => 'J',   'К' => 'K',
 
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
 
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
 
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
 
            'Ф' => 'F',   'Х' => 'X',   'Ц' => 'C',
 
            'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SHH',
 
            'Ь' => '\'',  'Ы' => 'Y\'',   'Ъ' => '\'\'',
 
            'Э' => 'E\'',   'Ю' => 'YU',  'Я' => 'YA',
 
        );
 
       $word = strtr($input, array_flip($translit));
	   return $word;
	   
	}  
}