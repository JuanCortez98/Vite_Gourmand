# ✅ Base de Datos Configurada - Guía de Inicio

## 🎉 ¡Tu aplicación está lista!

La base de datos **vite_gourmand** ha sido creada exitosamente con todas las tablas y datos de prueba.

---

## 📊 Verificación de Base de Datos

```
✅ Database: vite_gourmand
✅ Tabla users: 3 usuarios
✅ Tabla menus: 4 menús
✅ Tabla commandes: 1 orden
```

### 👤 Cuentas de Prueba:

| Email | Rol | Contraseña |
|-------|-----|------------|
| admi@vite.fr | Admin | (hash: $2y$10$UukSDWEgD3jMVlDFyWxYfOM1IHcn8mk0rVvhilb6CswwU/NP2k3Se) |
| client@vite.fr | Cliente | (hash: $2y$10$.90.YWooChbOqRXPIBxZke.zLdbm672u28hUgmAjbQYrwtFkeQBti) |
| travailleur@vite.fr | Trabajador | (hash: $2y$10$eWzK99w0rAhKKZO2uvrnqe/W6NejSNsUo1sHBD03BcJZScJOETXe6) |

**Nota:** Las contraseñas están hasheadas con bcrypt (PASSWORD_DEFAULT). Para pruebas, necesitarás:
- Registrar un nuevo usuario, O
- Actualizar el hash con una contraseña conocida

---

## 🚀 Cómo Iniciar la Aplicación

### Opción 1: Con XAMPP (Recomendado)

1. **Abre XAMPP Control Panel**
   ```
   C:\xampp\xampp-control.exe
   ```

2. **Inicia los servicios:**
   - Apache ✅
   - MySQL ✅

3. **Accede a la aplicación:**
   - http://localhost/vite-gourmand/public/index.php

### Opción 2: Por Terminal (Actual)

La base de datos ya está corriendo en otra terminal. Solo necesitas:

```powershell
# En terminal nueva, navega a:
cd C:\xampp\htdocs\vite-gourmand

# O accede directamente desde el navegador:
# http://localhost/vite-gourmand/public/index.php
```

---

## 🔐 Funcionalidades Implementadas

### ✅ Seguridad
- [x] Tokens CSRF en todos los formularios
- [x] Password hashing con bcrypt
- [x] Protección de sesiones
- [x] Validación de JWT
- [x] Control de acceso por roles

### ✅ Autenticación
- [x] Login con email/contraseña
- [x] Registro de nuevos usuarios
- [x] Logout seguro
- [x] Redirecciones por rol (admin/cliente/trabajador)

### ✅ Funcionalidades
- [x] Gestión de menús (admin)
- [x] Gestión de órdenes (trabajador)
- [x] Dashboard para clientes
- [x] Visualización de menús disponibles

---

## 📁 Estructura de Archivos

```
vite-gourmand/
├── public/
│   ├── index.php          (Página de inicio)
│   ├── menus.php          (Catálogo de menús)
│   ├── about.php          (Acerca de)
│   └── mentions-legales.php
├── autentification/
│   ├── login.php          ✅ Corregido con CSRF
│   ├── register.php       ✅ Corregido con CSRF
│   └── logout.php
├── client/
│   ├── dashboard.php      ✅ Protegido por roles
│   └── nouvelle-commande.php
├── admin/
│   ├── index.php          ✅ Dashboard admin
│   ├── gestion-menus.php
│   ├── gestion-commandes.php
│   └── gestion-utilisateurs.php
├── travailleur/
│   └── dashboard.php      ✅ Dashboard trabajador
├── Includes/
│   ├── config.php         ✅ Conexión BD + CSRF
│   └── jwt.php            ✅ Funciones JWT
├── css/
│   ├── style.css          (Estilos principales)
│   └── old/               (Estilos específicos por página)
├── js/
│   └── *.js               (Scripts JavaScript)
└── database/
    └── vite_gourmand.sql  (Esquema BD + datos semilla)
```

---

## 🧪 Testing

### Pruebas Rápidas:

1. **Página de inicio:**
   - http://localhost/vite-gourmand/public/index.php

2. **Intentar login (sin cuenta):**
   - http://localhost/vite-gourmand/autentification/login.php

3. **Registrar nuevo usuario:**
   - http://localhost/vite-gourmand/autentification/register.php

4. **Como Admin (después de crear contraseña):**
   - http://localhost/vite-gourmand/admin/index.php

---

## ⚠️ Próximos Pasos IMPORTANTES

### Para Producción:

1. **Cambiar credenciales de BD:**
   ```php
   // En Includes/config.php, cambiar de:
   define('DB_USER', 'root');
   define('DB_PASS', '');
   
   // A variables de entorno
   ```

2. **Usar archivo .env:**
   ```
   DB_HOST=localhost
   DB_NAME=vite_gourmand
   DB_USER=usuario_seguro
   DB_PASS=contraseña_fuerte
   JWT_SECRET=clave_secreta_fuerte
   ```

3. **Implementar HTTPS:**
   - Es crítico para proteger tokens CSRF y JWT
   - Usar certificados SSL/TLS

4. **Rate Limiting:**
   - En login y registro para prevenir ataques

5. **Logging:**
   - Registrar intentos fallidos de login
   - Auditoría de cambios en BD

6. **Validar Contraseñas Iniciales:**
   - Las cuentas de prueba tienen hash pero sin contraseña clara
   - Crear nuevas cuentas para testing

---

## 🐛 Troubleshooting

### "Can't connect to MySQL server"
```
✅ Solución: Asegúrate de que mysqld está corriendo en otra terminal
```

### "404 - Archivo no encontrado"
```
✅ Solución: Verifica que la carpeta está en C:\xampp\htdocs\vite-gourmand\
```

### "Tabla no existe"
```
✅ Solución: Re-ejecutar el script SQL:
Get-Content "database/vite_gourmand.sql" | C:\xampp\mysql\bin\mysql -u root
```

### "Token CSRF inválido"
```
✅ Solución: Es normal si refrescas (el token cambia). Intenta nuevamente.
```

---

## 📞 Soporte

Para más información, revisa:
- [CORRECCIONES.md](CORRECCIONES.md) - Cambios realizados
- [README.md](README.md) - Documentación del proyecto
- [database/vite_gourmand.sql](database/vite_gourmand.sql) - Esquema BD

---

**Última actualización:** Abril 10, 2026
**Estado:** ✅ Listo para desarrollo y testing
