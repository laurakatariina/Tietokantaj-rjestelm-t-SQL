SELECT 
    e1.id AS elokuva_id,
    e1.edeltava_id AS edeltava_id,
    1 AS taso
FROM 
    elokuva e1
WHERE 
    e1.edeltava_id IS NULL

UNION ALL

SELECT 
    e2.id AS elokuva_id,
    e2.edeltava_id AS edeltava_id,
    1 AS taso
FROM 
    elokuva e2
WHERE 
    e2.edeltava_id IS NOT NULL

UNION ALL

SELECT 
    e3.id AS elokuva_id,
    e2.edeltava_id AS edeltava_id,
    2 AS taso
FROM 
    elokuva e3
JOIN 
    elokuva e2 ON e3.edeltava_id = e2.id
WHERE 
    e2.edeltava_id IS NOT NULL

UNION ALL

SELECT 
    e4.id AS elokuva_id,
    e3.edeltava_id AS edeltava_id,
    3 AS taso
FROM 
    elokuva e4
JOIN 
    elokuva e3 ON e4.edeltava_id = e3.id
JOIN 
    elokuva e2 ON e3.edeltava_id = e2.id
WHERE 
    e2.edeltava_id IS NOT NULL

ORDER BY 
    elokuva_id, taso;

