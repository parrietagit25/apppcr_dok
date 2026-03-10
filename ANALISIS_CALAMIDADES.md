# Análisis del módulo de Calamidades

## 1. Flujo general

| URL / Vista | Quién accede | Qué hace |
|-------------|--------------|----------|
| `RRHHController.php?calamidad=1` | Cualquier usuario con menú RRHH | **Colaborador:** Ver sus solicitudes, crear nueva (archivo, monto, plazo, forma de pago, descripción). |
| `RRHHController.php?calamidad_vrrhh=1` | Solo RRHH (por `$tiene_acceso_rrhh`) | **RRHH:** Ver todas las calamidades, marcar "Revisado" y enviar email al colaborador. |

---

## 2. Tabla y datos

- **Tabla:** `calamidades`
- **Campos usados:** id, code_user, descripcion, fecha_log, stat, file_add, user_update, monto, plazo, forma_pago.
- **Estados (stat):** 1 = Solicitado, 2 = Revisado.
- **Archivos:** se guardan en `app/uploads/calamidades/`.
- **Datos de nombre/departamento:** se obtienen por JOIN con `col_datos_generales` (en listados) o con `empleados` (en `get_email_calamidad`).

---

## 3. Consultas por rol

### 3.1 Colaborador – solo sus calamidades (`calamidades()`)

- **Uso:** Vista `calamidades.php` cuando el usuario **no** es tipo 6.
- **Query:**

```sql
SELECT ct.id, ct.descripcion, ct.fecha_log, ct.code_user, 
       CASE ct.stat WHEN 1 THEN 'Solicitado' WHEN 2 THEN 'Revisado' END AS estado, 
       c.nombre, ct.file_add 
FROM calamidades ct 
INNER JOIN col_datos_generales c ON ct.code_user = c.codigo  
WHERE ct.stat IN (1, 2) AND ct.code_user = '<code sin prefijo 00>'
```

- **Observación:** `calamidades` guarda `code_user` sin ceros (ej. `1558`); en sesión suele ir con ceros (`001558`). El código hace `$code = substr($code, 2)` para comparar. Si en `col_datos_generales.codigo` se guarda con otro formato, el JOIN puede fallar. Conviene unificar criterio (por ejemplo usar `CAST(... AS UNSIGNED)` como en otros módulos).

### 3.2 Supervisor – calamidades por departamento (`calamidades_gerentes($code)`)

- **Uso:** Vista `calamidades.php` cuando **tipo_usuario == 6**.
- **Query:**

```sql
SELECT * FROM calamidades 
INNER JOIN empleados ON CONCAT('00', calamidades.code_user) = empleados.codigo_empleado 
WHERE empleados.nombre_departamento = :departamento
```

- **Observación:** Muestra todo el **departamento** del supervisor, no solo su personal asignado en `supervisores_personal_cargo`. Si se quiere alinear con “Mi Personal” y vacaciones, habría que filtrar por personal a cargo (como en vacaciones).

### 3.3 RRHH – todas las calamidades (`calamidades_rrhh()`)

- **Uso:** Vista **`calamidad_rrhh.php`** (la vista llama directamente a `$class->calamidades_rrhh()`, no usa la variable `$calamidades` del controlador).
- **Query:**

```sql
SELECT ct.id, ct.descripcion, ct.fecha_log, 
       CASE ct.stat WHEN 1 THEN 'Solicitado' WHEN 2 THEN 'Revisado' END AS estado, 
       c.departamento, ct.monto, ct.plazo, ct.forma_pago, c.nombre, ct.file_add 
FROM calamidades ct 
INNER JOIN col_datos_generales c ON ct.code_user = c.codigo  
WHERE ct.stat IN (1, 2)
```

- **Observación:** RRHH ve todas las solicitudes; la vista está correcta al usar `calamidades_rrhh()`.

---

## 4. Controlador – incoherencia en calamidad_vrrhh

En `RRHHController.php` (aprox. líneas 1375–1402):

