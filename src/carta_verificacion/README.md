# Sistema de Verificación de Cartas de Trabajo

## 📋 Descripción

Sistema de verificación externa de cartas de trabajo con código QR para Grupo PCR. Permite la validación de autenticidad de cartas laborales mediante escaneo de código QR o acceso directo a URL de verificación.

## 🏗️ Arquitectura

### Base de Datos (Servidor Externo)
- **Host:** grupopcr.com.pa
- **Base de Datos:** apppcr
- **Tablas:**
  - `cartas_trabajo_verificacion` - Datos principales de las cartas
  - `cartas_deducciones` - Deducciones salariales
  - `cartas_verificaciones_log` - Auditoría de verificaciones

### Archivos del Sistema

```
carta_verificacion/
├── config.php                      # Configuración de BD externa y constantes
├── DatabaseExternal.php            # Singleton para conexión a BD externa
├── CartaVerificacionService.php    # Lógica de negocio principal
├── verificar.php                   # Página pública de verificación
├── index.php                       # Redirección
├── .htaccess                       # Seguridad y reescritura
└── README.md                       # Esta documentación
```

## 🔒 Seguridad

### Encriptación
- **Método:** AES-256-CBC
- **Token:** Encriptado y URL-safe
- **Hash:** SHA256 para verificación

### Acceso
- Archivos de configuración protegidos vía `.htaccess`
- Validación de tokens
- Log de todos los accesos con IP y User Agent

## 🚀 Flujo de Operación

### 1. Generación de Carta (Sistema Interno)
```
RRHHController.php → enviar_carta_pdf
├── Obtiene datos del colaborador
├── Genera token encriptado
├── Registra en BD externa
├── Genera QR con URL de verificación
├── Inserta QR en PDF
└── Envía por email
```

### 2. Verificación (Sistema Público)
```
Escaneo QR → verificar.php?token=XXXXX
├── Desencripta token
├── Busca en BD externa
├── Valida estado y vigencia
├── Registra en log
└── Muestra datos verificados
```

## 📡 API de QR Code

**Servicio:** QR Server API
**URL:** https://api.qrserver.com/v1/create-qr-code/
**Características:**
- Gratuito y sin límites
- Alta disponibilidad
- Formato PNG

## 🔗 URLs de Acceso

- **Verificación:** https://grupopcr.com.pa/carta/verificar.php?token=XXXXX
- **Alternativa:** https://grupopcr.com.pa/carta/XXXXX (con reescritura)

## 📊 Datos Almacenados

### Información del Colaborador
- Código de empleado
- Nombre completo
- Cédula
- Seguro social
- Email
- Cargo
- Fecha de ingreso

### Información Salarial (Opcional)
- Salario bruto
- Deducciones detalladas
- Cálculo de salario neto

### Metadatos de Seguridad
- Hash de verificación único
- Hash SHA256 del PDF
- Token QR encriptado
- IP de generación
- Fecha de emisión y expiración
- Contador de verificaciones

## ⚙️ Configuración

### Variables en `config.php`

```php
// Conexión BD Externa
DB_EXTERNAL_HOST      = 'grupopcr.com.pa'
DB_EXTERNAL_NAME      = 'apppcr'
DB_EXTERNAL_USER      = 'pedropcr'
DB_EXTERNAL_PASS      = 'elchamo1787$$$'

// Sistema
URL_BASE_VERIFICACION = 'https://grupopcr.com.pa/carta/'
ENCRYPTION_KEY        = 'PCR_2025_CARTA_VERIFICACION_KEY_SECURE'
DIAS_EXPIRACION_CARTA = 365 (1 año)
```

### Instalación en Servidor Externo

1. **Subir carpeta completa a:**
   ```
   /public_html/carta/
   ```

2. **Verificar permisos:**
   ```bash
   chmod 755 /public_html/carta/
   chmod 644 /public_html/carta/*.php
   chmod 644 /public_html/carta/.htaccess
   ```

3. **Verificar acceso:**
   - https://grupopcr.com.pa/carta/verificar.php

4. **Probar con token de prueba**

## 🔍 Mantenimiento

### Consultas Útiles

```sql
-- Cartas activas
SELECT * FROM cartas_trabajo_verificacion WHERE estado = 'activa';

-- Cartas por vencer (próximos 30 días)
SELECT * FROM cartas_trabajo_verificacion 
WHERE fecha_expiracion BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY);

-- Más verificadas
SELECT codigo_empleado, nombre, apellido, total_verificaciones 
FROM cartas_trabajo_verificacion 
ORDER BY total_verificaciones DESC LIMIT 10;

-- Log de verificaciones de hoy
SELECT * FROM cartas_verificaciones_log 
WHERE DATE(fecha_verificacion) = CURDATE();
```

### Revocar una Carta

```sql
UPDATE cartas_trabajo_verificacion 
SET estado = 'revocada', 
    motivo_revocacion = 'Razón aquí' 
WHERE id = X;
```

## 🐛 Resolución de Problemas

### Error: "No se pudo conectar a BD externa"
- Verificar credenciales en `config.php`
- Verificar firewall/whitelist IP en servidor MySQL
- Verificar que MySQL permita conexiones remotas

### Error: "Error al generar código QR"
- Verificar conectividad con api.qrserver.com
- Verificar permisos de escritura en `/uploads/carta_trabajo/`
- Verificar que `file_get_contents()` esté habilitado

### QR no aparece en PDF
- Verificar que la imagen temporal se descargó
- Verificar rutas file:// en mPDF
- Revisar logs de error de PHP

## 📝 Changelog

### v1.0.0 (2025-10-10)
- Implementación inicial
- Sistema de verificación con QR
- Integración con BD externa
- Log de auditoría
- Página de verificación responsive

## 👥 Soporte

Para soporte técnico, contactar a:
- Desarrollo: TI Grupo PCR
- RRHH: sofia.macias@grupopcr.com.pa

## ⚖️ Licencia

Uso interno exclusivo de Grupo PCR. Todos los derechos reservados.

