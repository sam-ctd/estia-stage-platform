Lire ceci en [Français](README.md) | Read this in [English](README.en.md)

# 🎓 ESTIA Stage - Plataforma de Contratación

![Status](https://img.shields.io/badge/Status-Academic_Project-blue)
![Lenguaje](https://img.shields.io/badge/Backend-PHP-purple)
![Frontend](https://img.shields.io/badge/Frontend-TailwindCSS-teal)

## ℹ️ Sobre este proyecto

**⚠️ Contexto Académico:**
Este proyecto fue desarrollado como parte del plan de estudios en **ESTIA**. Es una aplicación web desarrollada **localmente** con fines pedagógicos. No está destinada a ser desplegada en producción tal cual, sino que sirve como demostración de competencias en desarrollo Fullstack (PHP/JS/SQL).

El objetivo era crear una plataforma funcional para conectar estudiantes y reclutadores, con un panel de administración para la moderación.

---

## ✨ Funcionalidades Principales

La aplicación se divide en tres espacios distintos:

### 👨‍🎓 Espacio Estudiante
- **Panel de control:** Vista general de las candidaturas y estadísticas.
- **Búsqueda de ofertas:** Filtrado por sector, tipo de contrato y palabras clave.
- **Candidatura:** Postular a ofertas, gestión de favoritos.
- **Mensajería:** Chat en tiempo real (simulación) con los reclutadores.
- **Reportes:** Posibilidad de reportar una oferta sospechosa a la administración.
- **Perfil:** Gestión de información personal y carga de CV.

### 💼 Espacio Reclutador
- **Gestión de ofertas:** Creación, modificación y eliminación de ofertas de pasantía/empleo.
- **Seguimiento de candidatos:** Gestión de estados (Pendiente, Entrevista, Aceptado, Rechazado).
- **Mensajería:** Contacto directo con los candidatos.
- **Notificaciones:** Alertas al recibir mensajes.

### 🛡️ Espacio Administrador
- **Vista global:** Estadísticas sobre usuarios y ofertas.
- **Gestión de usuarios:** CRUD (Crear, Leer, Actualizar, Eliminar) sobre estudiantes y reclutadores.
- **Moderación:** Validación de ofertas pendientes y tratamiento de reportes (eliminar o ignorar).

---

## 🛠️ Stack Tecnológico

* **Frontend:** HTML5, CSS3 (TailwindCSS vía CDN), JavaScript (Vanilla ES6).
* **Backend:** PHP (Nativo, sin framework).
* **Base de datos:** MySQL.
* **Diseño:** FontAwesome (Iconos), Google Fonts (Inter).

---

## 🚀 Instalación y Lanzamiento

Este proyecto está diseñado para ejecutarse en un servidor local (WAMP, XAMPP, MAMP o Laragon).

### Requisitos previos
* Un servidor local compatible con PHP y MySQL.
* Un navegador web moderno.

### Pasos
1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/tu-nombre/estia-stage-platform.git](https://github.com/tu-nombre/estia-stage-platform.git)
    ```
2.  **Base de datos:**
    * Abre tu gestor de base de datos (ej: phpMyAdmin).
    * Crea una base de datos llamada `estia_stage` (u otro nombre).
    * Importa el archivo `estia_stage.sql` ubicado en la raíz.
3.  **Configuración:**
    * Abre el archivo `db.php`.
    * Modifica las credenciales de conexión PDO si es necesario (`host`, `dbname`, `username`, `password`).
4.  **Lanzamiento:**
    * Coloca la carpeta del proyecto en el directorio `www` o `htdocs` de tu servidor.
    * Accede a `http://localhost/estia-stage-platform/login.html`.

---

## 🔑 Cuentas de Demostración

Para probar los diferentes roles, puedes usar estas cuentas preconfiguradas (si están presentes en el archivo SQL importado):

| Rol | Email | Contraseña |
| :--- | :--- | :--- |
| **Estudiante** | `marie@estia.fr` | `1234` |
| **Reclutador** | `rh@airbus.com` | `1234` |
| **Admin** | `admin@estia.fr` | `1234` |

---

## 📂 Estructura del Proyecto

```text
/
├── admin.html       # Dashboard Administrador
├── login.html       # Página de inicio de sesión
├── recruiter.html   # Dashboard Reclutador
├── signup.html      # Página de registro
├── student.html     # Dashboard Estudiante
├── style.css        # Estilos CSS personalizados
├── api.php          # Backend (API REST en PHP)
├── db.php           # Conexión a la base de datos
├── estia_stage.sql  # Estructura y datos de la BD
└── README.md        # Documentación

Autor: Samuel CHATARD
Fecha: 16/02/2026