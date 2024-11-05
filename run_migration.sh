#!/bin/bash

# Array de rutas de migraciones
declare -a migrations=(
    
"database/migrations/2024_09_04_120837_add_fletero_id_to_transports_table.php"
    "database/migrations/2024_09_04_154507_add_transport_id_to_users.php"
    "database/migrations/2024_09_04_173923_create_razon_socials_table.php"
"database/migrations/2024_09_04_174301_add_fletero_id_to_trailers.php"
"database/migrations/2024_09_04_174301_add_fletero_id_to_trailers.php"
"database/migrations/2024_09_04_175321_add_fletero_id_to_drivers.php"
"database/migrations/2024_09_05_182811_modify_satelital_column_in_fleteros.php"
"database/migrations/2024_09_10_143517_add_deleted_at_to_drivers_table.php"

)

# Recorrer el array y ejecutar el comando migrate para cada ruta
for migration in "${migrations[@]}"
do
    echo "Ejecutando migración: $migration"
    php artisan migrate --path="$migration"

    # Verificar si la migración fue exitosa
    if [ $? -ne 0 ]; then
        echo "Error al ejecutar la migración: $migration"
        exit 1
    fi
done

echo "Todas las migraciones fueron ejecutadas correctamente."
