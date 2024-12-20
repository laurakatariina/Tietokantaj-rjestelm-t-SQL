SELECT CONCAT(etunimi,' ',sukunimi) AS nimi,
ROUND( 
CASE 
WHEN osastonro = 4 THEN palkka * 1.20 ELSE palkka
END, 2
) AS uusipalkka
FROM tyontekija
ORDER BY uusipalkka, nimi;