<?php

class LPLib_Ferien_Connector
{
  const BASE_URL = 'https://ferien-api.de/api/v1/holidays/BY';

  const LAND_BAYERN = 'BY';

  private static $cache = array();
  private static $itsInstance = null;

  private function __construct(){}

  public static function getInstance()
  {
    if(self::$itsInstance == null)
      self::$itsInstance = new LPLib_Ferien_Connector();

    return self::$itsInstance;
  }


  public function getFerientage()
    {
      $result = $this->_file_get_contents_t_curl(LPLib_Ferien_Connector::BASE_URL);
      self::$cache = json_decode($result,true);

      return self::$cache;
    }

    private function _file_get_contents_t_curl($url) {
  		$file = @file_get_contents($url);
  		if(!empty($file))
  			return $file;
  		else
  		{
  			$ch = curl_init();

  			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
  			curl_setopt($ch, CURLOPT_HEADER, 0);
  			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  			curl_setopt($ch, CURLOPT_URL, $url);
  			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

  			$data = curl_exec($ch);
  			curl_close($ch);

  			return $data;
      }
    }
  }
?>
