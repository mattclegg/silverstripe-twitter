<?php

// YahooWidgets Widget 0.0.1 for the SilverStripe Blog Module
// 01.03.2009
// By nivanka@whynotonline.com
// Save this and TwitterFollowersWidget.ss to twitter_widget/ and run "db/build".

require_once("TwitterAPI.php");

class TwitterStatusWidget extends Widget{
	
	//The widget info
	static $title = "My Twitter Status";
	static $cmsTitle = "Twitter Status Widgets";
	static $description = "This widget displays your twitter Status on any of your webpages. (you need to have a twitter page setup in order to get this widget working)";

	static $db = array(
		"ShowAvatar" => "Boolean",
		"NumberOfStatus" => "Int",
		"SinceWhen" => "Text"
	);
	static $defaults = array(
		"ShowAvatar" => '1',
		"NumberOfStatus" => 10,
		"SinceWhen" => "0"
	);
	
	/**
	 * the getCMSFields function to collect relavant information for the widget to work
	 */
	function getCMSFields(){
		return new FieldSet(
			new DropdownField("ShowAvatar", "Show your follower's avatar.", array("0" => "NO", "1"=>"Yes")),
			new NumericField("NumberOfStatus", "Number of followers you need to show on the page."),
			new TextField('SinceWhen','Since when do you need to show the messages (YYYY-MM-DD), this will show the messages which are up to 24 hours old and created after the date, leave it blank if you dont want to use this feature')
		);
	}


	function Status(){
		Requirements::css("twitter/css/friendswidget.css");
		$twitterPage = DataObject::get_one("TwitterPage");
		
		if($twitterPage->TwitterUsername && $twitterPage->TwitterPassword){

				
			$params = array(
				'count' => $this->NumberOfStatus
			);			
			
			$since = null;
			$stamp = 0;			
			if($this->SinceWhen){
				$time = explode("-", $this->SinceWhen);
				$stamp = mktime(0,0,0,$time[1], $time[2], $time[0]);
			}
			if($stamp)
				$params['scince'] = $stamp;
			
			$twitterApi = new TwitterAPI($twitterPage->TwitterUsername, $twitterPage->TwitterPassword);
			$xml = $twitterApi->doCall("http://twitter.com/statuses/user_timeline/".$twitterPage->TwitterUsername.".xml", true, $params);
			if($xml){
				// init var
				$msgs = array();
		
				// loop statuses
				foreach ($xml->status as $status) $msgs[] = $twitterApi->statusXMLToArray($status);
								
				$output = new DataObjectSet();
				foreach($msgs as $status){
					$output->push(new ArrayData(array(
						"Name" => $status['user']['name'],
						"URL" => $status['user']['url'],
						"Avatar" => $status['user']['profile_image_url'],
						"Text" => $this->checkForURLs($status['text']),
						"Time" => $status['created_at'],
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
			$words = explode(" ",$text);
			$html = "";
			
			foreach($words as $word){
				if(preg_match("/^http/", $word)){
					$htmlword = "<a href='$word' target='_blank'>$word</a>";
				}
				else if(strcmp("$word", "&") == 0)
					$htmlword = "&amp;";
				else
					$htmlword = $word;
				$html .= "$htmlword ";
			}
			return $html;
		}
		else
			return $text;
	 }

}

?>
