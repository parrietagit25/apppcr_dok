# 📋 SISTEMA DE SUPERVISORES Y PERSONAL A CARGO

## ✅ IMPLEMENTACIÓN COMPLETADA

Sistema para gestionar supervisores y asignar personal a cargo, permitiendo que los supervisores aprueben solicitudes de permisos y vacaciones de su personal.

---

## 🎯 FUNCIONALIDADES

### Para Administradores (RRHH):
- ✅ Asignar usuarios como supervisores (type_user = 6)
- ✅ Ver todos los supervisores registrados
- ✅ Asignar personal a cargo a cada supervisor
- ✅ Ver el personal a cargo de cada supervisor
- ✅ Eliminar personal a cargo de un supervisor
- ✅ Remover supervisores (regresa a type_user = 2)

### Para Supervisores (próximamente):
- 🔄 Ver solicitudes de permisos de su personal a cargo
- 🔄 Ver solicitudes de vacaciones de su personal a cargo
- 🔄 Aprobar/rechazar solicitudes de su personal a cargo

---

## 🗄️ BASE DE DATOS

### Nueva Tabla: `supervisores_personal_cargo`

```sql
CREATE TABLE supervisores_personal_cargo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supervisor_code VARCHAR(10) NOT NULL,
    colaborador_code VARCHAR(10) NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_supervisor_colaborador (supervisor_code, colaborador_code)
);
```

**Campos:**
- `id`: ID único de la relación
- `supervisor_code`: Código del empleado supervisor (type_user = 6)
- `colaborador_code`: Código del empleado que está a cargo
- `fecha_asignacion`: Fecha en que se asignó
- `activo`: 1=Activo, 0=Inactivo (soft delete)

**Características:**
- ✅ Un colaborador puede tener múltiples supervisores
- ✅ Un supervisor puede ser personal a cargo de otro supervisor (jerarquía)
- ✅ Índice único evita duplicar relaciones
- ✅ Soft delete (activo/inactivo) para mantener historial

---

## 🚀 INSTALACIÓN

### PASO 1: Crear la tabla en la base de datos

```bash
# Opción 1: Desde Docker
docker exec -i apppcr_db mysql -uappuser -papppass apppcr < src/crear_tabla_supervisores_personal.sql

# Opción 2: Manualmente
docker exec -it apppcr_db mysql -uappuser -papppass apppcr
```

Luego ejecutar el contenido de `src/crear_tabla_supervisores_personal.sql`

### PASO 2: Verificar que la tabla se creó

```sql
USE apppcr;
DESCRIBE supervisores_personal_cargo;
```

### PASO 3: Los archivos PHP ya están actualizados

✅ Script SQL creado  
✅ Modelo User actualizado (métodos nuevos)  
✅ Vista mantenimiento_encargados actualizada  
✅ Controlador MainController actualizado  

---

## 📁 ARCHIVOS MODIFICADOS/CREADOS

### ✅ 1. Script SQL: `src/crear_tabla_supervisores_personal.sql`
**Nuevo archivo** - Script para crear la tabla

### ✅ 2. Modelo: `src/app/models/User.php`
**Métodos agregados:**
- `get_personal_a_cargo($supervisor_code)` - Obtiene personal a cargo de un supervisor
- `get_supervisores_de_colaborador($colaborador_code)` - Obtiene supervisores de un colaborador
- `asignar_personal_a_cargo($supervisor_code, $colaborador_code)` - Asigna personal a cargo
- `remover_personal_a_cargo($id_relacion)` - Remueve personal a cargo (soft delete)
- `colaboradores_disponibles_para_asignar($supervisor_code)` - Lista colaboradores disponibles

### ✅ 3. Vista: `src/app/views/mantenimiento_encargados.php`
**Completamente reescrita**

**Características:**
- Vista con acordeones para cada supervisor
- Muestra personal a cargo de cada supervisor en tabla
- Botones para agregar/eliminar personal a cargo
- Modal para asignar nuevos supervisores
- Modal para agregar personal a cargo
- Modal para remover supervisores

### ✅ 4. Controlador: `src/app/controllers/MainController.php`
**Funcionalidades agregadas:**
- Manejo de asignación de personal a cargo
- Manejo de eliminación de personal a cargo
- Endpoint AJAX para obtener colaboradores disponibles
- Desactivación automática de relaciones al remover supervisor

---

## 🎨 INTERFAZ DE USUARIO

### Vista Principal:

