<html lang="en-US">
<head>

	<meta charset="utf-8">

	<title>	Sistema de Sensores </title>
	<link rel="stylesheet" href="login-style.css">
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<script type="text/javascript">
	function validateForm()
	{
		var us = document.forms["login"]["user"].value;
		var ps = document.forms["login"]["password"].value;
		if(us == null || us == "usuario" || ps == null || ps == "password")
		{
			alert("Alguno de los campos está vacío.");
			return false;
		}
		else if(us == "root" && ps == "1234")
		{
			alert("Bienvenido!");
		}
		else
		{
			alert("Usuario o contraseña incorrectos. Intente de nuevo.");
			return false;
		}
	}
	</script>

</head>

<body>

    <div id="login-form">
        <h3>Sistema de Sensores</h3>
        <fieldset>
            <form name="login" action="home.php" onsubmit="return validateForm()" method="post">
                <input type="user" name="user" required value="usuario" onBlur="if(this.value=='')this.value='usuario'" onFocus="if(this.value=='usuario')this.value='' "> <!-- JS because of IE support; better: placeholder="user" -->
                <input type="password" name="password" required value="password" onBlur="if(this.value=='')this.value='password'" onFocus="if(this.value=='password')this.value='' "> <!-- JS because of IE support; better: placeholder="Password" -->
                <input type="submit" value="Ingresar">

                <footer class="clearfix">
                    <p><span class="info">:)</span><a href="#">Bienvenido</a></p>
                </footer>
            </form>
        </fieldset>
    </div> <!-- end login-form -->

</body>
</html>
