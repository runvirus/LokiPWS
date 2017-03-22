<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Auth</title>
    <style>html,body,div,form{display:block;font-family:sans-serif}*{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}#login_{width:220px;height:155px;position:absolute;left:50%;top:30%;margin-left:-110px;margin-top:-75px}#login_ input[type="text"],#login_ input[type="password"]{width:100%;height:40px;positon:relative;margin-top:7px;font-size:14px;color:#444;outline:none;border:1px solid rgba(0,0,0,.49);padding-left:20px;-webkit-background-clip:padding-box;-moz-background-clip:padding-box;background-clip:padding-box;border-radius:6px}#login_ input[type="submit"]{width:100%;height:35px;margin-top:7px;color:#fff;font-size:18px;font-weight:700;text-shadow:0 -1px 0 #5b6ddc;outline:none;border:1px solid rgba(0,0,0,.49);-webkit-background-clip:padding-box;-moz-background-clip:padding-box;background-clip:padding-box;border-radius:6px;background-color:#5466da}</style>
</head>

<body>
	<?php if(strlen($LoginMSG)) echo "<center><h5>".$LoginMSG."</h5></center>"; ?>
<?php if(!AUTH_URL) { ?>
    <form id="login_" method="POST" action="<?php echo $ScriptURL; ?>">
<?php } else { ?>
	<form id="login_" method="POST" action="<?php echo $ScriptURL . "?" . AUTHVALUE_ . "=" . $AuthUrl;?>">
<?php } ?>
        <input type="text" name="iU" value="" placeholder="Username">
        <input type="password" name="iP" value="" placeholder="Password">
<?php if(CAPTCHA) { ?>
					
		<img class="img-thumbnail" width="160" height="75" style="border: solid 1px #ccc; margin: 1em 2em; " src="data:image/png;base64, <?php print $Captcha['image'];?>" alt="Image" />
		<input type="text" name="iC"  value="" placeholder="Captcha">
		<input type="text" style="display: none;" value="<?php echo $Captcha['encrypted'];?>" name="iE">
<?php } ?>
        <input type="submit" value="<?php GetText_( 'login_button'); ?>">
    </form>
</body>
</html>
