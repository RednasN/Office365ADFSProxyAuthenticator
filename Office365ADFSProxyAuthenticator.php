<?php

//Usage
//$auth = new Office365Authenticator();
//$authenticated = $auth -> authenticate("firstname.lastname@yourcompany.net", "password");


//This class is based on: https://blogs.msdn.microsoft.com/omarv/2012/11/15/developing-windows-8-store-apps-for-sharepoint-online-with-sso-single-sign-on/
//This class is used to authenticate a user trough Office365. 
//To use this class a ADFS proxy service following O365 SSO recommended guidelines is mandatory.
//Version 0.1
//Author: Sander Nijhof https://nijhof.biz
class Office365ADFSProxyAuthenticator {

	private $url = "https://login.microsoftonline.com/GetUserRealm.srf";
	private $identifier = "urn:federation:MicrosoftOnline";
	
	
	public function authenticate($login, $password){
		
		$authenticationUrl = $this -> retrieveAuthUrl($login);
		
		//This is where the real authenticating starts.
		
		//Replacing values in SOAP envelop.
		$envelop = file_get_contents('./SoapEnvelop.txt');	
		$envelop = str_replace('[username]', $login, $envelop);
		$envelop = str_replace('[password]', $password, $envelop);
		$envelop = str_replace('[url]', $this -> identifier, $envelop);
		$envelop = str_replace('[toUrl]', $authenticationUrl, $envelop);

		
		$handle = curl_init($authenticationUrl);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $envelop);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/soap+xml; charset=utf-8')); 
		$result = curl_exec($handle);
		//echo $result;
		
		if (strpos($result, $this -> identifier) !== false) {
			return true;
		}
		return false;		
	}
	
	//Retrieve authentication URL. 
	private function retrieveAuthUrl($login)
	{
		$data = array(
		'handler' => "1",
		'login' => $login);
		
		$handle = curl_init($this -> url);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
			
		$result = curl_exec($handle);
		
		$jsonResult = json_decode($result, true);		
		
		$authenticationUrl =  $jsonResult['AuthURL'];		
		$authenticationUrl = parse_url($authenticationUrl, PHP_URL_HOST);
		$authenticationUrl = "https://". $authenticationUrl . "/adfs/services/trust/2005/usernamemixed/";
		return $authenticationUrl;
	}
}


	
	
?>

