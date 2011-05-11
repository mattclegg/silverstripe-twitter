<?php

require_once('TwitterAPI.php');

class TwitterPage extends Page{
	
	static $db = array(
		'TwitterUsername' => 'Varchar',
		'TwitterPassword' => 'Varchar',
		'TwitterURL' => 'Varchar',
		'ShowUserTimeLine' => 'Boolean',
		'ShowPublicTimeLine' => 'Boolean',
		'ShowFriendsTimeLine' => 'Boolean',
		'ShowFollowersAvatar' => 'Boolean',
		'ShowFriendsAvatar' => 'Boolean',
		'HowManyFollowers' => 'Int',
		'HowManyFriends' => 'Int',
		'HowManyStatuses' => 'Int',
		'SinceWhen' => 'Date'	
	);

	static $defaults = array(
		'ShowUserTimeLine' => '0',
		'ShowPublicTimeLine' => '0',
		'ShowFriendsTimeLine' => '1',
		'HowManyFollowers' => 15,
		'HowManyFriends' => 15,
		'HowManyStatuses' => 20,
		'ShowFollowersAvatar' => '1',
		'ShowFriendsAvatar' => '1'
	);
	
	function getCMSFields(){
		
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Content.Twitter', new HeaderField($title = 'Your Twitter account details',$headingLevel = 3));
		$fields->addFieldToTab('Root.Content.Twitter', new TextField('TwitterUsername','Your twitter username'));
		$fields->addFieldToTab('Root.Content.Twitter', new PasswordField('TwitterPassword','Your twitter password'));
		$fields->addFieldToTab('Root.Content.Twitter', new TextField('TwitterURL','Your twitter url'));
		$fields->addFieldToTab('Root.Content.Twitter', new DateField('SinceWhen','Since when do you need to show the messages, this will show the messages which are up to 24 hours old and created after the date, leave it blank if you dont want to use this feature'));


		$fields->addFieldToTab('Root.Content.Twitter', new HeaderField($title = 'Time Line Controllers',$headingLevel = 3));
		$fields->addFieldToTab('Root.Content.Twitter', new CheckboxField('ShowUserTimeLine','Show user timeline'));
		$fields->addFieldToTab('Root.Content.Twitter', new CheckboxField('ShowPublicTimeLine','Show public timeline'));
		$fields->addFieldToTab('Root.Content.Twitter', new CheckboxField('ShowFriendsTimeLine','Show friends timeline'));
		$fields->addFieldToTab('Root.Content.Twitter', new NumericField('HowManyStatuses','How many status messages you need to show?'));

		$fields->addFieldToTab('Root.Content.Twitter', new HeaderField($title = 'Controllers for your followers',$headingLevel = 3));
		$fields->addFieldToTab('Root.Content.Twitter', new NumericField('HowManyFollowers','How many followers you need to show? Make this 0 if you dont want to show any.'));
		$fields->addFieldToTab('Root.Content.Twitter', new CheckboxField('ShowFollowersAvatar','Show followers avatars'));

		$fields->addFieldToTab('Root.Content.Twitter', new HeaderField($title = 'Controllers for your friends',$headingLevel = 3));
		$fields->addFieldToTab('Root.Content.Twitter', new NumericField('HowManyFriends','How many friends you need to show? Make this 0 if you dont want to show any.'));
		$fields->addFieldToTab('Root.Content.Twitter', new CheckboxField('ShowFriendsAvatar','Show friends avatars'));
		return $fields;
		
	}
	
}

class TwitterPage_Controller extends Page_Controller{
	
	/**
	 * Load the javascripts and css files
	 */
	 function init(){
	 	parent::init();
		Requirements::css('twitter/css/twitter.css');
	 }
	 
	/**
	 * Get the followers of the user
	 */
	function Followers(){
		if($this->checkCredentials()){
			$twitterApi = new TwitterAPI($this->TwitterUsername, $this->TwitterPassword);
			$xml = $twitterApi->doCall('http://twitter.com/statuses/followers/'.$this->TwitterUsername.'.xml', true);
			if($xml){
				// init var
				$followers = array();
		
				// loop statuses
				foreach ($xml->user as $user) $followers[] = $twitterApi->userXMLToArray($user);
	
				$output = new DataObjectSet();
				
				if(!is_array($followers))
					return false;
				$count = 0;
				foreach($followers as $follower){
					$output->push(
						new ArrayData(
							array(
								'Name' => $follower['name'],
								'URL' => $follower['url'],
								'Avatar' => $follower['profile_image_url']
							)
						)
					);
					$count += 1;
					if($count >= $this->HowManyFollowers)
						return $output;
				}
				
				return $output;
			}
			else 
				return false;
		}
		else
			return false;
	}
	
	
	/**
	 * Get the friends of the user
	 */
	function Friends(){
		if($this->checkCredentials()){
			$twitterApi = new TwitterAPI($this->TwitterUsername, $this->TwitterPassword);
			$xml = $twitterApi->doCall('http://twitter.com/statuses/friends/'.$this->TwitterUsername.'.xml', true);
			if($xml){
				// init var
				$followers = array();
		
				// loop statuses
				foreach ($xml->user as $user) $followers[] = $twitterApi->userXMLToArray($user);
	
				$output = new DataObjectSet();
				
				if(!is_array($followers))
					return false;
				$count = 0;
				foreach($followers as $follower){
					$output->push(
						new ArrayData(
							array(
								'Name' => $follower['name'],
								'URL' => $follower['url'],
								'Avatar' => $follower['profile_image_url']
							)
						)
					);
					$count += 1;
					if($count >= $this->HowManyFriends)
						return $output;
				}
				
				return $output;
			}
			else 
				return false;
		}
		else
			return false;
	}
	