- Se asigna `$calamidades = $class->calamidades()` (solo las del usuario en sesión).
- Se carga la vista `calamidad_rrhh.php`.
- La vista **ignora** esa variable y hace `$calamidades = $class->calamidades_rrhh();`.

Efecto: RRHH ve bien porque la vista usa `calamidades_rrhh()`, pero el controlador está enviando datos equivocados. Es mejor que el controlador llame a `calamidades_rrhh()` y pase `$calamidades` a la vista, y que la vista solo use esa variable (sin volver a llamar al modelo).

---

## 5. Bugs detectados

### 5.1 INSERT: plazo guardado con valor de monto

En `Rrhh.php`, método `insertar_calamidades`:

```php
VALUES (:code_user, :descripcion, CURRENT_TIMESTAMP(), :stat, :file_add, :user_update, :monto, :monto, :forma_pago)
```

El segundo `:monto` debería ser `:plazo`. Ahora mismo **plazo** se guarda con el valor de **monto**.

**Corrección sugerida:** en el `VALUES`, cambiar el segundo `:monto` por `:plazo`.

### 5.2 Uso de `get_email_calamidad` en el controlador

En el bloque `calamidad_vrrhh` del controlador:

```php
$get_email_colab = $class->get_email_calamidad($_POST['calamidad_id']);
foreach ($get_email_colab as $key => $value) {
    $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
    $email = $value['email'];
}
```

`get_email_calamidad` devuelve **una sola fila** (`fetch(PDO::FETCH_ASSOC)`), es decir un array asociativo `['email' => ..., 'nombre' => ..., 'apellido' => ...]`. Hacer `foreach ($get_email_colab as $key => $value)` itera sobre **claves y valores** de esa fila (ej. `$key = 'nombre'`, `$value = 'Juan'`), por lo que `$value['nombre']` no existe y puede generar error o datos incorrectos.

**Corrección sugerida:** usar la fila directamente, sin `foreach`:

```php
$get_email_colab = $class->get_email_calamidad($_POST['calamidad_id']);
if ($get_email_colab) {
    $nombre_comple = ($get_email_colab['nombre'] ?? '') . ' ' . ($get_email_colab['apellido'] ?? '');
    $email = $get_email_colab['email'] ?? '';
    // enviar correo...
}
```

---

## 6. Resumen de archivos

| Archivo | Responsabilidad |
|---------|-----------------|
| `RRHHController.php` | `calamidad=1`: POST (insertar), carga `calamidades.php`. `calamidad_vrrhh=1`: POST (update + email), carga `calamidad_rrhh.php`. |
| `Rrhh.php` | `calamidades()`, `calamidades_gerentes()`, `calamidades_rrhh()`, `insertar_calamidades()`, `update_calamidad()`, `get_email_calamidad()`. |
| `calamidades.php` | Listado según tipo: supervisor → `calamidades_gerentes()`, resto → `calamidades()`. Modal alta, modal detalle. |
| `calamidad_rrhh.php` | Listado con `calamidades_rrhh()`, modal por fila para marcar "Revisado". |

---

## 7. Recomendaciones

1. **Corregir INSERT:** Sustituir el segundo `:monto` por `:plazo` en el `VALUES` de `insertar_calamidades`.
2. **Corregir envío de email:** Dejar de hacer `foreach` sobre el resultado de `get_email_calamidad` y usar la fila devuelta como un solo array asociativo.
3. **Controlador y vista RRHH:** Hacer que el controlador asigne `$calamidades = $class->calamidades_rrhh()` y que la vista use solo `$calamidades` (sin llamar de nuevo al modelo).
4. **Códigos (code_user / sesión):** Unificar comparación de códigos (con/sin ceros) usando por ejemplo `CAST(... AS UNSIGNED)` en `calamidades()` y en el JOIN con `col_datos_generales` si aplica.
5. **Supervisores (opcional):** Si se desea que solo vean su personal a cargo, implementar un método tipo `calamidades_por_supervisor($code)` basado en `supervisores_personal_cargo` (similar a vacaciones) y usarlo en `calamidades.php` cuando `tipo_usuario == 6`.
