<% if Keywords %>
	$SearchForm
<% end_if %>

<% if not Keywords %>

  <p><%t SearchPage.YOUSEARCHED 'You searched' %>: '$KeywordsGet'</p>

  <a href="$Link"><%t SearchPage.NEWSEARCH 'New search' %></a>

  <% loop Results %>
    <p><a href="$Link">$Pos – $Title ($Rank)</a></p>
  <% end_loop %>

  <% if $Results.MoreThanOnePage %>
  	<div class="pagination">
			<ul>
        <% if $Results.NotFirstPage %>
          <li><a href="$Results.PrevLink"> &#60; </a></li>
        <% end_if %>
          <% loop $Results.Pages %>
          	<li <% if $CurrentBool %>class="active"<% end_if %>><a href="$Link">$PageNum</a></li>
          <% end_loop %>
        <% if $Results.NotLastPage %>
            <li><a href="$Results.NextLink"> &#62; </a></li>
        <% end_if %>
			</ul>
    </div>
  <% end_if %>

<% end_if %>
