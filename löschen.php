<!DOCTYPE html>

<html>
    <head>
        <title>Account löschen</title>
    </head>

    <body>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty(htmlspecialchars(stripslashes(trim($_POST["name"])))) && !empty(htmlspecialchars(stripslashes(trim($_POST["passwort"]))))) {
                    $servername = "localhost";
                    $nutzername = "root";
                    $pw = "";
                    $dbname = "sensitivedaten";

                    $conn = new mysqli($servername, $nutzername, $pw, $dbname);

                    if ($conn -> connect_error) {
                        echo "<script>window.alert('Bei der Verbindung zum Server ist ein Fehler aufgetreten')</script>";
                    }
                    else {
                        $sql = $conn -> prepare("SELECT * FROM User WHERE Name=?");

                        $sql -> bind_param("s", $_POST["name"]);
                        $sql -> execute();

                        $sql -> bind_result($res_id, $res_name, $res_pass);

                        while ($sql -> fetch()) {
                            if (password_verify($_POST["passwort"], $res_pass)) {
                                $id = $res_id;
                                break;
                            }
                        }

                        $sql -> close();

                        $sql = $conn -> prepare("DELETE FROM User WHERE ID=?");

                        $sql -> bind_param("i", $id);
                        $sql -> execute();

                        $sql -> close();

                        $sql = $conn -> prepare("SELECT Name FROM Bilder WHERE UserID=?");

                        $sql -> bind_param("i", $id);
                        $sql -> execute();

                        $sql -> bind_result($res_name);

                        while ($sql -> fetch()) {
                            unlink($res_name);
                        }

                        rmdir("uploads/" . htmlspecialchars(stripslashes(trim($_POST["name"]))) . "/");

                        $sql = $conn -> prepare("DELETE FROM Bilder WHERE UserID=?");

                        $sql -> bind_param("i", $id);
                        $sql -> execute();

                        $sql -> close();

                        $sql = $conn -> prepare("SELECT * FROM User WHERE ID=?");

                        $sql -> bind_param("i", $id);
                        $sql -> execute();

                        $error = 0;

                        if ($sql -> fetch()) {
                            $error = "Bei dem Löschen des Accounts ist ein Fehler aufgetreten";
                        }

                        $sql -> close();
                        $conn -> close();

                        if ($error === 0) {
                            echo "<script>window.alert('Du hast deinen Account erfolgreich gelöscht')</script>";
                        }
                        else {
                            echo "<script>window.alert('" . $error . "')</script>";
                        }
                    }
                }
            }
        ?>

        <form action="löschen.php" method="POST">
            Name: <input type="text" name="name" autocomplete="off"><br>
            Passwort: <input type="password" name="passwort" autocomplete="off"><br>
            <input type="submit">
        </form>
    </body>
</html>