# 🚀 GUÍA DE INSTALACIÓN - SISTEMA DE RESERVAS

## 📋 REQUISITOS PREVIOS

- ✅ Docker + Docker Compose funcionando
- ✅ Python 3.x instalado en el servidor
- ✅ Acceso SSH al servidor AWS
- ✅ Acceso a la API de BARS Cloud

---

## 🗂️ UBICACIÓN EN EL SERVIDOR

```
Servidor: AWS EC2 (ip-172-31-17-232)
Path: /home/ubuntu/apppcr/src/reservas/
URL: https://apppcr.net/reservas/
```

---

## ⚙️ PASO 1: CREAR LA TABLA EN MYSQL

### Opción A: Desde el servidor

```bash
# Conectar al servidor
ssh ubuntu@IP_AWS

# Navegar al directorio
cd /home/ubuntu/apppcr/src/reservas/

# Ejecutar script SQL
docker exec -i apppcr_db mysql -uappuser -papppass apppcr < crear_tabla_reservas.sql
```

### Opción B: Manualmente

```bash
# Conectar al contenedor MySQL
docker exec -it apppcr_db mysql -uappuser -papppass apppcr

# Dentro de MySQL
SOURCE /var/www/html/reservas/crear_tabla_reservas.sql;

# Verificar
DESCRIBE reservas;
```

---

## 🐍 PASO 2: CONFIGURAR PYTHON

### Instalar dependencias:

```bash
# Instalar pip si no lo tienes
sudo apt-get update
sudo apt-get install python3-pip -y

# Instalar requests
pip3 install requests
```

### Dar permisos de ejecución al script:

```bash
cd /home/ubuntu/apppcr/src/reservas/
chmod +x sincronizar_reservas.py
```

---

## 🧪 PASO 3: PROBAR LA SINCRONIZACIÓN

### Ejecutar manualmente:

```bash
cd /home/ubuntu/apppcr/src/reservas/
python3 sincronizar_reservas.py
```

**Salida esperada:**

```
============================================================
🚗 SINCRONIZACIÓN DE RESERVAS AUTOMARKET
============================================================
📅 Fecha: 2025-10-20

📡 Consultando API BARS Cloud para fecha: 2025-10-20
   URL: https://cq1e.barscloud.com:612/dolPanamaRW/queryapi/...
✅ Obtenidas 25 reservas de BARS Cloud

📤 Enviando 25 reservas a AppPCR...
------------------------------------------------------------
1/25 - JUAN PEREZ (PTY)... ✅
2/25 - MARIA RODRIGUEZ (PTY)... ✅
3/25 - CARLOS SMITH (MALEK)... ✅
...
------------------------------------------------------------

📊 RESUMEN:
   ✅ Exitosas: 25
   ❌ Fallidas:  0
   📊 Total:     25

============================================================
```

---

## ⏰ PASO 4: AUTOMATIZAR CON CRON

### Configurar ejecución automática diaria:

```bash
# Editar crontab
crontab -e

# Agregar línea (ejecuta todos los días a las 6:00 AM)
0 6 * * * cd /home/ubuntu/apppcr/src/reservas && /usr/bin/python3 sincronizar_reservas.py >> /var/log/reservas_sync.log 2>&1

# O ejecutar cada hora durante horario laboral (8 AM - 6 PM)
0 8-18 * * * cd /home/ubuntu/apppcr/src/reservas && /usr/bin/python3 sincronizar_reservas.py >> /var/log/reservas_sync.log 2>&1
```

### Ver logs:

```bash
# Ver log de sincronización
tail -f /var/log/reservas_sync.log

# Ver solo errores
grep "❌" /var/log/reservas_sync.log
```

---

## 🔧 PASO 5: CONFIGURAR LIMPIEZA AUTOMÁTICA (OPCIONAL)

Si quieres limpiar las reservas al inicio de cada día:

### Editar `sincronizar_reservas.py`:

```python
# Línea ~92 - Descomentar esta línea:
limpiar_reservas_antiguas()
```

O crear un cron separado para limpiar:

```bash
# Limpiar tabla todos los días a las 5:00 AM (antes de sincronizar)
0 5 * * * curl -X POST https://apppcr.net/reservas/borrar_reserva.php
```

---

