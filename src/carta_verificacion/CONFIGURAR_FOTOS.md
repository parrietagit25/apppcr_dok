# 📸 Configuración de Fotos de Colaboradores

## 📁 Estructura Requerida

En GoDaddy, crear la siguiente estructura:

```
/public_html/carta/
├── verificar.php
├── fotos/
│   ├── .htaccess
│   ├── 0001.jpeg
│   ├── 0002.jpeg
│   ├── 0011.jpeg
│   ├── 0123.jpeg
│   └── ...
```

---

## 📝 Nomenclatura de Archivos

### **Formato:** `XXXX.jpeg`

- **XXXX** = Código del empleado con 4 dígitos (padding con ceros a la izquierda)
- **Extensión:** `.jpeg` (en minúsculas)

### **Ejemplos:**

| Código Empleado | Nombre de Archivo |
|-----------------|-------------------|
| 1               | `0001.jpeg`       |
| 11              | `0011.jpeg`       |
| 123             | `0123.jpeg`       |
| 1234            | `1234.jpeg`       |

---

## 🔧 Configuración Inicial

### **Paso 1: Crear carpeta fotos**

1. cPanel → File Manager
2. Navegar a `/public_html/carta/`
3. Click en "New Folder"
4. Nombre: `fotos`
5. Permisos: `755` (rwxr-xr-x)

### **Paso 2: Subir .htaccess a la carpeta fotos**

1. Copiar el contenido de `.htaccess_fotos`
2. Crear archivo `/public_html/carta/fotos/.htaccess`
3. Pegar el contenido
4. Permisos: `644`

### **Paso 3: Subir fotos**

1. Ir a `/public_html/carta/fotos/`
2. Click en "Upload"
3. Seleccionar las fotos de los colaboradores
4. **IMPORTANTE:** Renombrar según el formato `XXXX.jpeg`

---

## 📐 Especificaciones de las Fotos

### **Recomendaciones:**

- **Formato:** JPEG
- **Tamaño:** 300x300 px (mínimo 150x150 px)
- **Aspecto:** Cuadrado preferido (se recortará en círculo)
- **Peso:** Máximo 500 KB
- **Fondo:** Preferible fondo claro/neutro
- **Tipo:** Foto tipo carnet/profesional

### **Ejemplo:**

```
Nombre archivo: 0011.jpeg
Dimensiones: 300x300 px
Peso: 120 KB
Formato: JPEG
```

---

## 🧪 Probar que Funciona

### **Método 1: Acceso directo**

Abrir en navegador:
```
https://grupopcr.com.pa/carta/fotos/0011.jpeg
```

Debe mostrar la foto del colaborador con código 0011.

### **Método 2: En la verificación**

1. Generar una carta del colaborador con código 0011
2. Escanear el QR
3. La página de verificación debe mostrar la foto

---

## 🔄 Subir Fotos Masivamente

### **Opción A: FTP (FileZilla, WinSCP)**

1. Conectar vía FTP a GoDaddy
2. Navegar a `/public_html/carta/fotos/`
3. Seleccionar todas las fotos
4. Subir en batch
5. **IMPORTANTE:** Verificar que los nombres sean correctos

### **Opción B: cPanel File Manager (Lento)**

1. Comprimir fotos en un `.zip`
2. Subir el `.zip` a `/public_html/carta/fotos/`
3. Click derecho → Extract
4. Eliminar el `.zip`

---

## 🛠️ Renombrar Fotos en Lote

### **Script PHP para renombrar (temporal):**

Crear `/public_html/carta/fotos/renombrar.php`:

```php
<?php
// Script temporal para renombrar fotos
// ELIMINAR después de usar

$directorio = __DIR__;
$archivos = scandir($directorio);

foreach ($archivos as $archivo) {
    if (is_file($directorio . '/' . $archivo) && preg_match('/\.(jpg|jpeg|png)$/i', $archivo)) {
        // Extraer número del nombre
        preg_match('/(\d+)/', $archivo, $matches);
        if (!empty($matches[1])) {
            $codigo = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
            $nuevo_nombre = $codigo . '.jpeg';
            
            if ($archivo !== $nuevo_nombre) {
                rename($directorio . '/' . $archivo, $directorio . '/' . $nuevo_nombre);
                echo "Renombrado: $archivo → $nuevo_nombre<br>";
            }
        }
    }
}

echo "Proceso completado.";
?>
```

Acceder a: `https://grupopcr.com.pa/carta/fotos/renombrar.php`

**⚠️ ELIMINAR el archivo después de usarlo.**

---

## 🔍 Solución de Problemas

### **Problema: Foto no se muestra**

**Verificar:**

1. ✅ El archivo existe en `/public_html/carta/fotos/`
2. ✅ El nombre es correcto: `0011.jpeg` (4 dígitos + .jpeg)
3. ✅ Permisos del archivo: `644`
4. ✅ Permisos de la carpeta: `755`
5. ✅ URL funciona: `https://grupopcr.com.pa/carta/fotos/0011.jpeg`

### **Problema: Aparece "Foto no disponible"**

**Causas:**

- El archivo no existe
- El nombre no coincide con el código del empleado
- Permisos incorrectos
- Extensión incorrecta (debe ser `.jpeg` en minúsculas)

### **Problema: Error 403 Forbidden**

**Solución:**

Verificar que `.htaccess` en la carpeta `fotos` tenga:
```apache
<FilesMatch "\.(jpeg|jpg|png|gif)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
```

---

## 📊 Listado de Fotos Requeridas

Para generar un listado de códigos de empleados sin foto:

```sql
-- Ejecutar en phpMyAdmin de GoDaddy
SELECT codigo_empleado 
FROM cartas_trabajo_verificacion 
GROUP BY codigo_empleado 
ORDER BY codigo_empleado;
```

Copiar los códigos y crear las fotos correspondientes.

---

## 🎨 Cómo se Muestra la Foto

En la página de verificación:
- **Posición:** Centrada, arriba de los datos del colaborador
- **Tamaño:** 150x150 px
- **Forma:** Circular
- **Borde:** Azul de 4px
- **Sombra:** Ligera

**Vista previa:**

```
┌─────────────────────────────┐
│   ✓ Carta Verificada        │
│                             │
│      ┌───────────┐          │
│      │   📷      │          │  ← Foto circular
│      │           │          │     150x150 px
│      └───────────┘          │
│                             │
│  Nombre: Juan Pérez         │
│  Código: 0011               │
│  ...                        │
└─────────────────────────────┘
```

---

## ✅ Checklist de Configuración

- [ ] Carpeta `fotos` creada en `/public_html/carta/`
- [ ] Archivo `.htaccess` en carpeta `fotos`
- [ ] Permisos correctos (carpeta 755, archivos 644)
- [ ] Fotos subidas con nomenclatura correcta
- [ ] Probado acceso directo a una foto
- [ ] Probado en verificación con QR

---

## 📞 Soporte

Si tienes problemas con las fotos, contacta a TI Grupo PCR.

**Última actualización:** 2025-10-10

