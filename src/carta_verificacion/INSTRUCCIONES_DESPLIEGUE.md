# 📦 Instrucciones de Despliegue - Sistema de Verificación de Cartas

## ✅ Pre-requisitos Completados

- ✅ Base de datos `apppcr` creada
- ✅ Usuario `pedropcr` configurado
- ✅ Tablas creadas:
  - `cartas_trabajo_verificacion`
  - `cartas_deducciones`
  - `cartas_verificaciones_log`

## 🚀 Pasos de Despliegue

### Paso 1: Subir Archivos al Servidor Externo

Necesitas subir la carpeta `carta_verificacion/` al servidor en la ruta:

```
/public_html/carta/
```

**Estructura esperada en el servidor:**
```
/public_html/
  └── carta/
      ├── config.php
      ├── DatabaseExternal.php
      ├── CartaVerificacionService.php
      ├── verificar.php
      ├── index.php
      ├── .htaccess
      ├── README.md
      └── INSTRUCCIONES_DESPLIEGUE.md
```

**Métodos para subir:**

#### Opción A: FTP/SFTP
```bash
# Usando FileZilla, WinSCP o similar
# Conectar a: grupopcr.com.pa
# Subir carpeta carta_verificacion/ a /public_html/carta/
```

#### Opción B: cPanel File Manager
1. Acceder a cPanel
2. Ir a "Administrador de archivos"
3. Navegar a `/public_html/`
4. Crear carpeta `carta`
5. Subir todos los archivos de `carta_verificacion/`

#### Opción C: SSH/SCP
```bash
scp -r carta_verificacion/* usuario@grupopcr.com.pa:/public_html/carta/
```

---

### Paso 2: Verificar Permisos

Conectarse por SSH o usar cPanel Terminal:

```bash
# Permisos de carpeta
chmod 755 /public_html/carta/

# Permisos de archivos
chmod 644 /public_html/carta/*.php
chmod 644 /public_html/carta/.htaccess
chmod 644 /public_html/carta/*.md
```

---

### Paso 3: Configurar Apache/Nginx

#### Si usas Apache (cPanel)
El archivo `.htaccess` ya está incluido y configurado. Verificar que `mod_rewrite` esté activo.

#### Si usas Nginx
Agregar al archivo de configuración del sitio:

```nginx
location /carta/ {
    try_files $uri $uri/ /carta/verificar.php?token=$uri;
    
    location ~ ^/carta/(config|DatabaseExternal|CartaVerificacionService)\.php$ {
        deny all;
    }
}
```

---

### Paso 4: Configurar Conexión a Base de Datos

Editar `/public_html/carta/config.php` si es necesario:

```php
// Ya está configurado con tus credenciales:
define('DB_EXTERNAL_HOST', 'grupopcr.com.pa');  // o 'localhost' si BD está en mismo servidor
define('DB_EXTERNAL_NAME', 'apppcr');
define('DB_EXTERNAL_USER', 'pedropcr');
define('DB_EXTERNAL_PASS', 'elchamo1787$$$');
```

**⚠️ IMPORTANTE:** 
Si la base de datos está en el MISMO servidor que la web, cambiar:
```php
define('DB_EXTERNAL_HOST', 'localhost');
```

---

### Paso 5: Verificar Acceso Remoto a MySQL (Si aplica)

Si la BD está en servidor diferente:

1. **Permitir IP del servidor web en MySQL:**
```sql
-- Conectar a MySQL como root
GRANT ALL PRIVILEGES ON apppcr.* TO 'pedropcr'@'IP_DEL_SERVIDOR_WEB' IDENTIFIED BY 'elchamo1787$$$';
FLUSH PRIVILEGES;
```

2. **Verificar firewall:**
   - Puerto 3306 abierto entre servidores
   - MySQL escuchando en 0.0.0.0

---

### Paso 6: Probar Conexión

Crear archivo temporal de prueba:

```php
// /public_html/carta/test_conexion.php
<?php
require_once 'config.php';
require_once 'DatabaseExternal.php';

try {
    $db = DatabaseExternal::getInstance();
    $pdo = $db->getConnection();
    echo "✓ Conexión exitosa a la base de datos<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cartas_trabajo_verificacion");
    $result = $stmt->fetch();
    echo "✓ Total de cartas: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
```

Acceder a: `https://grupopcr.com.pa/carta/test_conexion.php`

**⚠️ Eliminar este archivo después de la prueba:**
```bash
rm /public_html/carta/test_conexion.php
```

---

### Paso 7: Probar Sistema Completo

#### 7.1 Generar una carta de prueba
1. Ir al sistema administrativo de RRHH
2. Seleccionar una solicitud de carta
3. Completar formulario y hacer clic en "Enviar Carta PDF"

