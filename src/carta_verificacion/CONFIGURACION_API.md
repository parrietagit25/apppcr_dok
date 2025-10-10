# 🔄 Configuración de Sincronización AWS ↔ GoDaddy vía API

## 🏗️ Arquitectura

```
┌─────────────────────────────────────┐
│  AWS (Docker)                       │
│                                     │
│  1. Genera carta con QR             │
│  2. Guarda en BD local ✅           │
│  3. Envía POST a GoDaddy 📤         │
└─────────────────────────────────────┘
                  │
                  │ HTTPS POST
                  │ (JSON + API Key)
                  ▼
┌─────────────────────────────────────┐
│  GoDaddy                            │
│                                     │
│  1. Recibe POST en API              │
│  2. Valida API Key                  │
│  3. Guarda en BD local ✅           │
│  4. Responde éxito/error            │
└─────────────────────────────────────┘
```

---

## 📦 **PASO 1: Subir API a GoDaddy**

### 1.1 Subir archivo

Subir `api_recibir_carta.php` a:
```
/public_html/carta/api_recibir_carta.php
```

### 1.2 Verificar permisos

```
Archivo: api_recibir_carta.php
Permisos: 644 (rw-r--r--)
```

### 1.3 Verificar URL

Debe ser accesible en:
```
https://grupopcr.com.pa/carta/api_recibir_carta.php
```

---

## 🔐 **PASO 2: Generar y Configurar API Key**

### 2.1 Generar clave segura

Ejecuta esto en tu terminal:

```bash
# En tu computadora
openssl rand -base64 32
```

O usa:
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Ejemplo de clave generada:
```
a7f3c9e2b1d4f8a6c5e9d2b7f4a8c3e6d1f9b2c7e4a5d8f3c6b9e1a4d7f2c5e8
```

### 2.2 Configurar en AMBOS servidores

#### **En AWS (Docker):**

Editar `src/carta_verificacion/config.php`:
```php
define('API_SECRET_KEY', 'a7f3c9e2b1d4f8a6c5e9d2b7f4a8c3e6d1f9b2c7e4a5d8f3c6b9e1a4d7f2c5e8');
```

#### **En GoDaddy:**

Editar `/public_html/carta/api_recibir_carta.php`:
```php
define('API_SECRET_KEY', 'a7f3c9e2b1d4f8a6c5e9d2b7f4a8c3e6d1f9b2c7e4a5d8f3c6b9e1a4d7f2c5e8');
```

**⚠️ IMPORTANTE:** La clave debe ser EXACTAMENTE la misma en ambos archivos.

---

## 🧪 **PASO 3: Probar la API**

### 3.1 Probar desde línea de comandos

```bash
curl -X POST https://grupopcr.com.pa/carta/api_recibir_carta.php \
  -H "Content-Type: application/json" \
  -H "X-API-Key: TU_API_KEY_AQUI" \
  -d '{
    "carta": {
      "id_carta_original": 999,
      "hash_verificacion": "test123",
      "token_qr": "testtoken",
      "codigo_empleado": "TEST",
      "nombre": "Test",
      "apellido": "API",
      "cedula": "0-0-0",
      "cargo": "Tester",
      "fecha_ingreso": "2025-01-01",
      "fecha_emision": "2025-10-10 12:00:00",
      "estado": "activa"
    },
    "deducciones": []
  }'
```

**Respuesta esperada:**
```json
{
  "success": true,
  "message": "Carta registrada exitosamente",
  "carta_id": 2,
  "id_carta_original": 999
}
```

### 3.2 Verificar en la base de datos de GoDaddy

```sql
SELECT * FROM cartas_trabajo_verificacion WHERE id_carta_original = 999;
```

Si aparece el registro = ✅ API funcionando correctamente

---

## 🚀 **PASO 4: Generar carta real**

1. Ir al sistema de RRHH
2. Aprobar una carta de trabajo
3. Verificar logs:

