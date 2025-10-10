# 🚀 Despliegue Simplificado - Sistema de Verificación de Cartas

## 📦 Estructura del Sistema

```
src/
└── carta_verificacion/  ← Sistema de verificación
    ├── config.php
    ├── DatabaseExternal.php
    ├── CartaVerificacionService.php
    ├── verificar.php
    ├── index.php
    └── .htaccess
```

---

## 🐳 **DESPLIEGUE EN DOCKER (AWS)**

### Paso 1: Pull del repositorio

```bash
ssh ubuntu@TU_IP_AWS
cd /home/ubuntu/apppcr/
git pull origin main
```

### Paso 2: Verificar que el contenedor ve los archivos

```bash
docker exec -it apppcr_php ls -la /var/www/html/carta_verificacion/
```

Deberías ver todos los archivos PHP.

### Paso 3: Configurar BD externa

Editar `src/carta_verificacion/config.php` según donde esté la BD:

**Si la BD está en GoDaddy:**
```php
define('DB_EXTERNAL_HOST', 'grupopcr.com.pa'); // o IP del servidor MySQL
```

**Si la BD está en el mismo servidor (Docker):**
```php
define('DB_EXTERNAL_HOST', 'apppcr_db'); // Nombre del contenedor
```

### Paso 4: Probar

Generar una carta de trabajo desde el sistema de RRHH.

---

## 🌐 **DESPLIEGUE EN GODADDY (Verificación Pública)**

Para que funcione la verificación pública en `https://grupopcr.com.pa/carta/`:

### Opción A: Subir manualmente vía cPanel

1. Ir a cPanel → File Manager
2. Navegar a `/public_html/`
3. Crear carpeta `carta`
4. Subir todos los archivos de `src/carta_verificacion/` a `/public_html/carta/`

### Opción B: Git en GoDaddy (si está habilitado)

```bash
# Si GoDaddy tiene SSH habilitado
cd /public_html/
git clone URL_DEL_REPO
cp -r ruta/src/carta_verificacion/* carta/
```

### Configuración en GoDaddy

Editar `/public_html/carta/config.php`:
```php
define('DB_EXTERNAL_HOST', 'localhost'); // La BD está en el mismo servidor
```

### Permisos

- Carpeta `carta/`: 755
- Archivos `.php`: 644
- Archivo `.htaccess`: 644

### Verificar

Abrir: `https://grupopcr.com.pa/carta/test_sistema.php`

---

## 🔧 **Configuración de Base de Datos**

### Si la BD está en GoDaddy

1. En cPanel → Remote MySQL
2. Agregar IP de AWS a "Access Hosts"
3. En `config.php` de AWS, usar el hostname de GoDaddy

### Si la BD está en Docker (AWS)

1. En `config.php` usar: `define('DB_EXTERNAL_HOST', 'apppcr_db');`
2. No se necesita acceso remoto

---

## ✅ **Checklist de Despliegue**

### AWS (Docker):
- [ ] `git pull` ejecutado
- [ ] Archivos visibles en contenedor
- [ ] `config.php` configurado con host correcto
- [ ] Probar generación de carta

### GoDaddy:
- [ ] Archivos subidos a `/public_html/carta/`
- [ ] Permisos configurados
- [ ] `config.php` configurado
- [ ] Probar: `https://grupopcr.com.pa/carta/test_sistema.php`

### Base de Datos:
- [ ] Tablas creadas (cartas_trabajo_verificacion, cartas_deducciones, cartas_verificaciones_log)
- [ ] Acceso remoto configurado (si es necesario)
- [ ] Credenciales correctas en `config.php`

---

## 🧪 **Prueba End-to-End**

1. Generar carta de trabajo desde RRHH
2. Verificar PDF tiene QR
3. Escanear QR con celular
4. Debe abrir: `https://grupopcr.com.pa/carta/verificar.php?token=...`
5. Debe mostrar datos de la carta

---

## 🐛 **Troubleshooting**

### Error: "No se encontró el sistema de verificación"
```
throw new Exception("No se encontró el sistema de verificación en: /var/www/html/carta_verificacion/");
```

**Solución:** Los archivos no están en la ubicación correcta.
```bash
docker exec -it apppcr_php ls /var/www/html/
# Debe mostrar carta_verificacion/
```

### Error: "Failed opening required config.php"

**Solución:** Verificar que `config.php` tenga permisos de lectura:
```bash
docker exec -it apppcr_php ls -la /var/www/html/carta_verificacion/config.php
```

### Error: "Connection refused" al conectar a BD

**Solución:** 
- Verificar que `DB_EXTERNAL_HOST` sea correcto
- Si es remoto, verificar que el firewall/acceso remoto esté configurado

---

## 📞 **Soporte**

Para problemas contactar a TI Grupo PCR.

**Última actualización:** 2025-10-10

