<?php

Yii::import('application.modules.store.models.StoreCurrency');

/**
 * Class to work with currencies
 */
class SCurrencyManager extends CComponent
{

	/**
	 * @var array available currencies
	 */
	private $_currencies = array();

	/**
	 * @var StoreCurrency main currency
	 */
	private $_main;

	/**
	 * @var StoreCurrency current active currency
	 */
	private $_active;

	/**
	 * @var StoreCurrency default currency
	 */
	private $_default;

	/**
	 * @var string
	 */
	public $cacheKey = 'currency_manager';

	public function init()
	{
		foreach($this->loadCurrencies() as $currency)
		{
			$this->_currencies[$currency->id] = $currency;
			if($currency->main)
				$this->_main = $currency;
			if($currency->default)
				$this->_default = $currency;
		}

		$this->setCurrencyByIP();
	}

	/**
	 * @return array
	 */
	public function getCurrencies()
	{
		return $this->_currencies;
	}

	/**
	 * Detect user active currency
	 * @return StoreCurrency
	 */
	public function detectActive()
	{
		// Detect currency from session
		$sessCurrency = Yii::app()->session['currency'];

		if($sessCurrency && isset($this->_currencies[$sessCurrency]))
			return $this->_currencies[$sessCurrency];
		return $this->_default;
	}

	/**
	 * @param int $id currency id
	 */
	public function setActive($id)
	{
		if(isset($this->_currencies[$id]))
			$this->_active = $this->_currencies[$id];
		else
			$this->_active = $this->_default;

		Yii::app()->session['currency'] = $this->_active->id;
	}

	/**
	 * get active currency
	 * @return StoreCurrency
	 */
	public function getActive()
	{
		return $this->_active;
	}

	/**
	 * @return StoreCurrency main currency
	 */
	public function getMain()
	{
		return $this->_main;
	}

	/**
	 * Convert sum from main currency to selected currency
	 * @param mixed $sum
	 * @param mixed $id StoreCurrency. If not set, sum will be converted to active currency
	 * @return float converted sum
	 */
	public function convert($sum, $id=null)
	{
		if($id !== null && isset($this->_currencies[$id]))
			$currency = $this->_currencies[$id];
		else
			$currency = $this->_active;

		return $currency->rate * $sum;
	}

	/**
	 * Convert from active currency to main
	 * @param $sum
	 * @return float
	 */
	public function activeToMain($sum)
	{
		return $sum / $this->getActive()->rate;
	}

	/**
	 * @return array
	 */
	public function loadCurrencies()
	{
		$currencies = Yii::app()->cache->get($this->cacheKey);

		if(!$currencies)
		{
			$currencies = StoreCurrency::model()->findAll();
			Yii::app()->cache->set($this->cacheKey, $currencies);
		}

		return $currencies;
	}

    /**
     * определяем страну пользователя по IP
     * свежая база IP-адресов (только страны) здесь:
     * http://lite.ip2location.com/database/ip-country
     * @return string
     */
    public function getCountryCodeByIp()
    {
        $ip = CHttpRequest::getUserHostAddress();
//        $ip = '192.196.142.22'; // test France
//        $ip = '188.163.97.7'; // test Ukraine
//        $ip = '72.229.28.185'; // test United States
        $ip_long = ip2long($ip);
        $country_code = Yii::app()->db->createCommand()
            ->select('country_code')
            ->from('ip2location')
            ->where(':ip_long between `ip_from` and `ip_to`', array(':ip_long'=>$ip_long))
            ->queryScalar();
        return (!empty($country_code)) ? $country_code : '';
    }

    /**
     * валюта по IP-информации о пользователе:
     * Украина = гривна
     * Европа = евро
     * Остальные = доллар
     */
    public function setCurrencyByIP()
    {
//        Yii::app()->session['currency'] = ''; // uncomment for testing
        // проверяем валюту в сессии
        $sessCurrency = Yii::app()->session['currency'];
        if(!empty($sessCurrency)){
            // пользователь сам выбрал валюту
            // или её уже назначили по IP ранее в этой сессии пользователя
            // делаем активной валюту из сессии
            $this->setActive($sessCurrency);
            return true;
        }

        $country_code = $this->getCountryCodeByIp();
        $currencies = $this->getCurrencies();
        if(!empty($country_code) && !empty($currencies)){
            $euro_countries = array(
                'AT', 'BE', 'CY', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT',
                'LV', 'LT', 'LU', 'MT', 'NL', 'PT', 'SK', 'SI', 'ES',
            );
            $ua = 'UA';
            $set_iso = '';
            if(in_array($country_code, $euro_countries)){
                // EUR
                $set_iso = 'EUR';
            }
            elseif($country_code == $ua){
                // UAH
                $set_iso = 'UAH';
            }
            if(!empty($set_iso)){
                // устанавливаем новую активную валюту по IP
                foreach ($currencies as $currency) {
                    if($currency->iso == $set_iso){
                        $this->setActive($currency->id);
                        break;
                    }
                }
                return true;
            }
        }
        $this->setActive($this->detectActive()->id); // default
        return true;
    }

    /**
     * форматирование цены по шаблону – или в формат {знак_валюты}{сумма}
     * @param $sum
     * @return mixed|string
     */
    public function format($sum)
    {
        if(
            !empty($this->_active->price_format) &&
            (strpos($this->_active->price_format, '{sum}') !== false)
        ) {
            return str_replace('{sum}', $sum, $this->_active->price_format);
        }
        else {
            return $this->_active->symbol . $sum;
        }
    }
}