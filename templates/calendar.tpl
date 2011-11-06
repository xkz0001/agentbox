<!DOCTYPE html   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">  <head>    <title>Calendar</title>    <link rel="stylesheet" href="/agentbox/style/structure.css" type="text/css" media="screen">    <script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>      </head>  <body>    <h2>Calendar Events      <font size='2'>    <a href='index.php?module=contact&method=display'>Contacts</a>    <a href='index.php?module=abtask&method=display'>Tasks</a>    <a href='index.php?module=documentlibrary&method=df'>Document Library</a>    <a href='index.php?module=user&method=logout'>logout</a>    </font>    </h2>    <div>    <{$results_count}>    </div>    <div>    <{$google_sync}>    </div>        <{foreach $results as $r}>    <div class="entry <{if (!empty($r['googleID']))}> google <{/if}>">      <div class="name"><{$r['title']}></div>      <div class="data">        <table>          <tr>            <td class="header">Description:</td>            <td><{$r['description']}></td>          </tr>					<tr>            <td class="header">Where:</td>            <td><{$r['location']}></td>          </tr>	  <tr>            <td class="header">Who:</td>            <td><{$r['guest']}></td>          </tr>          <tr>            <td class="header">Start:</td>            <td><{$r['startTime']}></td>          </tr>          <tr>            <td class="header">End:</td>            <td><{$r['endTime']}></td>          </tr>          <tr>            <td class="header">googleID:</td>            <td><{get_google_id($r['googleID'])}></td>          </tr>        </table>      </div>    </div>    <{/foreach}>  </body></html>