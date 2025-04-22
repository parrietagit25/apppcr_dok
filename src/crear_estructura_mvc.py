
import os

def create_mvc_structure(base_path):
    # Definir estructura de carpetas
    folders = [
        "app/controllers",
        "app/models",
        "app/views",
        "app/core",
        "app/helpers",
        "config",
        "public/css",
        "public/js",
        "public/images",
        "routes",
        "storage",
        "vendor"
    ]
    
    # Crear las carpetas
    for folder in folders:
        path = os.path.join(base_path, folder)
        os.makedirs(path, exist_ok=True)
    
    # Crear archivos básicos
    files = {
        "config/config.php": "<?php\n// Configuración general\n\nreturn [\n    'db_host' => 'localhost',\n    'db_name' => 'mi_base_de_datos',\n    'db_user' => 'root',\n    'db_pass' => ''\n];\n",
        "routes/web.php": "<?php\n// Definir rutas aquí\n",
        "public/index.php": "<?php\nrequire_once '../app/core/Bootstrap.php';\n",
        "README.md": "# Proyecto MVC en PHP\nEste es un proyecto base con estructura MVC."
    }
    
    for file, content in files.items():
        file_path = os.path.join(base_path, file)
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(content)
    
    print("Estructura MVC creada con éxito en:", base_path)

# Ejecutar la función en la carpeta actual
if __name__ == "__main__":
    create_mvc_structure(os.getcwd())
