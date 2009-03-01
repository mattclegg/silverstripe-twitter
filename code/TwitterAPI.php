<?php

class TwitterAPI extends RestfulService{
	
	private static $username;
	private static $password;
	
	function __construct($username = '', $password = ''){
		$this->setUsername($username);
		$this->setPassword($password);
	}
	
	/*
	 * Private function to set the username
	 */
	private function setUsername($username){
		self::$username = $username;
	}
	/*
	 * Private function to set the password
	 */
	private function setPassword($password){
		self::$password = $password;
	}
	
	/*
	 * This functions does the real call, and it returns and XML object.
	 */
	function doCall($url = null, $requireAuth = false,$params = null){
		try{
			$twitter = new RestfulService( $url );
			if($requireAuth)
				$twitter->basicAuth(self::$username, self::$password);
			if($params)
				$twitter->setQueryString($params);
			$conn = $twitter->connect('');
			
			$xml = @simplexml_load_string($conn);
			if($xml == false)
				return false;
			else return $xml;
		}
		catch(Exception $e){
			return false;
		}
	}
	
	/**
	 * Converts a piece of XML into a status-array
	 *
	 * @return	array
	 * @param	SimpleXMLElement $xml
	 */
	function statusXMLToArray($xml){
		// validate xml
		if(!isset($xml->id, $xml->text, $xml->created_at, $xml->source, $xml->truncated, $xml->in_reply_to_status_id, $xml->in_reply_to_user_id, $xml->favorited, $xml->user)) throw new Exception( 'Invalid xml for message.' );

		// convert into array
		$aStatus['id'] = (string) $xml->id;
		$aStatus['created_at'] = (int) strtotime($xml->created_at);
		$aStatus['text'] = utf8_decode((string) $xml->text);
		$aStatus['source'] = (isset($xml->source)) ? (string) $xml->source : '';
		$aStatus['user'] = $this->userXMLToArray($xml->user);
		$aStatus['truncated'] = (isset($xml->truncated) && $xml->truncated == 'true');
		$aStatus['favorited'] = (isset($xml->favorited) && $xml->favorited == 'true');
		$aStatus['in_reply_to_status_id'] = (string) $xml->in_reply_to_status_id;
		$aStatus['in_reply_to_user_id'] = (string) $xml->in_reply_to_user_id;

		// return
		return $aStatus;
	}
	
	function userXMLToArray($xml, $extended = false){
		// validate xml
		if(!isset($xml->id, $xml->name, $xml->screen_name, $xml->description, $xml->location, $xml->profile_image_url, $xml->url, $xml->protected, $xml->followers_count)) throw new Exception( 'Invalid xml for message.' );


		// convert into array
		$aUser['id'] = (string) $xml->id;
		$aUser['name'] = utf8_decode((string) $xml->name);
		$aUser['screen_name'] = utf8_decode((string) $xml->screen_name);
		$aUser['description'] = utf8_decode((string) $xml->description);
		$aUser['location'] = utf8_decode((string) $xml->location);
		$aUser['url'] = (string) $xml->url;
		$aUser['protected'] = (isset($xml->protected) && $xml->protected == 'true');
		$aUser['followers_count'] = (int) $xml->followers_count;
		$aUser['profile_image_url'] = (string) $xml->profile_image_url;

		// extended info?
		if($extended)
		{
			if(isset($xml->profile_background_color)) $aUser['profile_background_color'] = utf8_decode((string) $xml->profile_background_color);
			if(isset($xml->profile_text_color)) $aUser['profile_text_color'] = utf8_decode((string) $xml->profile_text_color);
			if(isset($xml->profile_link_color)) $aUser['profile_link_color'] = utf8_decode((string) $xml->profile_link_color);
			if(isset($xml->profile_sidebar_fill_color)) $aUser['profile_sidebar_fill_color'] = utf8_decode((string) $xml->profile_sidebar_fill_color);
			if(isset($xml->profile_sidebar_border_color)) $aUser['profile_sidebar_border_color'] = utf8_decode((string) $xml->profile_sidebar_border_color);
			if(isset($xml->profile_background_image_url)) $aUser['profile_background_image_url'] = utf8_decode((string) $xml->profile_background_image_url);
			if(isset($xml->profile_background_tile)) $aUser['profile_background_tile'] = (isset($xml->profile_background_tile) && $xml->profile_background_tile == 'true');
			if(isset($xml->created_at)) $aUser['created_at'] = (int) strtotime((string) $xml->created_at);
			if(isset($xml->following)) $aUser['following'] = (isset($xml->following) && $xml->following == 'true');
			if(isset($xml->notifications)) $aUser['notifications'] = (isset($xml->notifications) && $xml->notifications == 'true');
			if(isset($xml->statuses_count)) $aUser['statuses_count'] = (int) $xml->statuses_count;
			if(isset($xml->friends_count)) $aUser['friends_count'] =  (int) $xml->friends_count;
			if(isset($xml->favourites_count)) $aUser['favourites_count'] = (int) $xml->favourites_count;
			if(isset($xml->time_zone)) $aUser['time_zone'] = utf8_decode((string) $xml->time_zone);
			if(isset($xml->utc_offset)) $aUser['utc_offset'] = (int) $xml->utc_offset;
		}

		// return
		return (array) $aUser;
	}
	
}

?>