```
┌─────────────────────────────────────────────────────────┐
│  Mantenimiento - Supervisores y Personal a Cargo        │
│  [Asignar Supervisor]                                    │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ▼ Juan Pérez (001234) [3 personal a cargo]            │
│    ├─ Personal a Cargo:                                 │
│    │  ┌──────────────────────────────────────────┐     │
│    │  │ Código │ Nombre │ Apellido │ [Eliminar] │     │
│    │  │ 005678 │ María  │ García   │    [X]     │     │
│    │  └──────────────────────────────────────────┘     │
│    │  [Agregar Personal]                               │
│    │  [Remover como Supervisor]                         │
│                                                          │
│  ▶ Ana López (005678) [0 personal a cargo]             │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 FLUJO DE TRABAJO

### 1. Asignar un Supervisor:
1. Click en "Asignar Supervisor"
2. Seleccionar usuario de la lista
3. Click en "Asignar"
4. El usuario pasa a `type_user = 6`

### 2. Asignar Personal a Cargo:
1. Expandir el acordeón del supervisor
2. Click en "Agregar Personal"
3. Seleccionar colaborador de la lista (excluye ya asignados)
4. Click en "Agregar"
5. Se crea relación en `supervisores_personal_cargo`

### 3. Eliminar Personal a Cargo:
1. En la tabla de personal a cargo
2. Click en "Eliminar" del colaborador
3. Confirmar eliminación
4. Se desactiva la relación (soft delete)

### 4. Remover Supervisor:
1. Expandir acordeón del supervisor
2. Click en "Remover como Supervisor"
3. Confirmar
4. Se desactivan todas sus relaciones de personal a cargo
5. El usuario regresa a `type_user = 2`

---

## 📊 ESTRUCTURA DE DATOS

### Ejemplo de Relaciones:

```
Supervisor: Juan Pérez (001234)
  ├── Personal a Cargo:
  │   ├── María García (005678)
  │   ├── Carlos López (005679)
  │   └── Ana Martínez (005680)

Supervisor: Ana López (005678) [también es personal a cargo de Juan]
  ├── Personal a Cargo:
  │   └── Pedro Sánchez (005681)
```

**Nota:** Ana López es supervisor Y también está a cargo de Juan Pérez (jerarquía permitida).

---

## 🔧 PRÓXIMOS PASOS (Pendientes)

### 1. Modificar Consultas de Permisos:
- Actualizar `select_permisos_all_admin()` en `Rrhh.php`
- Filtrar por supervisores del usuario logueado
- Mostrar solo permisos de su personal a cargo

### 2. Modificar Consultas de Vacaciones:
- Actualizar `select_vacaciones_all()` en `Rrhh.php`
- Filtrar por supervisores del usuario logueado
- Mostrar solo vacaciones de su personal a cargo

### 3. Crear Vista para Supervisores:
- Nueva vista: `solicitud_permiso_supervisor.php`
- Nueva vista: `solicitud_vacaciones_supervisor.php`
- Botones de aprobar/rechazar
- Envío de notificaciones por email

### 4. Modificar Flujo de Solicitudes:
- Al solicitar permiso/vacación, identificar supervisores
- Enviar notificación a supervisores
- Permitir aprobación desde vista de supervisor

---

## 🧪 PRUEBAS RECOMENDADAS

### Pruebas Funcionales:

1. **Asignar Supervisor:**
   - [ ] Seleccionar usuario
   - [ ] Verificar que `type_user = 6`
   - [ ] Verificar que aparece en lista de supervisores

2. **Asignar Personal a Cargo:**
   - [ ] Agregar colaborador a supervisor
   - [ ] Verificar que aparece en tabla
   - [ ] Intentar agregar duplicado (debe prevenirse)
   - [ ] Verificar que no aparece en lista de disponibles

3. **Eliminar Personal a Cargo:**
   - [ ] Eliminar relación
   - [ ] Verificar que desaparece de tabla
   - [ ] Verificar que vuelve a aparecer en disponibles

4. **Jerarquía:**
   - [ ] Asignar supervisor A como personal de supervisor B
   - [ ] Verificar que supervisor A puede tener su propio personal

5. **Remover Supervisor:**
   - [ ] Remover supervisor con personal a cargo
   - [ ] Verificar que todas las relaciones se desactivan
   - [ ] Verificar que `type_user = 2`

---

## 📝 NOTAS IMPORTANTES

1. **Soft Delete:** Las relaciones se desactivan (activo = 0), no se eliminan físicamente
2. **Unicidad:** No se pueden duplicar relaciones supervisor-colaborador
3. **Jerarquía:** Los supervisores pueden ser personal a cargo de otros supervisores
4. **Múltiples Supervisores:** Un colaborador puede tener varios supervisores

---

## ✅ CHECKLIST DE COMPLETITUD

- [x] Script SQL para crear tabla
- [x] Métodos en modelo User
- [x] Vista de mantenimiento actualizada
- [x] Controlador actualizado
- [x] Endpoint AJAX para colaboradores disponibles
- [ ] Modificar consultas de permisos para supervisores
- [ ] Modificar consultas de vacaciones para supervisores
- [ ] Crear vistas de aprobación para supervisores
- [ ] Integrar notificaciones por email

---

## 🎉 CONCLUSIÓN

La estructura base del sistema de supervisores está **100% implementada**. Los administradores pueden gestionar supervisores y asignar personal a cargo.

**Pendiente:** Implementar las vistas y lógica para que los supervisores puedan aprobar solicitudes de su personal a cargo.

**Desarrollado:** Enero 2026  
**Estado:** Base Implementada ✅ | Funcionalidad de Aprobación Pendiente 🔄
