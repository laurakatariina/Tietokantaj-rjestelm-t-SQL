<?php 
?> 
 
<!DOCTYPE html> 
 
<?php 
 
 // aloitetaan sessio 
    session_start(); 
 echo "Liity jäseneksi anargistien ammattiyhdistykseen! <br /><br />"; 
  
 // muodostetaan yhteys tietokantaan 
 $y_tiedot = "dbname=fglany user=fglany password=tX6GquharwHfsPh"; 
 $yhteys = pg_connect($y_tiedot); 
 // jos yhteyden muodostaminen ei onnistu, tulostetaan virheilmoitus 
 if (!$yhteys) { 
    die("Tietokantayhteyden luominen epäonnistui."); 
 } 

// Read input and create session variables if the form is submitted
if (isset($_POST['jasen2'])) {
    $_SESSION['nimi'] = $_POST['nimi'];

    // Begin a transaction
    pg_query($yhteys, "BEGIN");

    // Lock the entire 'jasenyys' table in EXCLUSIVE mode to prevent concurrent writes
    $lock_query = "LOCK TABLE jasenyys IN EXCLUSIVE MODE";
    pg_query($yhteys, $lock_query);

    // Fetch the highest existing member number (nro) and increment by 1
    $suurin_numero_query = "SELECT COALESCE(MAX(nro), 0) FROM jasenyys";
    $suurin_numero = pg_query($yhteys, $suurin_numero_query);
    $next_number = (pg_fetch_result($suurin_numero, 0, 0) + 1);
    $_SESSION['jasennro'] = $next_number;

    // Insert the new member with the incremented member number
    $nimi = pg_escape_string($_SESSION['nimi']);
    $jasennro = $_SESSION['jasennro'];
    $kysely = "INSERT INTO jasenyys(nimi, nro) VALUES ('$nimi', '$jasennro')";

    $paivitys = pg_query($yhteys, $kysely);

    // Check if the insertion was successful
    if ($paivitys) {
        // Commit the transaction
        pg_query($yhteys, "COMMIT");

        // Redirect to the confirmation page
        header('Location: tervetuloa.php');
    } else {
        // If there's an error, rollback the transaction
        pg_query($yhteys, "ROLLBACK");
        echo "Jäsennumeron lisääminen epäonnistui.";
    }

    // Close the database connection
    pg_close($yhteys);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="/style.css" rel="stylesheet" />
    <title>Liity jäseneksi anarkistien ammattiyhdistykseen!</title>
</head>
<body>
    <form method="post" action="jasen2.php">
        <table>
            <tr><td>Nimi: </td><td><input type="text" name="nimi" value=""/></td></tr>
            <tr><td></td><td><input type="submit" name="jasen2" value="Jatka"/></td></tr>
        </table>
    </form>
</body>
</html>
