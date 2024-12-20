--Laura Nyrhilä
CREATE OR REPLACE FUNCTION painotetun_keskiarvo_lasku(opiskelija_id INT)
RETURNS NUMERIC AS $$
DECLARE
    painotettu_summa NUMERIC := 0;
    opintopisteiden_summa NUMERIC := 0;
    rivi RECORD;
BEGIN
    FOR rivi IN
        SELECT s.kurssi_nro, k.laajuus, s.arvosana
        FROM suoritus s
        JOIN kurssi k ON s.kurssi_nro = k.kurssi_nro
        WHERE s.op_nro = opiskelija_id
    LOOP
        painotettu_summa := painotettu_summa + (rivi.laajuus * rivi.arvosana);
        opintopisteiden_summa := opintopisteiden_summa + rivi.laajuus;
    END LOOP;

    IF opintopisteiden_summa = 0 THEN
        RETURN NULL;
    END IF;

    RETURN painotettu_summa / opintopisteiden_summa;
END;
$$ LANGUAGE plpgsql;
