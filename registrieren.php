<!DOCTYPE html>

<html>
    <head>
        <title>Registrieren</title>
    </head>

    <body>
        <form action="registrieren.php" method="POST">
            Name: <input type="text" name="name" autocomplete="off"><br>
            Passwort: <input type="password" name="passwort1" autocomplete="off"><br>
            Passwort best&auml;tigen: <input type="password" name="passwort2" autocomplete="off"><br>

            <input type="submit" value="registrieren">
        </form>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty(htmlspecialchars(stripslashes(trim($_POST["name"])))) && !empty(htmlspecialchars(stripslashes(trim($_POST["passwort1"]) && !empty(htmlspecialchars(stripslashes(trim($_POST["passwort2"])))))))) {
                    if ($_POST["passwort1"] == $_POST["passwort2"]) {
                        $servername = "localhost";
                        $username = "root";
                        $pw = "";
                        $dbname = "sensitivedaten";

                        $conn = new mysqli($servername, $username, $pw, $dbname);

                        if ($conn -> connect_error) {
                            echo "<script>window.alert('Bei der Verbindung zum Server ist ein Fehler aufgetreten')</script>";
                        }
                        else {
                            $sql = $conn -> prepare("SELECT * FROM User WHERE Name=?");

                            $sql -> bind_param("s", $_POST["name"]);
                            $sql -> execute();

                            $vorhanden = false;

                            while ($sql -> fetch()) {
                                $vorhanden = true;
                            }

                            $sql -> close();

                            if ($vorhanden) {
                                echo "<script>window.alert('Der Name ist schon vorhanden')</script>";
                            }
                            else {
                                $sql = $conn -> prepare("INSERT INTO User(Name, Passwort) VALUES(?, ?)");

                                $hashed = password_hash($_POST["passwort1"], PASSWORD_DEFAULT);

                                $sql -> bind_param("ss", $_POST["name"], $hashed);
                                $sql -> execute();

                                $sql -> close();

                                $befehl = $conn -> prepare("SELECT * FROM User WHERE Name=? AND Passwort=?");

                                $befehl -> bind_param("ss", $_POST["name"], $hashed);
                                $befehl -> execute();

                                $registriert = false;

                                while ($befehl -> fetch()) {
                                    $registriert = true;
                                }

                                if ($registriert) {
                                    mkdir("uploads/" . htmlspecialchars(stripslashes(trim($_POST["name"]))));

                                    echo "<script>window.alert('Du hast dich erfolgreich registriert')</script>";
                                    echo "<a href='index.php'>zurück zur Anmeldung</a>";
                                }
                                else {
                                    echo "Bei der Registrierung ist ein Fehler aufgetreten";
                                }

                                $conn -> close();
                            }
                        }
                    }
                }
            }
        ?>
    </body>
</html> 