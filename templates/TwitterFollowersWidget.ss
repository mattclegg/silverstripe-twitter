<ul>
	<% if Followers %>
	       	<% if ShowAvatar %>
		    <% control Followers %>
		        <li>
				<div class="widgetThumb">		            
					<a href="$URL" target="_blank" title="See $Name on the internet"><img src="$Avatar" alt="$Name" /></a>
				</div>
				<div>
					<a href="$URL" target="_blank" title="See $Name on the internet">$Name</a><br />
					<b>From:</b> $Location
				</div>
				<div style="clear:both; font-size:0px; line-height:0px; height:0px;"></div>
		        </li>
		    <% end_control %>
		<% else %>
		    <% control Friends %>
		        <li>
		        	<a href="$URL" target="_blank" title="See $Name on the internet">$Name</a>,&nbsp;
		        </li>
		    <% end_control %>
		<% end_if %>
	<% else %>
		<li>Sorry I couldnt find any friends of you.</li>
	<% end_if %>
</ul>
