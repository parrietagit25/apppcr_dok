# ⚡ RESUMEN RÁPIDO - CONFIGURACIÓN DE RESERVAS

## 🎯 INFORMACIÓN CLAVE

### URLs del Sistema:
```
✅ URL Principal:        https://apppcr.net/
✅ Pantalla PTY:        https://apppcr.net/reservas/reservas_pty.php
✅ Pantalla MALEK:      https://apppcr.net/reservas/reservas_malek.php
✅ API Inserción:       https://apppcr.net/reservas/insert_reserva.php
✅ API Consulta PTY:    https://apppcr.net/reservas/consulta_clientes.php
✅ API Consulta MALEK:  https://apppcr.net/reservas/consulta_clientes_malek.php

❌ URL ANTIGUA (NO USAR): https://grupopcr.com.pa/reservas/
```

### Servidor:
```
Host:  AWS EC2 (ip-172-31-17-232)
Path:  /home/ubuntu/apppcr/src/reservas/
User:  ubuntu
```

### Base de Datos (Docker):
```
Container: apppcr_db
Host:      db
Database:  apppcr
User:      appuser
Password:  apppass
```

---

## 📝 CAMBIOS REALIZADOS

### ✅ 1. Credenciales Actualizadas (4 archivos PHP):
- `insert_reserva.php` ✅
- `consulta_clientes.php` ✅
- `consulta_clientes_malek.php` ✅
- `borrar_reserva.php` ✅

**Cambio:**
```php
// ANTES (incorrecto):
$host = "localhost";
$usuario = "pedropcr";
$contraseña = 'elchamo1787$$$';

// AHORA (correcto):
$host = "db";  // Contenedor Docker
$usuario = "appuser";
$contraseña = "apppass";
```

### ✅ 2. Script Python Actualizado:

**Archivo nuevo:** `sincronizar_reservas.py`

**Cambio principal:**
```python
# ANTES (incorrecto):
php_url = "https://grupopcr.com.pa/reservas/insert_reserva.php"

# AHORA (correcto):
APPPCR_API_URL = "https://apppcr.net/reservas/insert_reserva.php"
```

### ✅ 3. Archivos Nuevos Creados:
- `crear_tabla_reservas.sql` - Script para crear tabla
- `sincronizar_reservas.py` - Script Python mejorado
- `README.md` - Documentación completa
- `INSTALACION.md` - Guía paso a paso
- `RESUMEN_CONFIGURACION.md` - Este archivo

---

## 🚀 INSTALACIÓN RÁPIDA (3 PASOS)

### PASO 1: Crear tabla MySQL
```bash
ssh ubuntu@IP_AWS
cd /home/ubuntu/apppcr/src/reservas/
docker exec -i apppcr_db mysql -uappuser -papppass apppcr < crear_tabla_reservas.sql
```

### PASO 2: Probar sincronización Python
```bash
# Asegurarse que requests está instalado
pip3 install requests

# Ejecutar script
python3 sincronizar_reservas.py
```

### PASO 3: Configurar Cron (ejecutar diario)
```bash
crontab -e

# Agregar esta línea (ejecuta a las 6 AM diario):
0 6 * * * cd /home/ubuntu/apppcr/src/reservas && /usr/bin/python3 sincronizar_reservas.py >> /var/log/reservas_sync.log 2>&1
```

---

## 🧪 VERIFICACIÓN RÁPIDA

### Comprobar que todo funciona:

```bash
# 1. Tabla existe
docker exec -it apppcr_db mysql -uappuser -papppass apppcr -e "DESCRIBE reservas;"

# 2. API funciona
curl https://apppcr.net/reservas/insert_reserva.php

# 3. Python funciona
cd /home/ubuntu/apppcr/src/reservas && python3 sincronizar_reservas.py

# 4. Hay datos
curl https://apppcr.net/reservas/consulta_clientes.php

# 5. Pantallas funcionan
# Abrir en navegador: https://apppcr.net/reservas/reservas_pty.php
```

---

## 📊 ARQUITECTURA DEL SISTEMA

