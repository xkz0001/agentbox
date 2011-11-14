<!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Listing contacts</title>
    <link rel="stylesheet" href="style/structure.css" type="text/css" media="screen">
    <script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
  </head>
  <body>
    <h2>Tasks  
    <font size='2'>
    <a href='index.php?module=contact&method=display'>Contacts</a>
    <a href='index.php?module=calendar&method=display'>Calendar Events</a>  
    <a href='index.php?module=documentlibrary&method=df'>Document Library</a>
    <a href='index.php?module=user&method=logout'>logout</a>
    </font>
    </h2>
    <div>
    <{$link}>
    </div>
    <div>
    <{$results_count}>
    </div>
    <div>
    <{$google_sync}>
    </div> 
    <{foreach $results as $r}>   
		<div class="entry <{if ($r['is_deleted'])}> deleted <{/if}>">
			<div class="name"><{$r['title']}></div>
			<div class="data">
				<table>
					<tr>
						<td class="header">description:</td>
						<td><{$r['description']}></td>
					</tr>
					<tr>
						<td class="header">date</td>
						<td><{$r['date']}></td>
					</tr>
					<tr>
						<td class="header">status:</td>
						<td><{$r['status']}></td>
					</tr>
					<tr>
						<td class="header">googleID:</td>
						<td><{$r['googleID']}></td>
					</tr>
				</table>
			</div>
		</div>
		<{/foreach}>
  </body>
</html>