# 🚗 SISTEMA DE RESERVAS - AUTOMARKET

Sistema de pantallas de bienvenida para clientes con reservas activas en Automarket.

---

## 📋 DESCRIPCIÓN

Sistema que muestra en tiempo real los nombres de clientes con reservas activas del día en pantallas de bienvenida ubicadas en diferentes sucursales de Automarket.

---

## 🗂️ ARCHIVOS DEL SISTEMA

### Pantallas de Visualización:
- `reservas_pty.php` - Pantalla para Panamá Ciudad (PTY)
- `reservas_malek.php` - Pantalla para Malek

### APIs Backend:
- `insert_reserva.php` - API para insertar nuevas reservas
- `consulta_clientes.php` - API para consultar clientes PTY
- `consulta_clientes_malek.php` - API para consultar clientes MALEK
- `borrar_reserva.php` - Limpieza de reservas

### Scripts SQL:
- `crear_tabla_reservas.sql` - Creación de tabla y datos de prueba

### Recursos:
- `1.png` - Logo de Automarket

---

## 🚀 INSTALACIÓN

### 1. Crear la tabla en la base de datos:

```bash
# Conectar al contenedor MySQL
docker exec -it apppcr_db mysql -uappuser -papppass apppcr

# O desde la línea de comandos:
docker exec -i apppcr_db mysql -uappuser -papppass apppcr < src/reservas/crear_tabla_reservas.sql
```

### 2. Verificar que la tabla se creó:

```sql
USE apppcr;
DESCRIBE reservas;
```

### 3. (Opcional) Insertar datos de prueba:

Descomentar la sección de datos de prueba en `crear_tabla_reservas.sql` y ejecutar.

---

## 🔧 CONFIGURACIÓN

### Credenciales de Base de Datos:

Todos los archivos PHP ya están configurados para usar el contenedor Docker:

```php
$host = "db";           // Contenedor Docker MySQL
$usuario = "appuser";   // Usuario del contenedor
$contraseña = "apppass"; // Contraseña del contenedor
$dbname = "apppcr";     // Base de datos
```

---

## 📺 USO DE LAS PANTALLAS

### Pantalla PTY:
```
URL: https://apppcr.net/reservas/reservas_pty.php

Características:
- Muestra clientes con reservas HOY en ubicación PTY
- Auto-refresh cada 60 segundos
- Scroll automático si hay más de 25 clientes
- Clientes VIP (sourcecode 210) en verde y negrita
```

### Pantalla MALEK:
```
URL: https://apppcr.net/reservas/reservas_malek.php

Características:
- Muestra clientes con reservas HOY en ubicación MALEK
- Auto-refresh cada 60 segundos
- Pantalla estática (sin scroll)
- Clientes VIP (sourcecode 210) en verde y negrita
```

---

## 🔌 API DE INTEGRACIÓN

### Insertar Nueva Reserva:

**Endpoint:** `POST /reservas/insert_reserva.php`

**Content-Type:** `application/json`

**Payload:**
```json
{
  "commonid": 1001,
  "resnumber": "RES-2025-001",
  "ranumber": "RA-001",
  "company": "Empresa ABC",
  "dateout": "2025-10-20",
  "datein": "2025-10-23",
  "reservedclass": "Economy",
  "customer": "JUAN PEREZ",
  "dateadded": "2025-10-20 08:30:00",
  "locationcodeout": "PTY",
  "locationcodein": "PTY",
  "resstatus": 20,
  "sourcecode": "100"
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "message": "Registro insertado"
}
```

**Respuesta Error:**
```json
{
  "error": "Error al insertar",
  "detail": "Duplicate entry..."
}
```

---

## 📊 ESTRUCTURA DE DATOS

