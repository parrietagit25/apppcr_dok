# Usuarios Tipo 6 - Fuera de Planilla

## Descripción
Los usuarios tipo 6 son empleados que están **fuera de la planilla principal** pero necesitan acceso al sistema APP PCR. Estos usuarios se almacenan en la tabla `encargados_colab` en lugar de `empleados`.

## Características

### Tabla de Datos
- **Tabla principal**: `encargados_colab`
- **Tabla de autenticación**: `empleado_log` (type_user = 6)
- **Tipo de usuario**: 6

### Campos de la tabla `encargados_colab`
- `id`: Identificador único
- `code_empleado`: Código del empleado (único)
- `nombre`: Nombre del empleado
- `apellido`: Apellido del empleado
- `departamento`: Departamento al que pertenece
- `email`: Correo electrónico
- `gerente_area`: Gerente del área (opcional)
- `fecha_creacion`: Fecha de creación del registro
- `fecha_actualizacion`: Fecha de última actualización

## Funcionalidades

### Acceso al Sistema
- **Login**: Mismo proceso que empleados regulares
- **Autenticación**: Validación contra `empleado_log` con `type_user = 6`
- **Sesión**: Misma gestión de sesiones que otros usuarios

### Permisos
- **Acceso completo**: Todas las funcionalidades de empleado regular
- **Mi Espacio**: Dashboard personalizado
- **Solicitudes**: Cartas de trabajo, permisos, vacaciones
- **Reportes**: Incapacidades, calamidades
- **Beneficios**: Acceso a beneficios corporativos

### Gestión Administrativa
- **Creación**: Desde panel administrativo
- **Edición**: Modificación de datos personales
- **Contraseñas**: Cambio de contraseñas
- **Estado**: Activación/desactivación

## Implementación

### Archivos Modificados
1. **`mantenimiento_usuarios_no_listados.php`**: Interfaz de gestión
2. **`User.php`**: Modelo de datos actualizado
3. **`MainController.php`**: Controlador de gestión
4. **`buscar_colaborador.php`**: Validación de códigos

### Funciones Principales
- `registrar_usuario_no_listado()`: Crear nuevo usuario tipo 6
- `editar_usuario()`: Modificar datos del usuario
- `usuarios_no_listados()`: Listar usuarios tipo 6
- `nombre_colaborador()`: Obtener nombre del usuario

## Flujo de Trabajo

### Registro de Usuario
1. Administrador accede a mantenimiento
2. Completa formulario con datos del empleado
3. Sistema valida código único
4. Crea registro en `encargados_colab`
5. Crea registro en `empleado_log` con `type_user = 6`
6. Usuario puede acceder al sistema

### Autenticación
1. Usuario ingresa código y contraseña
2. Sistema busca en `empleado_log`
3. Valida `type_user = 6`
4. Verifica existencia en `encargados_colab`
5. Crea sesión si es válido

## Ventajas

### Flexibilidad
- **Empleados externos**: Contratistas, consultores
- **Personal temporal**: Trabajadores por proyecto
- **Colaboradores especiales**: Personal de otras empresas

### Integración
- **Mismo sistema**: Acceso a todas las funcionalidades
- **Misma interfaz**: Experiencia de usuario consistente
- **Misma seguridad**: Mismos controles de acceso

### Gestión
- **Centralizada**: Un solo lugar para gestionar
- **Escalable**: Fácil agregar más usuarios
- **Auditable**: Trazabilidad completa

## Consideraciones

### Seguridad
- **Validación**: Códigos únicos en ambas tablas
- **Encriptación**: Contraseñas encriptadas
- **Sesiones**: Gestión segura de sesiones

### Mantenimiento
- **Consistencia**: Datos sincronizados entre tablas
- **Backup**: Incluir en respaldos de BD
- **Monitoreo**: Seguimiento de accesos

## Uso Recomendado

### Casos de Uso
- **Contratistas**: Personal externo con acceso temporal
- **Consultores**: Profesionales independientes
- **Personal de otras empresas**: Colaboradores de empresas aliadas
- **Trabajadores por proyecto**: Personal específico por proyecto

### Mejores Prácticas
- **Códigos únicos**: No duplicar códigos existentes
- **Datos completos**: Llenar todos los campos requeridos
- **Revisión periódica**: Verificar usuarios activos
- **Limpieza**: Eliminar usuarios inactivos

## Conclusión

Los usuarios tipo 6 proporcionan una solución flexible para incluir empleados fuera de planilla en el sistema APP PCR, manteniendo la misma funcionalidad y seguridad que los empleados regulares, pero con una gestión de datos separada y especializada.
