<?php
session_start();
echo "Liity jäseneksi anargistien ammattiyhdistykseen"
 // Aloitetaan sessio tietojen siirtoa varten

// Luodaan tietokantayhteys ja ilmoitetaan mahdollisesta virheestä
$y_tiedot = "dbname=fglany user=fglany password=tX6GquharwHfsPh";

if (!$yhteys = pg_connect($y_tiedot)) {
    die("Tietokantayhteyden luominen epäonnistui.");
}

// isset-funktiolla jäädään odottamaan lomakkeen lähetystä
if (isset($_POST['tallenna'])) {
    $_SESSION['nimi'] = $_POST['nimi'];

    // Haetaan suurin olemassa oleva jäsennumero

    $result = pg_query($yhteys, $hae_nro);

    if (!$result) {
        die("Kysely epäonnistui: " . pg_last_error($yhteys));
    }

    $rivi = pg_fetch_assoc($result);
    $suurin_nro = pg_query("SELECT COALESE MAX(nro), 0) FROM jasenyys");
	$_SESSION['jasennro'] = (pg_fetch_row($suurin_nro)[0] + 1);
	
	$nimi = pg_escape_string($yhteys, $_POST['nimi']);


    $kysely = "INSERT INTO jasenyys (nimi, nro) VALUES ('$nimi', $nro)";
    $paivitys = pg_query($kysely);
	
	// Suljetaan tietokantayhteys
	pg_close($yhteys);


            // Ohjataan käyttäjä tervetuloa-sivulle
            header("Location: tervetuloa.php");
            exit();
}

?>

<!DOCTYPE html>

Liity jäseneksi anargistien ammattiyhdistykseen! <br/><br/>
<html>
    <head>
        <meta charset="utf-8" />
        <link href="/style.css" rel="stylesheet" />
        <title>AAY</title>
    </head>
    <body>
        <form method="post" action="jasen1.php">
            <table>
                <tr><td>Nimi: </td><td><input type="text" name="nimi" value=""/></td></tr>

                <tr><td></td><td><input type="submit" name="jasen1" value="Jatka"/></td></tr>
            </table>
        </form>
    </body>
</html>