	/**
	 * Get the friends time line
	 */
	function UserTimeLine(){
		if($this->checkCredentials()){

				
			$params = array(
				'count' => $this->HowManyStatuses
			);			
			
			$since = null;
			$stamp = 0;			
			if($this->SinceWhen){
				$stamp = mktime(0,0,0,$this->SinceWhen->Format("m"), $this->SinceWhen->Format("d"), $this->SinceWhen->Format("Y"));
			}
			if($stamp)
				$params['scince'] = $stamp;
			
			$twitterApi = new TwitterAPI($this->TwitterUsername, $this->TwitterPassword);
			$xml = $twitterApi->doCall('http://twitter.com/statuses/user_timeline/'.$this->TwitterUsername.'.xml', true, $params);
			if($xml){
				// init var
				$msgs = array();
		
				// loop statuses
				foreach ($xml->status as $status) $msgs[] = $twitterApi->statusXMLToArray($status);
								
				$output = new DataObjectSet();
				foreach($msgs as $status){
					$output->push(new ArrayData(array(
						'Name' => $status['user']['name'],
						'URL' => $status['user']['url'],
						'Avatar' => $status['user']['profile_image_url'],
						'Text' => $this->checkForURLs($status['text']),
						'Time' => $status['created_at'],
					)));
				}
				return $output;
			}
			else
				return false;
			
		}
		else
			return false;
	}

	/**
	 * Get the public time line
	 */
	function PublicTimeLine(){
		if($this->checkCredentials()){

			$params = array();			
			// setting up for the since parameter
			$since = null;
			$stamp = 0;			
			if($this->SinceWhen){
				$stamp = mktime(0,0,0,$this->SinceWhen->Format("m"), $this->SinceWhen->Format("d"), $this->SinceWhen->Format("Y"));
			}
			if($stamp)
				$params['since'] = $stamp;
				
			$twitterApi = new TwitterAPI($this->TwitterUsername, $this->TwitterPassword);
			$xml = $twitterApi->doCall('http://twitter.com/statuses/public_timeline/'.$this->TwitterUsername.'.xml', true, $params);
			if($xml){
				// init var
				$msgs = array();
		
				// loop statuses
				foreach ($xml->status as $status) $msgs[] = $twitterApi->statusXMLToArray($status);
								
				$output = new DataObjectSet();
				
				$count = 0;
				
				foreach($msgs as $status){
					$count += 1;
					if($count > $this->HowManyStatuses)
						break;
					$output->push(new ArrayData(array(
						'Name' => $status['user']['name'],
						'URL' => $status['user']['url'],
						'Avatar' => $status['user']['profile_image_url'],
						'Text' => $this->checkForURLs($status['text']),
						'Time' => $status['created_at'],
					)));

				}
				return $output;
			}
			else{
				return false;
			}
			
		}
		else
			return false;
	}
	
	/**
	 * Get the public time line
	 */
	function FriendsTimeLine(){
		if($this->checkCredentials()){

				
			$params = array(
				'count' => $this->HowManyStatuses
			);			
			// Check for the since parameter
			$since = null;
			$stamp = 0;			
			if($this->SinceWhen){
				$stamp = mktime(0,0,0,$this->SinceWhen->Format("m"), $this->SinceWhen->Format("d"), $this->SinceWhen->Format("Y"));
			}
			if($stamp)
				$params['since'] = $stamp;
				
			$twitterApi = new TwitterAPI($this->TwitterUsername, $this->TwitterPassword);
			$xml = $twitterApi->doCall('http://twitter.com/statuses/friends_timeline/'.$this->TwitterUsername.'.xml', true, $params);
			if($xml){
				// init var
				$msgs = array();
		
				// loop statuses
				foreach ($xml->status as $status) $msgs[] = $twitterApi->statusXMLToArray($status);
								
				$output = new DataObjectSet();
				foreach($msgs as $status){
					$output->push(new ArrayData(array(
						'Name' => $status['user']['name'],
						'URL' => $status['user']['url'],
						'Avatar' => $status['user']['profile_image_url'],
						'Text' => $this->checkForURLs($status['text']),
						'Time' => $status['created_at'],
					)));
				}
				return $output;
			}
			else
				return false;
			
		}
		else
			return false;
	}
	
	
	/**
	 * Check for any URLs and make them usable
	 */
	 function checkForURLs($text){
	 	if($text){
			$words = explode(' ',$text);
			$html = '';
			
			foreach($words as $word){
				if(preg_match('/^http/', $word)){
					$htmlword = '<a href=\'' . $word . '\' target=\'_blank\'>' . $word . '</a>';
				}
				else if(strcmp('$word', '&') == 0)
					$htmlword = '&amp;';
				else
					$htmlword = $word;
				$html .= $htmlword . ' ';
			}
			return $html;
		}
		else
			return $text;
	 }
	/**
	 * Check whether the credentials are valid or not
	 */
	function checkCredentials(){
		if(!$this->TwitterUsername)
			return false;
		if(!$this->TwitterPassword)
			return false;
		return true;
	}
}

?>
