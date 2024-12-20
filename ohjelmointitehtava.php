<?php
// Aloitetaan sessio
session_start();

// Muodostetaan yhteys tietokantaan
$y_tiedot = "dbname=fglany user=fglany password=tX6GquharwHfsPh";
$yhteys = pg_connect($y_tiedot);

// Jos yhteyden muodostaminen ei onnistu, tulostetaan virheilmoitus
if (!$yhteys) {
    die("Tietokantayhteyden luominen epäonnistui.");
}

// Tarkistetaan, onko lomake lähetetty
if (isset($_POST['siirto'])) {
    // Tallennetaan lomakkeen tiedot sessiomuuttujiin
    $_SESSION['summa'] = $_POST['summa'];
    $_SESSION['lahettaja_tili'] = $_POST['lahettaja_tili'];
    $_SESSION['vastaanottaja_tili'] = $_POST['vastaanottaja_tili'];
    
    // Aloitetaan transaktio
    pg_query($yhteys, "BEGIN") or die('Ei onnistuttu aloittamaan tapahtumaa: ' . pg_last_error());

    // Veloitetaan summa lähettäjän tililtä, jos saldo riittää
    $lahettaja_tili = pg_escape_string($_SESSION['lahettaja_tili']);
    $vastaanottaja_tili = pg_escape_string($_SESSION['vastaanottaja_tili']);
    $summa = floatval($_SESSION['summa']);

    $veloita_query = "UPDATE tilit SET saldo = saldo - $summa 
                      WHERE tilinumero = '$lahettaja_tili' AND saldo >= $summa";
    $veloita = pg_query($yhteys, $veloita_query) or die('Virhe ensimmäisessä päivityksessä: ' . pg_last_error());

    // Tarkistetaan, että veloitus onnistui
    if (pg_affected_rows($veloita) != 1) {
        pg_query($yhteys, "ROLLBACK") or die('Ei onnistuttu perumaan tapahtumaa: ' . pg_last_error());
        echo "Tilisiirto epäonnistui: lähettäjän tilinumero on virheellinen tai saldo ei riitä.";
        exit();
    }

    // Lisätään summa vastaanottajan tilille
    $lisays_query = "UPDATE tilit SET saldo = saldo + $summa WHERE tilinumero = '$vastaanottaja_tili'";
    $lisays = pg_query($yhteys, $lisays_query) or die('Virhe toisessa päivityksessä: ' . pg_last_error());

    // Tarkistetaan, että lisäys onnistui
    if (pg_affected_rows($lisays) != 1) {
        pg_query($yhteys, "ROLLBACK") or die('Ei onnistuttu perumaan tapahtumaa: ' . pg_last_error());
        echo "Tilisiirto epäonnistui: vastaanottajan tilinumero on virheellinen.";
        exit();
    }

    // Jos molemmat päivitykset onnistuivat, sitoudutaan tapahtumaan
    pg_query($yhteys, "COMMIT") or die('Ei onnistuttu hyväksymään tapahtumaa: ' . pg_last_error());

    // Hae tilin omistajien nimet tietokannasta
    $lahettaja_nimi_query = "SELECT omistaja FROM tilit WHERE tilinumero = '$lahettaja_tili'";
    $vastaanottaja_nimi_query = "SELECT omistaja FROM tilit WHERE tilinumero = '$vastaanottaja_tili'";

    $lahettaja_nimi = pg_fetch_result(pg_query($yhteys, $lahettaja_nimi_query), 0, 'omistaja');
    $vastaanottaja_nimi = pg_fetch_result(pg_query($yhteys, $vastaanottaja_nimi_query), 0, 'omistaja');

    // Tallennetaan nimet sessioon
    $_SESSION['lahettaja_nimi'] = $lahettaja_nimi;
    $_SESSION['vastaanottaja_nimi'] = $vastaanottaja_nimi;

    // Ohjataan vahvistussivulle
    header('Location: vahvistus.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Tilisiirto</title>
</head>
<body>
    <h2>Tee tilisiirto</h2>
    <form method="post" action="">
        <table>
            <tr><td>Siirrettävä summa: </td><td><input type="number" name="summa" step="0.01" required></td></tr>
            <tr><td>Veloitettava tilinumero: </td><td><input type="text" name="lahettaja_tili" required></td></tr>
            <tr><td>Vastaanottajan tilinumero: </td><td><input type="text" name="vastaanottaja_tili" required></td></tr>
            <tr><td></td><td><input type="submit" name="siirto" value="Siirrä"></td></tr>
        </table>
    </form>
</body>
</html>

