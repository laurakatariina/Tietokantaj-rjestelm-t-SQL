SELECT viini.nimi, viinitila.nimi AS tila
FROM viini
INNER JOIN viinitila ON viini.vttunnus = viinitila.vttunnus
ORDER BY viini.nimi ASC;
