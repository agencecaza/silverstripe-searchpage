<% if Keywords %>
	$SearchForm
<% end_if %>

<% if not Keywords %>

  <p><%t SearchPage.YOUSEARCHED 'You searched' %>: '$KeywordsGet'</p>

  <a class="searchlink" href="$Link"><%t SearchPage.NEWSEARCH 'New search' %></a>
  <% if Results %>
    <div class="results">
      <% loop Results %>
        <p><a href="$Link"><span class="pos">$Pos</span>$Title <span class="rank">$Rank</span></a></p>
      <% end_loop %>
    </div>
  <% end_if %>
  
  <% if $Results.MoreThanOnePage %>
  	<div id="pagination">
        <% if $Results.NotFirstPage %>
          <a href="$Results.PrevLink"> &#60; </a>
        <% end_if %>
          <% loop $Results.Pages %>
          	<a href="$Link" <% if $CurrentBool %>class="current"<% end_if %>>$PageNum</a>
          <% end_loop %>
        <% if $Results.NotLastPage %>
            <a href="$Results.NextLink"> &#62; </a>
        <% end_if %>
    </div>
  <% end_if %>

<% end_if %>
