# 1DAW || Grup13 — Web Gestió d'Incidències

## Integrants del projecte

- Juan Jose Osorio Mejia
- Jose Andres Guala Patiño

## Objectiu del projecte

Aplicació web per gestionar incidències informàtiques. Qualsevol persona pot obrir una incidència indicant el seu departament i descrivint el problema. El administrador informàtic la revisa, l'assigna a un tècnic i segons el tipus d'avaria li dona una prioritat. El tècnic registra les actuacions fetes fins que la incidència queda resolta.

## Estat del projecte

Funcional. L'aplicació està desplegada en producció amb totes les funcionalitats implementades.

## Adreça web del projecte

http://grup13.daw.inspedralbes.cat/proyecto_final/php/index.php

---

## Funcionalitats Principals

### Per a Usuaris:

- **Registre d'incidències:** Formulari per enviar incidències indicant el títol, el departament i la descripció del problema.
- **Consulta d'estat:** Cerca per ID d'incidència per veure l'estat actual i les actuacions visibles fetes pels tècnics.

### Per a Tècnics i Administradors:

- **Gestió d'incidències:** Llistat interactiu per assignar prioritat, tipologia i tècnic responsable directament des de la taula.
- **Registre d'actuacions:** Formulari per afegir actuacions a una incidència, amb temps invertit i visibilitat per a l'usuari.
- **Estadístiques d'accés:** Panell amb gràfic de tendències per dia, top 5 pàgines visitades i log dels últims 10 accessos, alimentat per MongoDB.
- **Informe per departaments:** Resum d'incidències i temps per departament.
- **Informe per tècnics:** Resum d'incidències resoltes i temps per tècnic.

---

## Tecnologies Utilitzades

- **Backend:** PHP procedural amb MySQLi
- **Base de Dades Principal:** MySQL
- **Base de Dades de Logs:** MongoDB
- **Frontend:** HTML5,JavaScript
- **Estils:** Bootstrap
- **Llibreries PHP:** MongoDB PHP Library
- **Gestió de dependències:** Composer
- **Entorn de desenvolupament:** Docker
---

## Serveis Docker

| Servei  | Descripció                        |
|---------|-----------------------------------|
| app     | Aplicació PHP/Apache              |
| db      | Base de dades MySQL               |
| mongodb | Base de dades de logs MongoDB     |

---

## Instal·lació i Configuració

**1. Clonar el repositori:**

```bash
git clone https://github.com/inspedralbes/projecte-1daw-25-26-la_doble_jj-1.git
cd projecte-1daw-25-26-la_doble_jj-1
```

**2. Configurar l'entorn:**

Crear un fitxer `.env` dins de la carpeta `php/` amb les variables de connexió a MySQL i MongoDB.

**3. Instal·lar dependències PHP:**

```bash
cd php
composer install
```

**4. Aixecar els contenidors:**

```bash
docker compose up
```

L'aplicació estarà disponible a http://localhost:8080

---

## Diagramas


Diagrama de casos d'us:



Diagrama del model E-R:
<img width="1122" height="871" alt="Diagrama E-R drawio" src="https://github.com/user-attachments/assets/0e6ca514-d069-49c9-8a63-72b07c7eaf15" />




Wireframe:
<img width="6485" height="4989" alt="Esquema de pantalles de l&#39;aplicació (1)" src="https://github.com/user-attachments/assets/63498197-2b6b-47c7-a427-74514d0b9eed" />






## Pàgines amb validació WCAG AA

- `actuacion.php`
- `incidencias.php`
