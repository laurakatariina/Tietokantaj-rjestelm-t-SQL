CREATE VIEW melkein_universaali_relaatio(vtnimi, vnimi, rnimi)
AS SELECT viinitila.nimi, viini.nimi, rypalelajike.nimi
FROM viinitila
FULL JOIN viini ON viinitila.vttunnus = viini.vttunnus
FULL JOIN sisaltaa ON viini.vtunnus = sisaltaa.vtunnus
FULL JOIN rypalelajike ON sisaltaa.rtunnus = rypalelajike.rtunnus
ORDER BY viinitila.nimi ASC, viini.nimi ASC, rypalelajike.nimi ASC;
---näkymä
SELECT * FROM melkein_universaali_relaatio;
 -- kysely