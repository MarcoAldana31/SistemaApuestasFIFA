# Sistema de apuestas FIFA - backend

## Pasos para instalarlo

Paso 1. Instalar dependencias y copiar el archivo env para la conexion, generar la clave de seguridad de la aplicación.
```
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

Paso 2. Configurar base de datos
- Cree la base de datos en su esquema mariadb.
- Configurar el archivo .env, para que se apegue a su configuración, ejemplo:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_apuestas_fifa
DB_USERNAME=root
DB_PASSWORD=
```

Paso 3. Ejecutar migraciones y datos defecto
```
php artisan migrate
php artisan db:seed
```

## Ejecutar aplicación

Levantar aplicación en un host y puerto en específico.
```
php artisan serve --host=192.168.0.5 --port=8010
```

NOTA: Para el valor de *host* se debe verificar que ip tiene su computador.
En windows ejecutar en la terminal: ```ipconfig```, valor en *IPv4 Address*.
```
Wireless LAN adapter Wi-Fi:
   Connection-specific DNS Suffix  . :
   IPv6 Address. . . . . . . . . . . : ....
   Temporary IPv6 Address. . . . . . : ....
   Link-local IPv6 Address . . . . . : ....
   IPv4 Address. . . . . . . . . . . : 192.168.0.5
   Subnet Mask . . . . . . . . . . . : 255.255.255.0
   Default Gateway . . . . . . . . . : ...
                                       192.168.0.1
```