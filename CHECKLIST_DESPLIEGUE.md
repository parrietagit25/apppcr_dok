# ✅ Checklist de Despliegue - Sistema de Verificación de Cartas

## 📋 Pre-Despliegue (Completado)

- [x] Tablas creadas en BD externa
- [x] Usuario de BD configurado
- [x] Código implementado y probado localmente
- [x] Documentación completa
- [x] Archivos listos para subir

---

## 🚀 Despliegue al Servidor Externo

### Paso 1: Subir Archivos
- [ ] Conectar al servidor (FTP/SSH/cPanel)
- [ ] Crear carpeta `/public_html/carta/`
- [ ] Subir todos los archivos de `carta_verificacion/`:
  - [ ] `config.php`
  - [ ] `DatabaseExternal.php`
  - [ ] `CartaVerificacionService.php`
  - [ ] `verificar.php`
  - [ ] `index.php`
  - [ ] `.htaccess`
  - [ ] `README.md`
  - [ ] `INSTRUCCIONES_DESPLIEGUE.md`
  - [ ] `test_sistema.php` (temporal)

### Paso 2: Configurar Permisos
- [ ] `chmod 755` a la carpeta `/public_html/carta/`
- [ ] `chmod 644` a todos los archivos `.php`
- [ ] `chmod 644` al archivo `.htaccess`

