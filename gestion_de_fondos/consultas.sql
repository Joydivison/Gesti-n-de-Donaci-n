El porcentaje de ejecución de fondos para cada proyecto registrado. La ejecución se
calcula como el monto total ejecutado (gastado) dentro del total de fondos recibidos por
medio de donaciones. no se si necesitas mi base de datos necesito esa colsulta

SELECT 
    p.id AS id_proyecto,
    p.nombre AS nombre_proyecto,
    p.municipio,
    p.departamento,
    SUM(d.monto) AS monto_recibido,   
    SUM(doe.monto) AS monto_ejecutado,
    (SUM(doe.monto) / SUM(d.monto)) * 100 AS porcentaje_ejecucion
FROM 
    proyectos p
JOIN 
    donaciones d ON p.id = d.id_proyecto
JOIN 
    rubros r ON p.id = r.id_proyecto
JOIN 
    detalle_orden doe ON r.id_rubro = doe.id_rubro
GROUP BY 
    p.id, p.nombre, p.municipio, p.departamento
ORDER BY 
    porcentaje_ejecucion DESC 
LIMIT 0, 25;



La disponibilidad de fondos en cada rubro del proyecto “X”, de modo que se muestren
todos los rubros del proyecto (incluyendo los que pueden no tener ninguna donación
recibida o ninguna orden de compra emitida)


SELECT 
    r.id AS id_rubro,
    r.id_proyecto,
    ra.nombre AS nombre_rubro,
    COALESCE(SUM(d.monto), 0) AS monto_recibido,     
    COALESCE(SUM(doe.monto), 0) AS monto_ejecutado,   
    COALESCE(SUM(d.monto), 0) - COALESCE(SUM(doe.monto), 0) AS disponibilidad_fondos
FROM 
    rubros r
JOIN 
    rubros_aux ra ON r.id_rubro = ra.id   
LEFT JOIN 
    donaciones d ON r.id_rubro = d.id_rubro AND r.id_proyecto = d.id_proyecto   
LEFT JOIN 
    detalle_orden doe ON r.id_rubro = doe.id_rubro  
WHERE 
    r.id_proyecto = 'X'  
GROUP BY 
    r.id, r.id_proyecto, ra.nombre
ORDER BY 
    r.id_rubro 
LIMIT 0, 25;