### Tabla: `reservas`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID autoincremental |
| `commonid` | INT | ID común de la reserva (requerido) |
| `resnumber` | VARCHAR(50) | Número de reserva |
| `ranumber` | VARCHAR(50) | Número de RA |
| `company` | VARCHAR(100) | Compañía del cliente |
| `dateout` | DATE | Fecha de salida ⭐ |
| `datein` | DATE | Fecha de devolución |
| `dateadded` | DATETIME | Fecha agregada al sistema |
| `reservedclass` | VARCHAR(50) | Clase de vehículo |
| `customer` | VARCHAR(255) | Nombre del cliente ⭐ |
| `locationcodeout` | VARCHAR(10) | Ubicación salida ⭐ (PTY/MALEK) |
| `locationcodein` | VARCHAR(10) | Ubicación devolución |
| `resstatus` | INT | Estado (20=Activa) ⭐ |
| `sourcecode` | VARCHAR(10) | Código fuente (210=VIP) |

---

## 🎯 ESTADOS DE RESERVA

- **20** = Reserva activa (mostrar en pantallas)
- Otros estados = No se muestran

---

## ⭐ CÓDIGOS DE FUENTE

- **210** = Cliente VIP/Prioritario (se muestra en VERDE)
- **Otros** = Cliente regular (color normal)

---

## 🔄 FLUJO DE TRABAJO

### 1. Sistema Externo Crea Reserva:
```
Sistema de Reservas  →  POST /insert_reserva.php  →  Tabla 'reservas'
```

### 2. Pantalla Muestra Clientes:
```
Pantalla (PTY/MALEK)  →  Consulta cada 60s  →  API devuelve JSON  →  Actualiza lista
```

### 3. Limpieza (Opcional):
```
Fin del día  →  POST /borrar_reserva.php  →  Limpia tabla
```

---

## 🧪 TESTING

### Insertar Reserva de Prueba:

```bash
curl -X POST https://apppcr.net/reservas/insert_reserva.php \
  -H "Content-Type: application/json" \
  -d '{
    "commonid": 9999,
    "resnumber": "TEST-001",
    "customer": "CLIENTE DE PRUEBA",
    "dateout": "2025-10-20",
    "locationcodeout": "PTY",
    "resstatus": 20,
    "sourcecode": "210"
  }'
```

### Consultar Clientes PTY:

```bash
curl https://apppcr.net/reservas/consulta_clientes.php
```

### Consultar Clientes MALEK:

```bash
curl https://apppcr.net/reservas/consulta_clientes_malek.php
```

---

## 📱 VISUALIZACIÓN

### Pantalla PTY:
```
┌─────────────────────────────────┐
│      [LOGO AUTOMARKET]          │
│                                 │
│   BIENVENIDO / WELCOME          │
│                                 │
│   JUAN PEREZ                    │
│   MARIA RODRIGUEZ (verde)       │ ← VIP (210)
│   CARLOS SMITH                  │
│   ANA GARCIA (verde)            │ ← VIP (210)
│   ...                           │
│   [Scroll automático >25]       │
└─────────────────────────────────┘
```

---

## 🛠️ MANTENIMIENTO

### Limpiar Reservas Antiguas:
```sql
DELETE FROM reservas WHERE dateout < DATE_SUB(CURDATE(), INTERVAL 30 DAY);
```

### Limpiar Todas las Reservas:
```sql
TRUNCATE TABLE reservas;
```

### Ver Estadísticas del Día:
```sql
SELECT 
    locationcodeout,
    COUNT(*) as total,
    SUM(CASE WHEN sourcecode = '210' THEN 1 ELSE 0 END) as vip
FROM reservas
WHERE dateout = CURDATE() AND resstatus = 20
GROUP BY locationcodeout;
```

---

## ⚠️ NOTAS IMPORTANTES

1. **Auto-actualización:** Las pantallas se actualizan cada 60 segundos automáticamente
2. **Fecha del día:** Solo se muestran reservas con `dateout = HOY`
3. **Estado activo:** Solo se muestran reservas con `resstatus = 20`
4. **Duplicados:** Los nombres se agrupan (UPPER) para evitar duplicados
5. **VIP:** Clientes con `sourcecode = 210` se destacan en verde

---

## 🔒 SEGURIDAD

- ✅ Prepared statements (PDO)
- ✅ Validación de entrada
- ✅ Manejo de errores
- ✅ Content-Type headers
- ✅ Error logging

---

## 📞 SOPORTE

Para problemas técnicos o preguntas sobre el sistema, contactar al departamento de TI de Grupo PCR.

**Última actualización:** Octubre 2025  
**Versión:** 1.0.0