### Paso 3: Verificar Configuración
- [ ] Revisar `config.php`:
  - [ ] Host de BD correcto (localhost o IP)
  - [ ] Nombre de BD correcto (apppcr)
  - [ ] Usuario correcto (pedropcr)
  - [ ] Contraseña correcta
  - [ ] URL base correcta (https://grupopcr.com.pa/carta/)

### Paso 4: Probar Conexión a BD
- [ ] Acceder a: `https://grupopcr.com.pa/carta/test_sistema.php`
- [ ] Verificar todos los tests pasen
- [ ] Test 1: Configuración ✅
- [ ] Test 2: Conexión BD ✅
- [ ] Test 3: Servicio ✅
- [ ] Test 4: API QR ✅
- [ ] Test 5: Inserción (opcional) ✅

### Paso 5: Probar Inserción de Prueba
- [ ] En `test_sistema.php`, clic en "Insertar Registro de Prueba"
- [ ] Verificar que se inserte correctamente
- [ ] Copiar la URL de verificación generada
- [ ] Abrir URL en navegador
- [ ] Verificar que muestre página de verificación con datos

### Paso 6: Limpiar Archivos de Prueba
- [ ] Eliminar `/public_html/carta/test_sistema.php`
- [ ] Eliminar registro de prueba de la BD (opcional):
  ```sql
  DELETE FROM cartas_trabajo_verificacion WHERE codigo_empleado = 'TEST001';
  ```

---

## 🧪 Pruebas del Sistema Completo

### Paso 7: Generar Carta Real de Prueba
- [ ] Ir al sistema de RRHH
- [ ] Seleccionar solicitud de carta de trabajo
- [ ] Completar formulario con datos reales
- [ ] Hacer clic en "Enviar Carta PDF"
- [ ] Verificar mensaje de éxito

### Paso 8: Verificar PDF Generado
- [ ] Abrir el PDF generado
- [ ] Verificar que contenga:
  - [ ] Logo de Grupo PCR
  - [ ] Datos del colaborador correctos
  - [ ] Código QR en esquina superior derecha
  - [ ] Mensaje de verificación al pie
  - [ ] Hash de verificación

### Paso 9: Probar QR
- [ ] Escanear QR con celular
- [ ] Verificar que abra la URL correcta
- [ ] Verificar que muestre página de verificación
- [ ] Verificar que los datos coincidan con la carta
- [ ] Verificar estado "ACTIVA"

### Paso 10: Verificar Base de Datos
- [ ] Conectar a BD externa
- [ ] Ejecutar:
  ```sql
  SELECT * FROM cartas_trabajo_verificacion ORDER BY id DESC LIMIT 1;
  ```
- [ ] Verificar que existan todos los datos
- [ ] Verificar deducciones:
  ```sql
  SELECT * FROM cartas_deducciones WHERE carta_id = (SELECT MAX(id) FROM cartas_trabajo_verificacion);
  ```
- [ ] Verificar log de verificación:
  ```sql
  SELECT * FROM cartas_verificaciones_log ORDER BY id DESC LIMIT 1;
  ```

---

## 🔧 Configuración de Producción

### Paso 11: Activar Emails de Producción
- [ ] Abrir `src/app/controllers/RRHHController.php`
- [ ] Ir a línea 366
- [ ] Comentar email de prueba:
  ```php
  //$copias = ["pedroarrieta25@hotmail.com"];
  ```
- [ ] Descomentar emails de producción:
  ```php
  $copias = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa"];
  ```
- [ ] Guardar archivo

### Paso 12: Seguridad Adicional
- [ ] Cambiar clave de encriptación en `config.php`:
  ```bash
  # Generar nueva clave:
  openssl rand -base64 48
  ```
- [ ] Actualizar `ENCRYPTION_KEY` con la nueva clave
- [ ] Guardar archivo

### Paso 13: Verificar Seguridad
- [ ] Intentar acceder a: `https://grupopcr.com.pa/carta/config.php`
  - [ ] Debe mostrar Error 403 ✅
- [ ] Intentar acceder a: `https://grupopcr.com.pa/carta/DatabaseExternal.php`
  - [ ] Debe mostrar Error 403 ✅
- [ ] Intentar acceder a: `https://grupopcr.com.pa/carta/CartaVerificacionService.php`
  - [ ] Debe mostrar Error 403 ✅
- [ ] Acceder a: `https://grupopcr.com.pa/carta/`
  - [ ] Debe redirigir a página principal ✅

---

## 📊 Monitoreo Post-Despliegue

### Paso 14: Monitoreo Inicial (Primera Semana)
- [ ] Revisar logs de error diariamente
- [ ] Monitorear cantidad de verificaciones:
  ```sql
  SELECT COUNT(*) FROM cartas_verificaciones_log WHERE DATE(fecha_verificacion) = CURDATE();
  ```
- [ ] Verificar cartas generadas:
  ```sql
  SELECT COUNT(*) FROM cartas_trabajo_verificacion WHERE DATE(fecha_emision) = CURDATE();
  ```
- [ ] Revisar emails enviados (consultar con destinatarios)

### Paso 15: Backup de Base de Datos
- [ ] Configurar backup automático diario de `apppcr`
- [ ] Probar restauración de backup
- [ ] Documentar proceso de backup

### Paso 16: Documentación para RRHH
- [ ] Crear manual de usuario para RRHH
- [ ] Capacitar al personal sobre:
  - [ ] Nueva funcionalidad de QR
  - [ ] Cómo verificar cartas
  - [ ] Qué hacer si hay problemas
- [ ] Establecer protocolo de soporte

---

## 🎯 Criterios de Éxito

El sistema estará completamente operativo cuando:

- ✅ Todas las cartas generadas incluyan QR
- ✅ Los QR sean escaneables y funcionen correctamente
- ✅ La página de verificación muestre datos correctos
- ✅ Los datos se almacenen en BD externa
- ✅ El log de verificaciones registre accesos
- ✅ Los emails se envíen correctamente
- ✅ No haya errores en logs de servidor
- ✅ Los archivos sensibles estén protegidos

---

## 📝 Información de Contacto

### Para Problemas Técnicos:
- **Servidor Web:** Panel de administración / soporte hosting
- **Base de Datos:** Administrador de BD
- **Código:** Desarrollador / TI

### Para Uso del Sistema:
- **RRHH Principal:** sofia.macias@grupopcr.com.pa
- **RRHH Soporte:** abi.pineda@grupopcr.com.pa

---

## 🔄 Rollback (Si algo sale mal)

Si necesitas revertir los cambios:

### Sistema de Verificación (Servidor Externo)
1. Eliminar carpeta `/public_html/carta/`
2. Los datos en BD se mantienen para auditoría

### Sistema Interno (App RRHH)
1. Restaurar versión anterior de `RRHHController.php` desde Git:
   ```bash
   git checkout HEAD~1 -- src/app/controllers/RRHHController.php
   ```
2. Las cartas seguirán generándose sin QR

---

## ✅ Checklist Final

Antes de dar por completado el despliegue, verificar:

- [ ] ✅ Todos los archivos subidos al servidor
- [ ] ✅ Permisos configurados correctamente
- [ ] ✅ Conexión a BD funcionando
- [ ] ✅ Tests pasando correctamente
- [ ] ✅ Carta de prueba generada con QR
- [ ] ✅ QR escaneado y verificado
- [ ] ✅ Datos en BD externa correctos
- [ ] ✅ Emails de producción activados
- [ ] ✅ Archivos de prueba eliminados
- [ ] ✅ Seguridad verificada
- [ ] ✅ Backup configurado
- [ ] ✅ Personal capacitado

---

## 🎉 ¡Sistema en Producción!

Una vez completado este checklist, el sistema está oficialmente en producción y listo para uso.

**Fecha de activación:** _______________  
**Responsable:** _______________  
**Firma:** _______________

---

## 📞 Soporte 24/7

En caso de emergencia fuera de horario:
- Revisar logs en `/public_html/carta/error_log`
- Revisar logs de Apache/Nginx
- Contactar a soporte técnico de emergencia

---

**Última actualización:** 10 de octubre, 2025  
**Versión del documento:** 1.0

