# Recomendaciones para subida de archivos

## Cambios ya aplicados en código

1. **No guardar registro si el archivo falla**  
   En solicitud de permiso (R-Permiso y Permiso-old), si el usuario adjunta un archivo y la subida falla (tamaño, permisos, disco, etc.), **ya no se guarda la solicitud**. Se muestra un mensaje claro y se vuelve a cargar el formulario.

2. **Mensajes por tipo de error**  
   Se interpreta `$_FILES['...']['error']` y se muestra un texto entendible (archivo muy grande, subida parcial, etc.).

3. **Orden recomendado**  
   Primero se valida y se mueve el archivo; solo si eso es correcto (o si no había archivo) se llama a `insertar_permiso`. Así se evita tener registros sin archivo cuando el usuario sí lo adjuntó.

---

## Configuración del servidor (PHP)

Para que las subidas no fallen por límites:

1. **php.ini** (o `.user.ini` / directivas en Apache):
   - `upload_max_filesize` = 10M o más (según necesidad).
   - `post_max_size` ≥ `upload_max_filesize` (por ejemplo 12M si upload es 10M).
   - `max_execution_time` = 60 o más si los archivos son grandes o la red es lenta.

2. **Docker**  
   Si usas el Dockerfile del proyecto, puedes añadir:
   ```dockerfile
   RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/uploads.ini && \
       echo "post_max_size = 12M" >> /usr/local/etc/php/conf.d/uploads.ini
   ```

3. **Comprobar en la app**  
   En algún PHP de diagnóstico (o temporalmente en una vista):
   ```php
   echo ini_get('upload_max_filesize'); // ej: 10M
   echo ini_get('post_max_size');
   ```

---

## Formularios

- El formulario debe llevar **`enctype="multipart/form-data"`** (en solicitud de permiso ya está).
- El `<input type="file">` debe tener `name="archivo_adjunto"` (o el nombre que use el controlador).

---

## Si el archivo es opcional

- Si el usuario **no** elige archivo (`UPLOAD_ERR_NO_FILE`), se guarda el registro con `archivo_adjunto = null`.
- Si el usuario **sí** elige archivo y la subida falla, **no** se guarda el registro y se muestra el error (comportamiento actual).

---

## Resumen

| Situación                         | Antes              | Ahora                    |
|----------------------------------|--------------------|---------------------------|
| Usuario adjunta y subida falla   | Se guardaba sin archivo | No se guarda, se muestra error |
| Usuario no adjunta               | Se guardaba bien   | Se guarda bien            |
| Usuario adjunta y subida OK      | Se guardaba bien   | Se guarda bien            |
