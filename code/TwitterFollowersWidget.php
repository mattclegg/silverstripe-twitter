<?php

// YahooWidgets Widget 0.0.1 for the SilverStripe Blog Module
// 01.03.2009
// By nivanka@whynotonline.com
// Save this and TwitterFollowersWidget.ss to twitter_widget/ and run "db/build".

require_once("TwitterAPI.php");

class TwitterFollowersWidget extends Widget{
	
	//The widget info
	static $title = "My Twitter Followers";
	static $cmsTitle = "Twitter Followers Widgets";
	static $description = "This widget displays your twitter followers on any of your webpages. (you need to have a twitter page setup in order to get this widget working)";

	static $db = array(
		"ShowAvatar" => "Boolean",
		"NumberOfFriends" => "Int"
	);
	static $defaults = array(
		"ShowAvatar" => '1',
		"NumberOfFriends" => 10
	);
	
	/**
	 * the getCMSFields function to collect relavant information for the widget to work
	 */
	function getCMSFields(){
		return new FieldSet(
			new DropdownField("ShowAvatar", "Show your follower's avatar.", array("0" => "NO", "1"=>"Yes")),
			new NumericField("NumberOfFriends", "Number of followers you need to show on the page.")
		);
	}


	/**
	 * Get the friends of the user
	 */
	function Followers(){
		Requirements::css("twitter/css/friendswidget.css");
		$twitterPage = DataObject::get_one("TwitterPage");
		if($twitterPage->TwitterUsername && $twitterPage->TwitterPassword){
			$twitterApi = new TwitterAPI($twitterPage->TwitterUsername, $twitterPage->TwitterPassword);
			$xml = $twitterApi->doCall("http://twitter.com/statuses/followers/".$twitterPage->TwitterUsername.".xml", true);
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
								"Name" => $follower['name'],
								"URL" => $follower['url'],
								"Avatar" => $follower['profile_image_url'],
								"Location" => $follower['location']
							)
						)
					);
					$count += 1;
					if($count >= $this->NumberOfFriends)
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
}

?>
