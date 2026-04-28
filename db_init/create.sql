-- Aquest script NOMÉS s'executa la primera vegada que es crea el contenidor.
-- Si es vol recrear les taules de nou cal esborrar el contenidor, o bé les dades del contenidor
-- és a dir, 
-- esborrar el contingut de la carpeta db_data 
-- o canviant el nom de la carpeta, però atenció a no pujar-la a git


-- És un exemple d'script per crear una base de dades i una taula
-- i afegir-hi dades inicials

-- Si creem la BBDD aquí podem control·lar la codificació i el collation
-- en canvi en el docker-compose no podem especificar el collation ni la codificació

-- Per assegurar-nes de que la codificació dels caràcters d'aquest script és la correcta
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS incidencies
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Donem permisos a l'usuari 'usuari' per accedir a la base de dades 'persones'
-- sinó, aquest usuari no podrà veure la base de dades i no podrà accedir a les taules
GRANT ALL PRIVILEGES ON incidencies.* TO 'usuari'@'%';
FLUSH PRIVILEGES;


-- Després de crear la base de dades, cal seleccionar-la per treballar-hi
USE incidencies;


CREATE TABLE tipo (
    id_tipo INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100)
);

CREATE TABLE departamento (
    id_departamento INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200)
);

CREATE TABLE tecnico (
    id_tecnico INT(11) AUTO_INCREMENT PRIMARY KEY, 
    nom VARCHAR(200)
);

CREATE TABLE incidencia (
    id_incidencia INT(11) AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(2000),
    data TIMESTAMP,
    departamento INT(11),
    tecnico INT(11),
    tipo INT(11),
    data_finalizacion SYSDATE,
    prioritat ENUM('Alta', 'Media', 'Baja'),
    FOREIGN KEY (tecnico) REFERENCES tecnico(id_tecnico),
    FOREIGN KEY (tipo) REFERENCES tipo(id_tipo),
    FOREIGN KEY (departamento) REFERENCES departamento(id_departamento)
);

CREATE TABLE actuacio (
    id_actuacio INT(11) AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(2000),
    data TIMESTAMP,
    tiempo INT(11),
    incidencia INT(11),
    visible INT(1),
    FOREIGN KEY (incidencia) REFERENCES incidencia(id_incidencia)
);

INSERT INTO `tecnico` (`id_tecnico`, `nom`) VALUES
(1, 'Juan'),
(2, 'Ermengol'),
(3, 'Alvaro'),
(4, 'Gerard');