```
┌─────────────────────┐
│   BARS Cloud API    │ (Sistema externo de rentas)
│ cq1e.barscloud.com  │
└──────────┬──────────┘
           │
           │ 1. Script Python consulta
           │    cada día a las 6 AM
           ↓
┌─────────────────────────────┐
│ sincronizar_reservas.py     │
│ (Servidor AWS)              │
└──────────┬──────────────────┘
           │
           │ 2. Envía POST con reservas
           ↓
┌─────────────────────────────┐
│ insert_reserva.php          │
│ https://apppcr.net/reservas │
└──────────┬──────────────────┘
           │
           │ 3. Inserta en tabla
           ↓
┌─────────────────────────────┐
│  MySQL (Docker)             │
│  Tabla: reservas            │
└──────────┬──────────────────┘
           │
           │ 4. Consultan cada 60s
           ↓
┌─────────────────────────────┐
│  Pantallas (navegadores)    │
│  - reservas_pty.php         │
│  - reservas_malek.php       │
└─────────────────────────────┘
```

---

## 🔄 FLUJO DIARIO AUTOMÁTICO

```
06:00 AM → Script Python se ejecuta (cron)
           ↓
           Consulta BARS Cloud API
           ↓
           Obtiene reservas del día
           ↓
           Envía a https://apppcr.net/reservas/insert_reserva.php
           ↓
           Se insertan en tabla MySQL
           ↓
           Pantallas se actualizan automáticamente (cada 60s)
```

---

## ⚠️ IMPORTANTE

### ANTES DE MIGRAR TU SCRIPT ACTUAL:

Tu script actual dice:
```python
php_url = "https://grupopcr.com.pa/reservas/insert_reserva.php"  # ❌ INCORRECTO
```

**Debes cambiarlo a:**
```python
php_url = "https://apppcr.net/reservas/insert_reserva.php"  # ✅ CORRECTO
```

**O mejor aún:** Usar el nuevo script `sincronizar_reservas.py` que ya tiene:
- ✅ URL correcta
- ✅ Mejor manejo de errores
- ✅ Logging detallado
- ✅ Resumen de resultados
- ✅ Validaciones

---

## 📂 ESTRUCTURA DE ARCHIVOS

```
/home/ubuntu/apppcr/src/reservas/
├── 1.png                          ← Logo Automarket
├── reservas_pty.php               ← Pantalla PTY
├── reservas_malek.php             ← Pantalla MALEK
├── insert_reserva.php             ← API ✅ ACTUALIZADA
├── consulta_clientes.php          ← API ✅ ACTUALIZADA
├── consulta_clientes_malek.php    ← API ✅ ACTUALIZADA
├── borrar_reserva.php             ← API ✅ ACTUALIZADA
├── crear_tabla_reservas.sql       ← Script SQL ✨ NUEVO
├── sincronizar_reservas.py        ← Script Python ✨ NUEVO
├── README.md                      ← Documentación ✨ NUEVO
├── INSTALACION.md                 ← Guía instalación ✨ NUEVO
└── RESUMEN_CONFIGURACION.md       ← Este archivo ✨ NUEVO
```

---

## 🎯 PRÓXIMOS PASOS

1. ✅ **Subir archivos al servidor** (ya están en `/home/ubuntu/apppcr/src/reservas/`)
2. ⏳ **Ejecutar script SQL** para crear tabla
3. ⏳ **Probar script Python** manualmente
4. ⏳ **Configurar Cron** para ejecución automática
5. ⏳ **Verificar pantallas** en navegadores

---

## 📞 COMANDOS DE EMERGENCIA

```bash
# Reiniciar todo
docker-compose restart

# Ver logs
docker logs -f apppcr_php

# Limpiar reservas
curl -X POST https://apppcr.net/reservas/borrar_reserva.php

# Sincronizar ahora
cd /home/ubuntu/apppcr/src/reservas && python3 sincronizar_reservas.py

# Ver reservas actuales
docker exec -it apppcr_db mysql -uappuser -papppass apppcr \
  -e "SELECT COUNT(*) FROM reservas WHERE dateout = CURDATE();"
```

---

**¡Sistema listo para producción!** 🚀

Solo falta ejecutar los 3 pasos de instalación y configurar el cron.

