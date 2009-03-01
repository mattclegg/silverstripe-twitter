<div class="typography">
	
	<% if Menu(2) %>
		<% include SideBar %>
		<div id="Content">
	<% end_if %>
	
			
        <% if Level(2) %>
            <% include BreadCrumbs %>
        <% end_if %>
    
        <div id="TwitterTitle">
            <h2>$Title</h2>
            <a href="<% if TwitterURL %> $TwitterURL <% else %> http://twitter.com <% end_if %>" target="_blank">
                <img src="twitter/images/twitters.png" />
            </a>
            <p class="clearboth"></p>
        </div>
        $Content
        
        <% if ShowFriendsTimeLine %>
            <% if FriendsTimeLine %>
                <h4>Friends Updates</h4>
                <ul class="TwitterTimeLine">
                    <% control FriendsTimeLine %>
                        <% include Status %>       
                    <% end_control %>
                </ul>
            <% else %>
                <h4>No recent statuses found</h4>
            <% end_if %>
        <% end_if %>
        
        <% if ShowPublicTimeLine %>
            <% if PublicTimeLine %>
                <h4>Public Updates</h4>
                <ul class="TwitterTimeLine">
                    <% control PublicTimeLine %>
                        <% include Status %>   
                    <% end_control %>
                </ul>
            <% else %>
                <h4>No recent statuses found</h4>
            <% end_if %>
        <% end_if %>
        
        <% if ShowUserTimeLine %>
            <% if UserTimeLine %>
                <h4>User Updates</h4>
                <ul class="TwitterTimeLine">
                    <% control UserTimeLine %>
                        <% include Status %>      
                    <% end_control %>
                </ul>
            <% else %>
                <h4>No recent statuses found</h4>
            <% end_if %>
        <% end_if %>
        
        <% if Followers %>
            <h4>My Followers</h4>
            <ul class="Followers">
            	<% if ShowFollowersAvatar %>
                    <% control Followers %>
                        <li>
                        	<a href="$URL" target="_blank" title="See $Name on the internet"><img src="$Avatar" alt="$Name" /></a>
                        </li>
                    <% end_control %>
                <% else %>
                    <% control Followers %>
                        <li>
                        	<a href="$URL" target="_blank" title="See $Name on the internet">$Name</a>,&nbsp;
                        </li>
                    <% end_control %>

                <% end_if %>
            </ul>
            <div class="clear"></div>
        <% else %>
            <h4>0 Followers</h4>
        <% end_if %>
        
        <% if Friends %>
            <h4>My Friends</h4>
            <ul class="Followers">
            
               	<% if ShowFriendsAvatar %>
                    <% control Friends %>
                        <li>
                            <a href="$URL" target="_blank" title="See $Name on the internet"><img src="$Avatar" alt="$Name" /></a>
                        </li>
                    <% end_control %>
                <% else %>
                    <% control Friends %>
                        <li>
                            <a href="$URL" target="_blank" title="See $Name on the internet">$Name</a>,&nbsp;
                        </li>
                    <% end_control %>
                <% end_if %>
            </ul>
            <div class="clear"></div>
        <% else %>
            <h4>0 Friends</h4>
        <% end_if %>

    
	<% if Menu(2) %>
		</div>
	<% end_if %>
	
</div>
