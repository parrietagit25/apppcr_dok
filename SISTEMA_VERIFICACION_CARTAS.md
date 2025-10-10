# 📋 Sistema de Verificación de Cartas con QR - Grupo PCR

## ✅ **IMPLEMENTACIÓN COMPLETADA**

Sistema de verificación de cartas de trabajo con código QR integrado.

---

## 🚀 **Despliegue Rápido**

### **En AWS (Docker):**

```bash
ssh ubuntu@TU_IP_AWS
cd /home/ubuntu/apppcr/
git pull origin main

# Verificar archivos
docker exec -it apppcr_php ls /var/www/html/carta_verificacion/

# Probar generando una carta de trabajo
```

### **En GoDaddy (Verificación Pública):**

1. cPanel → File Manager
2. Ir a `/public_html/`
3. Crear carpeta `carta`
4. Subir archivos de `src/carta_verificacion/` a `/public_html/carta/`
5. Probar: https://grupopcr.com.pa/carta/test_sistema.php

---

## 📁 **Archivos del Sistema**

```
src/carta_verificacion/
├── config.php                      # Configuración de BD y constantes
├── DatabaseExternal.php            # Conexión a BD externa
├── CartaVerificacionService.php    # Lógica principal
├── verificar.php                   # Página pública de verificación
├── test_sistema.php                # Tests (eliminar en producción)
├── README.md                       # Documentación técnica
└── README_DESPLIEGUE.md            # Guía de despliegue paso a paso
```

---

## 🔧 **Configuración Importante**

### **Archivo: `src/carta_verificacion/config.php`**

Ajustar según donde esté la base de datos:

```php
// Si la BD está en GoDaddy:
define('DB_EXTERNAL_HOST', 'grupopcr.com.pa');

// Si la BD está en Docker (AWS):
define('DB_EXTERNAL_HOST', 'apppcr_db');

// Si es local en el mismo servidor:
define('DB_EXTERNAL_HOST', 'localhost');
```

---

## 🎯 **Funcionalidades**

- ✅ Generación automática de código QR en cada carta
- ✅ Verificación pública vía web
- ✅ Base de datos externa para auditoría
- ✅ Encriptación AES-256-CBC
- ✅ Log de todas las verificaciones
- ✅ Hash SHA256 para integridad

---

## 📖 **Documentación Completa**

Ver: `src/carta_verificacion/README_DESPLIEGUE.md`

---

## 🧪 **Pruebas**

### Test del sistema:
```
https://grupopcr.com.pa/carta/test_sistema.php
```

### Generar carta de prueba:
1. Ir a RRHH → Cartas de Trabajo
2. Aprobar una solicitud
3. Verificar que el PDF tenga QR
4. Escanear QR y verificar

---

## 🗄️ **Base de Datos**

### Tablas creadas:
- `cartas_trabajo_verificacion` - Datos de las cartas
- `cartas_deducciones` - Deducciones salariales
- `cartas_verificaciones_log` - Log de accesos

### Ubicación:
Base de datos: `apppcr`  
Usuario: `pedropcr`  
Host: Configurar en `config.php`

---

## 📞 **Soporte**

Para más información, contactar al departamento de TI.

**Fecha de implementación:** Octubre 2025  
**Versión:** 1.0.0

