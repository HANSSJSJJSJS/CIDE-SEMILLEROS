# 🔧 SOLUCIÓN AL ERROR: Column 'nombre_completo' not found

## ❌ Error Actual
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'aprendices.nombre_completo' in 'field list'
```

## 🎯 Causa del Problema
La tabla `aprendices` en tu base de datos **NO tiene** la columna `nombre_completo`, pero tu código Laravel sí la está usando.

## ✅ Solución (3 Pasos)

### Paso 1: Ejecutar el Script SQL

**Opción A - Desde Terminal:**
```bash
mysql -u root -p cide_sena < fix_database.sql
```

**Opción B - Desde phpMyAdmin:**
1. Abre phpMyAdmin (http://localhost/phpmyadmin)
2. Selecciona la base de datos `cide_sena`
3. Ve a la pestaña **SQL**
4. Copia y pega el contenido del archivo `fix_database.sql`
5. Haz clic en **Continuar**

**Opción C - Ejecutar manualmente:**
```sql
USE cide_sena;

ALTER TABLE aprendices 
ADD COLUMN nombre_completo VARCHAR(255) NULL AFTER apellidos;

UPDATE aprendices 
SET nombre_completo = TRIM(CONCAT(COALESCE(nombres, ''), ' ', COALESCE(apellidos, '')));

ALTER TABLE aprendices 
MODIFY COLUMN nombre_completo VARCHAR(255) NOT NULL;
```

### Paso 2: Verificar que funcionó
```sql
SELECT id_aprendiz, nombres, apellidos, nombre_completo 
FROM aprendices 
LIMIT 5;
```

Deberías ver algo como:
```
id_aprendiz | nombres           | apellidos | nombre_completo
7           | Joaquin cañon     | NULL      | Joaquin cañon
8           | Hansbleidi        | Cardenas  | Hansbleidi Cardenas
```

### Paso 3: Refrescar la aplicación
```bash
# Limpiar cache de Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Reiniciar el servidor si está corriendo
# Ctrl+C para detener, luego:
php artisan serve
```

## 📋 ¿Qué hace el script?

1. **Agrega la columna** `nombre_completo` a la tabla `aprendices`
2. **Llena automáticamente** los datos concatenando `nombres + apellidos`
3. **Hace la columna obligatoria** (NOT NULL)

## 🔍 Verificación Final

Visita: http://127.0.0.1:8000/lider_semi/semilleros

Si todo está bien, deberías ver:
- ✅ Los proyectos cargando correctamente
- ✅ Los nombres completos de los aprendices
- ✅ Sin errores de SQL

## ⚠️ Problemas Comunes

### Error: "Access denied for user"
**Solución:** Verifica tu usuario y contraseña de MySQL en el archivo `.env`

### Error: "Table 'cide_sena.aprendices' doesn't exist"
**Solución:** Asegúrate de estar usando la base de datos correcta

### Error: "Duplicate column name 'nombre_completo'"
**Solución:** La columna ya existe, solo ejecuta el UPDATE:
```sql
UPDATE aprendices 
SET nombre_completo = TRIM(CONCAT(COALESCE(nombres, ''), ' ', COALESCE(apellidos, '')));
```

## 📝 Cambios Realizados en el Código

### Modelo Aprendiz (`app/Models/Aprendiz.php`)
- ✅ Agregado `nombre_completo` al fillable
- ✅ Agregada relación `proyectos()`

### Modelo Proyecto (`app/Models/Proyecto.php`)
- ✅ Agregada relación `aprendices()`
- ✅ Configurados timestamps personalizados

## 🚀 Próximos Pasos (Opcional)

Si quieres mejorar aún más la estructura, considera:

1. **Crear tabla pivote `aprendiz_proyecto`** para relaciones N:M
2. **Crear tabla pivote `aprendiz_semillero`** para relaciones N:M
3. **Actualizar la tabla `documentos`** para que `id_aprendiz` tenga valores reales

Pero por ahora, con ejecutar el script SQL es suficiente para que funcione.

## 📞 Soporte

Si después de ejecutar el script sigues teniendo problemas:
1. Verifica los logs: `storage/logs/laravel.log`
2. Revisa la conexión a la BD en `.env`
3. Asegúrate de que el servidor MySQL esté corriendo
