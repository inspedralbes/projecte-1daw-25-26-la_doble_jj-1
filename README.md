# PROYECTO_FINAL — 1DAW

## Descripció

Aplicació web desenvolupada en PHP procedural amb MySQLi. Permet gestionar incidències informàtiques d'una empresa: registrar incidències, assignar-les a tècnics, registrar actuacions i consultar el seu estat, connectant-se a una base de dades MySQL en el servidor `dam.inspedralbes.cat`.

link: http://a25juaosomej.dam.inspedralbes.cat/Proyecto_Final/index.php

## Estructura del projecte
```
/projecte
  connexio.php           → Connexió a la base de dades
  index.php              → Pàgina d'inici, escull entre usuari o tècnic
  crear_incidencia.php   → Formulari per registrar una nova incidència
  incidencies.php        → Accés tècnic, mostra les incidències assignades
  detall_incidencia.php  → Consulta l'estat i actuacions d'una incidència
  afegir_actuacio.php    → Registra una nova actuació sobre una incidència

```

## Estructura de la base de dades

```sql
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
    data_finalizacion DATE,
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
```

## Camps dels formularis

### Registrar incidència
| Camp | Tipus | Descripció |
|---|---|---|
| departamento | select | Departament de l'usuari |
| descripcion | textarea | Descripció del problema |

### Registrar actuació
| Camp | Tipus | Descripció |
|---|---|---|
| descripcion | textarea | Descripció de l'actuació |
| tiempo | number | Temps invertit en minuts |
| visible | checkbox | Si l'usuari pot veure l'actuació |
| finalitzada | checkbox | Marca la incidència com a resolta |

## Tecnologies utilitzades
* PHP (procedural)
* MySQLi (consultes preparades)
* HTML5
* Servidor: dam.inspedralbes.cat
* JavaScript
* MongoDB
