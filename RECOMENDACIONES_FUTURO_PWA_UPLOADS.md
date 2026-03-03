# Recomendaciones para una futura reestructuración del sistema (PWA + subidas robustas)

Objetivo: **PWA con funcionamiento óptimo** y **subidas de archivos que no fallen** (sin registros sin archivo, con reintentos y buena UX).

---

## 1. Estrategia de subidas (lo más importante)

### Problema actual
- Un solo request: formulario + archivo. Si el archivo falla (tamaño, timeout, red), el backend puede guardar el registro sin archivo o rechazar todo.
- No hay progreso visible ni reintento por chunk.

### En el futuro: flujo en dos fases

1. **Subir primero solo el archivo** (endpoint dedicado):
   - Subida por **chunks** (trozos) o protocolo **resumable** (ej. TUS).
   - El servidor responde con un **identificador del archivo** (ej. `file_id` o URL) solo cuando la subida terminó bien.
   - Si falla, no se guarda nada en BD; el usuario puede reintentar solo la subida.

2. **Después, enviar el formulario** (datos del permiso/solicitud):
   - El frontend envía: datos del formulario + `file_id` (referencia al archivo ya subido).
   - El backend crea el registro en BD **solo** si el `file_id` es válido (o es opcional).
   - Así **nunca** queda un registro “con archivo” sin archivo real: o existe el archivo y entonces se guarda la referencia, o no se guarda el registro.

Ventajas:
- Sin depender de `post_max_size` para el formulario.
- Reintentos solo para el archivo; progreso y mejor UX.
- Base de datos y almacenamiento de archivos siempre coherentes.

---

## 2. Tecnologías recomendadas

### Backend (API)

| Opción | Uso recomendado |
|--------|-------------------|
| **Laravel (PHP)** | Si quieren seguir en PHP: API REST, colas, almacenamiento (local/S3), validación y buena estructura. |
| **Node.js (Express/Fastify) + TypeScript** | API ligera; muchas librerías para uploads por chunks (TUS, multer con streaming). |
| **.NET Core / ASP.NET Core** | Entorno empresarial, buen soporte para uploads grandes y almacenamiento. |

Recomendación práctica: **Laravel** si el equipo es PHP; **Node + Fastify o Express** si prefieren JavaScript en backend.

### Subida de archivos en backend

