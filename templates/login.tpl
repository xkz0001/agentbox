<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
<style type="text/css">
.tLabel {
	text-align: right;
}
</style>
</head>

<body>
<div><font color='red'><{$errorInfo}></font>
		<form action="index.php?module=User&method=auth" method="post">
		<table width="500" border="0" align="center">
				<tr>
						<td class="tLabel">Username:</td>
						<td><label for="username"></label>
						<input type="text" name="username" id="username" /></td>
				</tr>
				<tr>
						<td class="tLabel">Password:</td>
						<td><label for="pwd"></label>
						<input name="pwd" id="pwd" type="password"/></td>
				</tr>
				<tr>
						<td colspan="2" align="center"><input type="submit" name="submit" id="submit" value="Submit" /></td>
				</tr>
		</table>
		</form>
</div>
</body>
</html>
