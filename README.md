# Docker LPH

Docker para el despliegue de aplicaciones web en un entorno LAMP + PHPMyAdmin

## Instalación

1. Instalar Docker + Docker-Compose

3. Agregar la siguiente entrada al archivo hosts del equipo

  ```127.0.0.1 pma.lph.local```

4. Clonar el repositorio

5. Renombrar el archivo ```.env.sample``` a ```.env```

6. Cambiar las credenciales de acceso de la base de datos situadas en el archivo ```.env```

7. Arrancar los servicios utilizando el comando ```docker-compose up -d```

8. El servicio PHPMyAdmin sera accesible a traves de [http://pma.lph.local](http://pma.lph.local)




## Creacion de sitios virtuales nuevos

1. Crear una carpeta en el directorio ```htdocs``` con el nombre de host

2. Reiniciar el contenedor utilizando ```docker-compose restart```
