# Juego Toros y Vacas

## Información relacionada con la APIRest.

### 1. Framework y Requisitos

* Se utilizó la versión 10.29.0 de Laravel.
* Se utilizó la versión 8.1.10 de PHP.
* Se utilizó la versión 3.35.5 de sqlite.
* Se utilizó la version 8.5.1 de Swagger para documentar los Endpoints. 

### 2. Requisitos de configuración 

* Configuración necesaria en las variables de entorno para poder ejecutar correctamente la aplicación. 

* Es necesario exitan las siguientes variables en el archivo .env

DB_CONNECTION=sqlite

DB_DATABASE=/laragon/www/bullscows/database/bd.sqlite
(debe ser la direccion de donde se va a ejecutar la aplicación en dependecia del servidor utilizado para ello, dígase xampp, wampp, laragon u otro).

API_KEY=$2y$10$wE7HwIwC6CQiN0V9ijMLIur1CBlVDflEVrxGu6yavQeH60hfNzhOu
(La variable API_KEY es necesaria que este configurada para poder ejecutar desde swagger los endpoints utilizando este llave como seguridad)

MAX_TIME=60
(Variable para definir el tiempo maximo de duración de un juego)


### 3. Requisitos del negocio


* En el endpoint **POST api/games/show** se puede consultar la información de un juego y entre sus atributos se puede verificar la combinación numérica a adivinar. 
* En el endpoint **GET api/games/all** se muestran todos los atributos de los juegos.
* El endpoint **POST api/games/combination** es el encargado de gestionar la dinámica del juego, comprobando en todos los casos la combinación enviada, tanto para saber si ya se envió anteriormente, si tiene algún dígito repetido, en caso de ser correcta, saber cuántos toros y vacas se obtienen o si la misma es la combinación correcta, lo que da por concluida la partida, o también finaliza en caso de culminar el tiempo de la misma definida en la variable de entorno MAX_TIME. 
* Para los endpoints dedicados a la gestión de los juegos, en formato json se le debe enviar el id y user del juego, para poder garantizar que cada usuario solo gestione los juego asociados al mismo, validando lo recibido en la petición con el usuario del juego.  

```JSON
   {        
     "id": "1",
     "user": "Pepe"
   }
```  

* En el endpoint para crear un juego se debe enviar la información en formato JSON con el siguiente formato de ejemplo:

```JSON
   {        
     "user": "Pepe",
     "age": "25"
   }

```  
"# bullscows" 
