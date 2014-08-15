<% if $IncludeFormTag %>
<form $getAttributesHTML("class") class="navbar-form navbar-right $extraClass" role="login">
<% end_if %>
    <% if $Fields %>
    <% loop $Fields %>
        <div class="form-group">
            $Field
        </div>
    <% end_loop %>
    <% end_if %>
    
    <% if $Actions %>
    <% loop $Actions %>
        $Field
    <% end_loop %>
    <% end_if %>
<% if $IncludeFormTag %>
</form>
<% end_if %>