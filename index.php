<!DOCTYPE html>

<html>
    <head>
        <title>Anmeldung</title>
        <link rel="stylesheet" type="text/css" href="index.css">
    </head>

    <body>
        <form action="ziel.php" method="POST">
            Username: <input type="text" name="username" autocomplete="off"><br>
            Passwort: <input type="password" name="passwort" autocomplete="off"><br>

            <div id="div1">
                <input type="submit" value="Einloggen" name="submit"/>
                <a href="registrieren.php" id="link">registrieren</a>
            </div>
        </form>
    </body>
</html> 