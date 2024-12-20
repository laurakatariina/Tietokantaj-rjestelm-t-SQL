CREATE OR REPLACE FUNCTION log_tili_muutos()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO KIRJAUS (tili, paiva, klo)
    VALUES (
        NEW.tili,       
        CURRENT_DATE,   
        CURRENT_TIME    
    );
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

--Laura Nyrhilä 50162888