- **Protocolo TUS** (resumable uploads): [tus.io](https://tus.io). Servidor TUS en PHP: `ankitpokhrel/tus-php`; en Node: `tus-node-server` o middleware.
- **Chunked uploads a medida**: endpoint que recibe trozos (`chunk index`, `total chunks`, `file id`), los escribe en disco/cache, y al recibir el último chunk responde con `file_id`. Laravel tiene paquetes para esto; en Node es fácil con streams.
- **Almacenamiento**: ficheros en **disco local** (con directorios por tipo/tenant) o en **S3-compatible** (MinIO, AWS S3, etc.). No guardar en la misma petición que el formulario; solo guardar referencia en BD tras subida exitosa.

### Frontend (PWA)

| Tecnología | Comentario |
|------------|------------|
| **React** o **Vue** o **Svelte** | SPA con estado claro; fácil integrar librerías de subida y PWA. |
| **TypeScript** | Menos errores y mejor mantenimiento. |
| **Vite** | Build rápido y buena experiencia de desarrollo. |

### Librerías de subida en frontend

- **Uppy** ([uppy.io](https://uppy.io)): subida con progreso, reintentos, TUS opcional, multiarchivo, buena UX. Muy recomendable.
- **tus-js-client**: si solo quieren TUS en el navegador.
- **react-dropzone** / **vue-dropzone**: para arrastrar/soltar; la lógica de “subir por chunks” o TUS se combina con Uppy o con tu propio servicio.

### PWA

- **Workbox** (Google): service worker, cache de assets, estrategias (network-first para API, cache-first para estático).
- **Offline**: formularios se pueden guardar en **IndexedDB** o LocalStorage y enviar cuando haya red; las subidas de archivos es mejor hacerlas **solo con conexión** y mostrar mensaje claro si no hay red.
- **Instalable**: `manifest.json` + service worker + HTTPS, como ya tienen en mente para PWA.

### Base de datos

- **MySQL** o **PostgreSQL** (como ahora): solo guardar en BD la **referencia** al archivo (path o `file_id`), no el binario.
- Opcional: **Redis** para colas (procesar notificaciones/emails tras guardar la solicitud) y para cache.

---

## 3. Esquema resumido del flujo futuro

```
[Usuario rellena formulario y elige archivo]
        │
        ▼
[Frontend] Sube archivo por chunks/TUS → [Backend] Guarda en disco/S3
        │                                      │
        │◄────────────────────────────────────┘
        │   Respuesta: { file_id } o error
        │
        ▼
Si file_id OK (o sin archivo): [Frontend] envía POST /api/solicitudes
        │   body: { tipo_licencia, fechas, ..., file_id? }
        │
        ▼
[Backend] Valida, asocia file_id al registro, INSERT en BD, responde 201
```

- Si la subida del archivo falla: no hay `file_id`, el frontend no envía el formulario o envía sin `file_id` (según si el archivo es obligatorio u opcional).
- Si el INSERT falla: el archivo ya está en almacenamiento; se puede tener un job de limpieza para archivos “huérfanos” (sin registro en BD tras X horas).

---

## 4. ¿Funciona en todos los dispositivos? (iPhone, Android, escritorio)

**Sí.** La estrategia de subida en dos fases + chunks/TUS + Uppy está pensada para funcionar bien en **cualquier dispositivo**: iPhone, iPad, Android, escritorio. Es precisamente en móviles (sobre todo iPhone) donde el enfoque actual suele fallar más.

### Por qué en iPhone a veces no sube ahora

En iOS (Safari / WebKit) suelen pasar cosas como:

- **Un solo POST con formulario + archivo**: la petición puede tardar mucho; si el usuario cambia de app, minimiza o la red se corta un momento, iOS puede matar la petición o el proceso. No hay reintento automático.
- **Límites y timeouts**: Safari es más estricto con conexiones largas y a veces no muestra errores claros cuando falla la subida.
- **`<input type="file">`**: en iOS a veces devuelve archivos con rutas "virtuales" o restricciones; un fallo en `move_uploaded_file` o en la validación puede hacer que parezca que "no sube" sin mensaje útil.
- **Red móvil**: 4G/5G inestable hace que una subida de todo el archivo en un solo request falle más que si se envían trozos pequeños con reintentos.

Con **chunks o TUS**:

- Se envían **trozos pequeños**; si uno falla, solo se reenvía ese trozo, no todo el archivo.
- Las peticiones son **cortas**, lo que encaja mejor con el comportamiento de Safari/iOS.
- **Reintentos** en el cliente (Uppy/tus-js-client) mejoran la tasa de éxito en redes inestables.
- **Progreso** visible: el usuario sabe que está subiendo y puede esperar o reintentar con claridad.

Uppy está probado en **iOS/Safari** y maneja bien el `input file` y la cámara/fotos en iPhone; TUS y chunked uploads funcionan en cualquier navegador moderno (Chrome, Safari, Firefox, Edge) en móvil y escritorio.

### Recomendaciones específicas para iPhone / móvil

- Usar **Uppy** (o similar) con **TUS** o **chunked uploads** en el frontend: mismo código para todos los dispositivos.
- **Tamaño de chunk** moderado (ej. 256 KB–1 MB): en móvil, chunks muy grandes pueden seguir dando problemas; los pequeños se recuperan mejor.
- Mostrar **mensaje claro** si no hay red ("Conecta a internet para subir el archivo") y, si se puede, **no cerrar la app/pestaña** hasta que la subida termine (avisar al usuario).
- Probar en **Safari en iPhone** (y si es posible en iPad) antes de dar por cerrado el flujo de subida.

Con esto, **sí funciona sin importar el dispositivo**: el mismo flujo sirve para iPhone, Android y escritorio, y se reducen mucho los fallos que hoy ves en iPhone.

---

## 5. Checklist para que “no se presenten” los problemas de subida

- [ ] **Subida en dos fases**: archivo primero, formulario después, con referencia al archivo.
- [ ] **Chunks o TUS** para archivos grandes y redes inestables.
- [ ] **Límites y validación** en backend (tamaño, tipo MIME, extensión) antes de guardar.
- [ ] **Progreso y reintentos** en el cliente (ej. Uppy).
- [ ] **PWA**: service worker + manifest; subidas solo con conexión o con cola explícita y mensaje claro.
- [ ] **No guardar registro en BD** hasta tener el archivo guardado (o haber decidido que va sin archivo).
- [ ] **Almacenamiento** definido (directorio o S3) y permisos correctos en el servidor.

---

## 6. Resumen en una frase

**Backend tipo API (Laravel o Node) + subida de archivos por chunks/TUS en un endpoint aparte + frontend SPA (React/Vue/Svelte) con Uppy (o similar) + PWA con Workbox y subidas solo online (o con cola bien gestionada).** Con esto se evita el modelo “un solo request con todo” que hoy genera registros sin archivo y problemas de límites.
