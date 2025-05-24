CREATE DATABASE IF NOT EXISTS billar;
USE billar;

CREATE TABLE IF NOT EXISTS mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_mesa INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio DATETIME,
    hora_fin DATETIME,
    minutos INT,
    total INT
);
