# Subida de adjuntos — incapacidad / incapacidad_privada

## 1. Diagnóstico: por qué fallaba en algunos teléfonos

| Causa | Explicación |
|--------|-------------|
| **HEIC** | Fotos iPhone en «alta eficiencia»; muchos servidores no tienen ImageMagick+libheif. El código anterior podía **guardar HEIC sin convertir** → el navegador no previsualiza y parece «error». |
| **GD sin JPEG** | Si PHP-GD está sin soporte JPEG, `imagecreatefromjpeg` no existe o falla; el **fallback** guardaba el archivo crudo sin validar bien el tipo real. |
| **MIME / extensión** | `$_FILES['type']` lo envía el cliente (no confiable). Confiar solo en extensión permite disfrazar archivos. |
| **Subidas PHP** | `UPLOAD_ERR_INI_SIZE` / `PARTIAL` dejaban `file_add` vacío **sin mensaje** al usuario si no se entraba al `if (error === OK)`. |
| **Fotos muy grandes** | Muchos megapíxeles → `memory_limit` o timeout al abrir la imagen. |
| **Orientación EXIF** | Foto «de lado» en archivo; sin corregir, se guarda mal orientada (no es fallo de subida pero confunde). |

## 2. Qué hace el código ahora (`RRHHController.php`)

- **`rrhh_process_incapacidad_upload()`**: validación, conversión a **JPG optimizado** (salvo **PDF**), sin guardar HEIC crudo.
- **`rrhh_incapacidad_handle_file_field()`**: usada en **incapacidad** e **incapacidad_privada** para todos los códigos `UPLOAD_ERR_*`.
- Logs con prefijo `[incapacidad_upload][categoría]` vía `error_log()`.

### Validación

- `finfo_file` + `mime_content_type` + **`getimagesize`** para raster.
- PDF: bytes iniciales `%PDF` + MIME coherente.
- HEIC: MIME HEIC o `application/octet-stream` + extensión `.heic`/`.heif` (nombre de cliente solo como pista, no como única prueba).

### Imágenes raster (JPG/PNG/WebP)

- Salida: **JPG** (~calidad 82), lado largo máx. **1600 px**.
- **Imagick** si existe: `readImage(...[0])`, `autoOrient()`, `stripImage()`, `thumbnailImage`, JPEG.
- Si Imagick falla → **GD** (EXIF orientación en JPEG, flatten PNG/WebP sobre fondo blanco, resize, `imagejpeg`).
- Si no hay Imagick ni GD usable → error claro (no subir binario «a ciegas»).

### HEIC / HEIF

- El **usuario no debe cambiar** la cámara del iPhone: el servidor convierte a **JPG** para poder verla en el navegador.
- Orden: **Imagick (PHP)** → si falla, **CLI** en este orden: `magick`, `convert`, `heif-convert`, `ffmpeg` (rutas `/usr/bin/…` y sin prefijo).
- Tras conversión se **optimiza** (mismo flujo que JPG: máx. 1600 px, calidad ~82).
- Si todo falla, el mensaje indica a **administración** instalar `php-imagick` + **libheif**, o `imagemagick` / `libheif-examples` / `ffmpeg` (y que `exec` o `proc_open` no estén en `disable_functions`).

### Seguridad

- Nombre final: `incap_Ymd_His_<random>.jpg` o `…_pdf.pdf` (no se usa el nombre original como nombre de guardado).
- Directorio: `realpath` + comprobación de escritura.

### Límites

- Tamaño archivo: **20 MB** (`rrhh_incapacidad_max_upload_bytes()`).
- Dimensiones de origen: rechazo si algún lado > **12000 px** (anti picos de memoria).

## 3. PHP recomendado (`php.ini` o pool FPM)

```ini
upload_max_filesize = 24M
post_max_size = 28M
memory_limit = 256M
max_execution_time = 120
```

Ajustar según hosting; `post_max_size` debe ser **mayor** que `upload_max_filesize`.

## 4. Extensiones

- **fileinfo** (habitualmente habilitado) — para `finfo_*`.
- **GD** con **JPEG** (y PNG; WebP si hay fotos WebP).
- **exif** — orientación JPEG vía GD (opcional pero recomendado).
- **imagick** — HEIC y fallback robusto; en Linux suele hacer falta **libheif** / ImageMagick con soporte HEIC.

## 5. Rendimiento / estabilidad

- Redimensionar antes de mantener en disco reduce almacenamiento y acelera la vista en RRHH.
- Primer fotograma `[0]` evita fallos con secuencias HEIC/multi-página.
- Rechazo temprano por dimensiones evita picos de RAM.
