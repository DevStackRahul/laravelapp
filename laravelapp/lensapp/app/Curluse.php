<?php

namespace App;

class Curluse
{
    
    
    public function Curldata($cUrl, $header){
            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, $cUrl);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $header);

            $curl_data = curl_exec($cURLConnection);
           
            curl_close($cURLConnection);
            return $curl_data;
    }
    
    
    public function CurlPostdata($cUrl, $pixelData, $header){
            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, $cUrl);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $pixelData);
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $header);

            $curl_data = curl_exec($cURLConnection);
            curl_close($cURLConnection);
            return $curl_data;
    }
    
    
    public function CurlPutdata($cUrl, $pixelData, $header){
            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, $cUrl);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $pixelData);
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $header);

            $curl_data = curl_exec($cURLConnection);
           
            curl_close($cURLConnection);
           
    }
    
    
     public function Curldeldata($cUrl, $header){
            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, $cUrl);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $header);

            $curl_data = curl_exec($cURLConnection);
           
            curl_close($cURLConnection);
           
    }
    
    //function for currency
    public function currency_symbol($cur){
		if(!$cur){
			return false;
		}
		$currencies = array(
		'USD'=>'$', // US Dollar
    	'EUR'=> '€', // Euro
    	'CRC'=> '₡', // Costa Rican Colón
    	'GBP'=> '£', // British Pound Sterling
    	'ILS'=> '₪', // Israeli New Sheqel
	    'INR'=> '₹', // Indian Rupee
	    'JPY'=> '¥', // Japanese Yen
	    'KRW'=> '₩', // South Korean Won
	    'NGN'=> '₦', // Nigerian Naira
	    'PHP'=> '₱', // Philippine Peso
	    'PLN'=> 'zł', // Polish Zloty
	    'PYG'=> '₲', // Paraguayan Guarani
	    'THB'=> '฿', // Thai Baht
	    'UAH'=> '₴', // Ukrainian Hryvnia
	    'VND'=> '₫', // Vietnamese Dong)
	);

		if(array_key_exists($cur,$currencies)){
			return $currencies[$cur];
		}else{
			return $cur;
		}
	}
  
    
}