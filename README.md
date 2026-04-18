# Cotizador IPS

**Cotizador IPS** es una aplicación web robusta diseñada para gestionar y generar cotizaciones de servicios médicos de diferences IPS conn el fin de llevar control de las tarifas que ofrecen año a año y comparar con otras IPS en la misma ciudad. 
Desarrollada bajo la arquitectura **Modelo-Vista-Controlador (MVC)** en PHP, ofrece una estructura modular, escalable y fácil de mantener.

## Características Principales

* **Arquitectura MVC:** Separación clara de la lógica de negocio, manejo de datos e interfaz de usuario.
* **Gestión de Cotizaciones:** Flujo optimizado para la selección de servicios por ciudades y cálculo de costos.
* **Estructura Modular:** Fácil implementación con módulos (Tarifas, Proovedores, Examenes, etc...).

##  Stack Tecnológico

* **Backend:** PHP 8.x
* **Frontend:** HTML5, CSS3 (Bootstrap), JavaScript (jQuery)
* **Base de Datos:** MySQL / MariaDB
* **Servidor Recomendado:** Apache / Nginx (compatible con XAMPP/Laragon)

## Estructura del Proyecto

```text
cotizador_ips/
├── assets/          # Archivos estáticos (CSS, JS, Imágenes)
├── configuracion/   # Conexión a DB y variables de entorno
├── controladores/   # Lógica de las peticiones y rutas
├── modelos/         # Consultas y manipulación de datos
├── vistas/          # Plantillas e interfaz de usuario
└── index.php        # Punto de entrada de la aplicación
```
## Instalación

1. Clonar el repositorio:
```git clone [https://github.com/migzam10/cotizador_ips.git](https://github.com/migzam10/cotizador_ips.git)```

2. Configurar el servidor: Mueve la carpeta a tu directorio de tu servidor.

3.Base de Datos:

Crea una base de datos en tu gestor MySQL.
Importa el archivo BD.sql.
Crea un archivo .env en la raiz con los parametros del archivo .env.example

4. Ejecutar: Abre ```http://localhost/cotizador_ips``` en tu navegador.

## Lincencia

MIT — libre de usar, modificar y distribuir. 