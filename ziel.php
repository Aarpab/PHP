<!DOCTYPE html>

<html>
    <head>
        <title>Bilder</title>
        <link rel="stylesheet" type="text/css" href="ziel.css">
    </head>

    <body>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty(htmlspecialchars(stripslashes(trim($_POST["username"])))) && !empty(htmlspecialchars(stripslashes(trim($_POST["passwort"]))))) {
                    if (empty($_POST["datei"])) {
                        $servername = "localhost";
                        $username = "root";
                        $pw = "";
                        $dbname = "sensitivedaten";

                        $conn = new mysqli($servername, $username, $pw, $dbname);

                        if ($conn -> connect_error) {
                            echo "Bei der Verbindung zum Server ist ein Fehler aufgetreten";
                        }

                        $sql = $conn -> prepare("SELECT * FROM User WHERE Name=?");

                        $sql -> bind_param("s", $_POST["username"]);
                        $sql -> execute();

                        $sql -> bind_result($res_id, $res_name, $res_pass);

                        $richtig = false;

                        while ($sql -> fetch()) {
                            if (password_verify($_POST["passwort"], $res_pass)) {
                                $richtig = true;
                                break;
                            }
                        }

                        if (!$richtig) {
                            header("Location: index.php");
                        }

                        $sql -> close();
                    }
                }
                else {
                    header("Location: index.php");
                }
            }

            if (isset($_POST["hochgeladen"])) {
                $ziel = "uploads/" . htmlspecialchars(stripslashes(trim($_POST["username"]))) . "/";
                $zieldatei = $ziel . basename($_FILES["datei"]["name"]);
                $error = 0;

                $imagesize = getimagesize($_FILES["datei"]["tmp_name"]);
                if ($imagesize === false) {
                    $error = "Du kannst nur Bilder hochladen";
                }

                $endung = pathinfo($zieldatei, PATHINFO_EXTENSION);
                if ($endung != "jpg" && $endung != "jpeg" && $endung != "png" && $endung != "bmp") {
                    $error = "Das Format deiner Datei wird nicht unterstützt";
                }

                if (file_exists($zieldatei)) {
                    $error = "Du kannst jedes Bild nur ein Mal hochladen";
                }

                if ($_FILES["datei"]["size"] > 2*1000*1000) {
                    $error = "Der Upload ist zu groß";
                }

                if ($error === 0) {
                    if (!move_uploaded_file($_FILES["datei"]["tmp_name"], $zieldatei)) {
                        echo "<script>window.alert('Bei dem Hochladen des Bildes ist ein Fehler aufgetreten')</script>";
                    }
                    else {
                        $servername = "localhost";
                        $nutzername = "root";
                        $pw = "";
                        $dbname = "sensitivedaten";

                        $conn = new mysqli($servername, $nutzername, $pw, $dbname);

                        if ($conn -> connect_error) {
                            echo "<script>window.alert('Bei der Verbinding zum Server ist ein Fehler aufgetreten')</script>";
                        }
                        else {
                            $sql = $conn -> prepare("SELECT ID FROM User WHERE Name=?");

                            $sql -> bind_param("s", $_POST["username"]);
                            $sql -> execute();

                            $sql -> bind_result($res_id);

                            if ($sql -> fetch()) {
                                $sql -> close();

                                $sql = $conn -> prepare("INSERT INTO Bilder VALUES(?, ?)");

                                $sql -> bind_param("is", $res_id, $zieldatei);
                                $sql -> execute();

                                $conn -> close();
                            }
                        }
                    }
                }
                else {
                    echo "<script>window.alert('" . $error . "')</script>";
                }
            }
        ?>

        <?php
            echo "<h1>" . htmlspecialchars(stripslashes(trim($_POST["username"]))) . "</h1>";
        ?>

        <form action="ziel.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars(stripslashes(trim($_POST["username"])));?>">
            <input type="hidden" name="passwort" value="<?php echo htmlspecialchars(stripslashes(trim($_POST["passwort"])));?>">
            <input type="hidden" name="hochgeladen" value="">

            Bild: <input type="file" name="datei"><br>
            <input type="submit" value="hochladen" name="submit" id="hochladen">
        </form><br>

        <a id="löschen" href="löschen.php">Account löschen</a>

        <?php
            $servername = "localhost";
            $nutzername = "root";
            $pw = "";
            $dbname = "sensitivedaten";

            $conn = new mysqli($servername, $nutzername, $pw, $dbname);

            if ($conn -> connect_error) {
                echo "<p>Bei der Verbindung zum Server ist ein Fehler aufgetreten</p>";
            }
            else {
                $sql = $conn -> prepare("SELECT ID FROM User WHERE Name=?");

                $sql -> bind_param("s", $_POST["username"]);
                $sql -> execute();

                $sql -> bind_result($res_id);

                if ($sql -> fetch()) {
                    $id = $res_id;
                }

                $sql -> close();

                $sql2 = $conn -> prepare("SELECT Name FROM Bilder WHERE UserID=?");

                $sql2 -> bind_param("s", $id);
                $sql2 -> execute();

                $sql2 -> bind_result($res_name);
                
                while ($sql2 -> fetch()) {
                    echo "<img src='" . $res_name . "'></img><br>";
                }

                $conn -> close();
            }
        ?>
    </body>
</html>