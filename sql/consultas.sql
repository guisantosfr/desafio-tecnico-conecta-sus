-- 1. Listar pacientes com o nome da respectiva unidade de saúde.
SELECT
    p.name AS patient_name,
    hu.name AS health_unit_name
FROM patients AS p
INNER JOIN health_units AS hu
    ON hu.id = p.health_unit_id
ORDER BY p.name;

-- 2. Contar pacientes por unidade, incluindo unidades sem pacientes.
SELECT
    hu.id,
    hu.name AS health_unit_name,
    COUNT(p.id) AS total_patients
FROM health_units AS hu
LEFT JOIN patients AS p
    ON p.health_unit_id = hu.id
GROUP BY hu.id, hu.name
ORDER BY hu.name;

-- 3. Listar pacientes cujo nome contenha o termo "Maria".
SELECT
    id,
    name,
    cpf,
    birth_date,
    phone,
    health_unit_id
FROM patients
WHERE name LIKE '%Maria%'
ORDER BY name;