**En AWS:**
```bash
docker logs apppcr_php | tail -20
```

Deberías ver:
```
Carta sincronizada con GoDaddy. ID local: X, ID remoto: Y
```

**En GoDaddy:**

Verificar que el registro exista en la BD.

---

## 🔍 **PASO 5: Probar verificación**

1. Generar carta con QR
2. Escanear QR con celular
3. Debe abrir: `https://grupopcr.com.pa/carta/verificar.php?token=...`
4. Debe mostrar los datos correctamente ✅

---

## 🐛 **Troubleshooting**

### Error: "No autorizado" (401)

**Causa:** La API Key no coincide

**Solución:**
```bash
# Verificar en AWS
cat src/carta_verificacion/config.php | grep API_SECRET_KEY

# Verificar en GoDaddy (via cPanel)
# Buscar en api_recibir_carta.php la línea:
# define('API_SECRET_KEY', '...');
```

Deben ser **idénticas**.

### Error: "Error de conexión con servidor de verificación"

**Causa:** No se puede conectar a GoDaddy

**Solución:**
```bash
# Probar conectividad desde Docker
docker exec -it apppcr_php curl -I https://grupopcr.com.pa/carta/api_recibir_carta.php
```

### Error: "Error al guardar en base de datos"

**Causa:** Tabla no existe o estructura incorrecta

**Solución:**
```bash
# Verificar tablas en GoDaddy
# cPanel → phpMyAdmin → verificar que existan:
# - cartas_trabajo_verificacion
# - cartas_deducciones
# - cartas_verificaciones_log
```

### ADVERTENCIA en logs: "No se pudo sincronizar con GoDaddy"

**Impacto:** La carta se guarda localmente pero no en GoDaddy

**Qué hacer:**
1. La carta ya está generada y el PDF enviado ✅
2. Revisar logs para ver el error específico
3. Corregir el problema
4. Las futuras cartas se sincronizarán correctamente

**El sistema es resiliente:** Si falla la sincronización, la carta se genera igualmente.

---

## 📊 **Verificar sincronización**

### Consulta en AWS (Docker):

```bash
docker exec -it apppcr_db mysql -u root -prootpass apppcr -e "
SELECT id, id_carta_original, codigo_empleado, nombre, apellido, fecha_emision 
FROM cartas_trabajo_verificacion 
ORDER BY id DESC LIMIT 5;
"
```

### Consulta en GoDaddy (cPanel → phpMyAdmin):

```sql
SELECT id, id_carta_original, codigo_empleado, nombre, apellido, fecha_emision 
FROM cartas_trabajo_verificacion 
ORDER BY id DESC LIMIT 5;
```

Los registros con el mismo `id_carta_original` deben existir en ambas bases de datos.

---

## ✅ **Checklist de Configuración**

- [ ] Archivo `api_recibir_carta.php` subido a GoDaddy
- [ ] Permisos configurados (644)
- [ ] API Key generada y configurada en ambos servidores
- [ ] API Key coincide en AWS y GoDaddy
- [ ] Tablas creadas en GoDaddy
- [ ] Test con cURL exitoso
- [ ] Carta de prueba generada y sincronizada
- [ ] QR escaneado y verifica correctamente

---

## 🔒 **Seguridad**

### API Key
- ✅ Usar claves largas (mínimo 32 caracteres)
- ✅ Cambiar la clave por defecto
- ✅ No compartir la clave en repositorios públicos
- ✅ Usar HTTPS (nunca HTTP)

### En producción
- ✅ Eliminar mensajes de error detallados en `api_recibir_carta.php`
- ✅ Monitorear logs de acceso
- ✅ Limitar intentos por IP (opcional)

---

## 📞 **Soporte**

Si tienes problemas:
1. Revisar logs de AWS: `docker logs apppcr_php`
2. Revisar logs de GoDaddy: cPanel → Errors
3. Verificar API Key en ambos lados
4. Probar con cURL manualmente

**Última actualización:** 2025-10-10

