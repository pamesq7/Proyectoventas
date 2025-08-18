-- Hacer los campos idEmpleado e idDiseñador nullable en la tabla disenos
ALTER TABLE disenos MODIFY COLUMN idEmpleado BIGINT UNSIGNED NULL;
ALTER TABLE disenos MODIFY COLUMN idDiseñador BIGINT UNSIGNED NULL;
