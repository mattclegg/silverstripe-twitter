<ul>
	<% if Status %>
	       	<% if ShowAvatar %>
		    <% control Status %>
		        <li>
				<div class="widgetThumb">		            
					<a href="$URL" target="_blank" title="See $Name on the internet"><img src="$Avatar" alt="$Name" /></a>
				</div>
				<div>
					<p>$Text</p>
				</div>
				<div style="clear:both; font-size:0px; line-height:0px; height:0px;"></div>
		        </li>
		    <% end_control %>
		<% else %>
		    <% control Status %>
		        <li>
		        	<a href="$URL" target="_blank" title="See $Name on the internet">$Name</a>:&nbsp;
				$Text
		        </li>
		    <% end_control %>
		<% end_if %>
	<% else %>
		<li>Sorry I couldnt find any friends of you.</li>
	<% end_if %>
</ul>
