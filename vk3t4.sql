WITH RECURSIVE ee(id, edeltava_id, taso)
AS (SELECT e.id, e.edeltava_id, 1 
FROM elokuva e
UNION 
SELECT e.id, ee.edeltava_id, ee.taso +1
FROM elokuva e, ee
WHERE e.edeltava_id = ee.id
)
SELECT * FROM ee
ORDER BY ee.id ASC, ee.taso ASC;