## 📺 PASO 6: VERIFICAR PANTALLAS

### Abrir en navegadores/pantallas:

**Pantalla PTY:**
```
https://apppcr.net/reservas/reservas_pty.php
```

**Pantalla MALEK:**
```
https://apppcr.net/reservas/reservas_malek.php
```

**Características:**
- Auto-refresh cada 60 segundos
- Clientes VIP (sourcecode 210) en verde
- PTY: Scroll automático si hay +25 clientes

---

## 🧪 TESTING Y VALIDACIÓN

### 1. Probar API manualmente:

```bash
# Consultar clientes PTY
curl https://apppcr.net/reservas/consulta_clientes.php

# Consultar clientes MALEK
curl https://apppcr.net/reservas/consulta_clientes_malek.php
```

### 2. Insertar reserva de prueba:

```bash
curl -X POST https://apppcr.net/reservas/insert_reserva.php \
  -H "Content-Type: application/json" \
  -d '{
    "commonid": 99999,
    "resnumber": "TEST-001",
    "customer": "CLIENTE DE PRUEBA",
    "dateout": "2025-10-20",
    "locationcodeout": "PTY",
    "resstatus": 20,
    "sourcecode": "210"
  }'
```

### 3. Verificar en base de datos:

```bash
docker exec -it apppcr_db mysql -uappuser -papppass apppcr -e \
  "SELECT COUNT(*) as total FROM reservas WHERE dateout = CURDATE();"
```

---

## 🔍 TROUBLESHOOTING

### Problema: Script Python falla

**Verificar:**
```bash
# Python instalado
python3 --version

# Requests instalado
pip3 list | grep requests

# Conectividad
curl -k https://cq1e.barscloud.com:612/
```

### Problema: No se insertan reservas

**Verificar:**
```bash
# Tabla existe
docker exec -it apppcr_db mysql -uappuser -papppass apppcr -e "DESCRIBE reservas;"

# API funciona
curl https://apppcr.net/reservas/insert_reserva.php

# Logs de Docker
docker logs apppcr_php
```

### Problema: Pantallas no muestran clientes

**Verificar:**
```bash
# Hay datos en la tabla
docker exec -it apppcr_db mysql -uappuser -papppass apppcr -e \
  "SELECT * FROM reservas WHERE dateout = CURDATE() LIMIT 5;"

# APIs devuelven datos
curl https://apppcr.net/reservas/consulta_clientes.php | jq
```

---

## 📊 MONITOREO

### Ver estado del sistema:

```bash
# Reservas de hoy
docker exec -it apppcr_db mysql -uappuser -papppass apppcr -e \
  "SELECT locationcodeout, COUNT(*) as total FROM reservas 
   WHERE dateout = CURDATE() AND resstatus = 20 
   GROUP BY locationcodeout;"

# Última sincronización (desde logs)
tail -20 /var/log/reservas_sync.log

# Verificar cron
crontab -l
```

---

## 🆘 COMANDOS ÚTILES

```bash
# Reiniciar contenedores Docker
cd /home/ubuntu/apppcr
docker-compose restart

# Ver logs en tiempo real
docker logs -f apppcr_php

# Limpiar todas las reservas
curl -X POST https://apppcr.net/reservas/borrar_reserva.php

# Sincronizar manualmente
cd /home/ubuntu/apppcr/src/reservas
python3 sincronizar_reservas.py

# Ver reservas en BD
docker exec -it apppcr_db mysql -uappuser -papppass apppcr \
  -e "SELECT * FROM reservas WHERE dateout = CURDATE();"
```

---

## ✅ CHECKLIST DE INSTALACIÓN

- [ ] Tabla `reservas` creada en MySQL
- [ ] Python 3 y requests instalados
- [ ] Script `sincronizar_reservas.py` con permisos de ejecución
- [ ] Sincronización manual probada exitosamente
- [ ] Cron job configurado
- [ ] Pantallas PTY y MALEK accesibles
- [ ] APIs devolviendo datos correctamente
- [ ] Logs configurados y funcionando

---

## 📞 SOPORTE

Para problemas técnicos:
- Revisar logs: `/var/log/reservas_sync.log`
- Contactar TI de Grupo PCR

---

**Última actualización:** Octubre 2025  
**Versión:** 1.0.0