#### 7.2 Verificar que se generó correctamente
- ✅ Se muestra mensaje de éxito
- ✅ PDF contiene código QR en la esquina superior derecha
- ✅ Email enviado al colaborador

#### 7.3 Probar el QR
1. Abrir el PDF generado
2. Escanear el QR con el celular
3. Debe abrir: `https://grupopcr.com.pa/carta/verificar.php?token=...`
4. Debe mostrar página de verificación con datos

#### 7.4 Verificar en base de datos
```sql
-- Verificar que se insertó la carta
SELECT * FROM cartas_trabajo_verificacion ORDER BY id DESC LIMIT 1;

-- Verificar deducciones
SELECT * FROM cartas_deducciones WHERE carta_id = (SELECT MAX(id) FROM cartas_trabajo_verificacion);

-- Verificar log de verificación
SELECT * FROM cartas_verificaciones_log ORDER BY id DESC LIMIT 5;
```

---

## 🔍 Verificación de URLs

Después del despliegue, verificar que estas URLs funcionen:

1. ✅ `https://grupopcr.com.pa/carta/` → Redirige a grupopcr.com.pa
2. ✅ `https://grupopcr.com.pa/carta/verificar.php?token=XXX` → Muestra página de verificación
3. ✅ `https://grupopcr.com.pa/carta/config.php` → Error 403 (Prohibido) ✓ Correcto
4. ✅ Escanear QR de carta generada → Muestra datos

---

## 🐛 Solución de Problemas Comunes

### Error: "No se pudo conectar a la base de datos"

**Causa:** Credenciales incorrectas o acceso remoto no permitido

**Solución:**
```sql
-- Verificar usuario y permisos
SELECT User, Host FROM mysql.user WHERE User = 'pedropcr';

-- Si no existe o el Host es incorrecto:
CREATE USER 'pedropcr'@'localhost' IDENTIFIED BY 'elchamo1787$$$';
GRANT ALL PRIVILEGES ON apppcr.* TO 'pedropcr'@'localhost';
FLUSH PRIVILEGES;
```

### Error: "Error al generar código QR"

**Causa:** No se puede acceder a la API externa o permisos de escritura

**Solución 1:** Verificar conectividad
```bash
curl https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=test
```

**Solución 2:** Verificar permisos
```bash
chmod 777 /ruta/src/app/uploads/carta_trabajo/
# Después de prueba, volver a 755
```

### Error 500 en verificar.php

**Causa:** Error de PHP sin mostrar

**Solución:** Activar errores temporalmente
```php
// Al inicio de verificar.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### QR no aparece en el PDF

**Causa:** mPDF no puede acceder a la imagen temporal

**Solución:** Verificar que existe
```php
// En RRHHController.php, después de descargar QR:
if (!file_exists($temp_qr_path)) {
    error_log("QR no se descargó: " . $temp_qr_path);
}
```

---

## 📋 Checklist Final

Marcar cuando esté completado:

- [ ] Archivos subidos a `/public_html/carta/`
- [ ] Permisos configurados correctamente
- [ ] Conexión a BD verificada
- [ ] Carta de prueba generada exitosamente
- [ ] QR escaneado y verificación funciona
- [ ] Datos insertados en BD externa
- [ ] Log de verificaciones registrando accesos
- [ ] URLs de verificación funcionando
- [ ] Archivos sensibles protegidos (.htaccess)
- [ ] Archivo test_conexion.php eliminado
- [ ] Email de prueba comentado en producción

---

## 📞 Soporte

Si encuentras problemas durante el despliegue:

1. Revisar logs de error: `/public_html/carta/error_log`
2. Revisar logs de Apache/Nginx
3. Verificar logs de PHP
4. Contactar a TI Grupo PCR

---

## 🔐 Seguridad Post-Despliegue

### Cambiar clave de encriptación
```php
// En config.php, cambiar por una clave más segura:
define('ENCRYPTION_KEY', 'GENERAR_CLAVE_ALEATORIA_AQUI_64_CARACTERES_MIN');
```

### Generar clave segura:
```bash
openssl rand -base64 48
```

### Cambiar email de prueba a producción
```php
// En RRHHController.php línea 366:
// Descomentar línea de producción:
$copias = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa"];

// Comentar línea de prueba:
// $copias = ["pedroarrieta25@hotmail.com"];
```

---

## ✅ Sistema Listo

Una vez completado el checklist, el sistema está listo para producción.

**Próximos pasos sugeridos:**
1. Monitorear logs durante la primera semana
2. Revisar estadísticas de verificaciones
3. Configurar backup automático de BD externa
4. Documentar procedimientos para RRHH

