# BOTzero API Documentation

Documentación moderna e interactiva para la API de BOTzero. Incluye más de 100 endpoints organizados por categorías con ejemplos de uso, búsqueda en tiempo real, y modo oscuro.

## 🚀 Características

- ✨ **Diseño Moderno**: Interfaz premium con glassmorphism y animaciones suaves
- 🌓 **Modo Oscuro**: Alterna entre tema claro y oscuro con persistencia local
- 🔍 **Búsqueda en Tiempo Real**: Encuentra endpoints rápidamente
- 📱 **Diseño Responsivo**: Optimizado para desktop, tablet y móvil
- 📋 **Copy-to-Clipboard**: Copia ejemplos de código con un click
- 🎨 **Syntax Highlighting**: Resaltado de código con Prism.js
- 📊 **11 Categorías**: Más de 100 endpoints organizados lógicamente

## 📦 Instalación

```bash
# Navegar al directorio
cd docs-frontend

# Instalar dependencias
npm install

# Iniciar servidor de desarrollo
npm run dev
```

El servidor se abrirá automáticamente en `http://localhost:3000`

## 🛠️ Comandos Disponibles

```bash
# Desarrollo
npm run dev

# Build para producción
npm run build

# Preview del build
npm run preview
```

## 📁 Estructura del Proyecto

```
docs-frontend/
├── index.html          # Estructura HTML principal
├── index.css           # Sistema de diseño y estilos
├── main.js             # Lógica de la aplicación y datos de endpoints
├── package.json        # Dependencias del proyecto
├── vite.config.js      # Configuración de Vite
└── README.md           # Este archivo
```

## 🎯 Categorías de Endpoints

1. **🔐 Autenticación**: Login, registro, recuperación de contraseña
2. **📦 Cargas**: Gestión completa de cargas
3. **📊 Estado de Cargas**: Seguimiento y actualización de estados
4. **🚛 Transportes**: Administración de empresas de transporte
5. **👤 Conductores**: Gestión de conductores
6. **🚚 Camiones**: Inventario de vehículos
7. **👥 Usuarios**: Administración de usuarios
8. **🔑 Roles y Permisos**: Control de acceso
9. **📦 Contenedores**: Gestión de contenedores
10. **📄 Documentos**: Carga y descarga de documentos
11. **🗺️ Rastreo GPS**: Seguimiento en tiempo real

## 🔧 Personalización

### Cambiar la URL Base de la API

Edita `main.js` y modifica la constante `API_BASE`:

```javascript
const API_BASE = 'https://tu-dominio.com/api';
```

### Agregar Nuevos Endpoints

En `main.js`, agrega endpoints en la estructura `apiData`:

```javascript
nombreCategoria: {
  name: 'Nombre',
  icon: '🎯',
  endpoints: [
    {
      method: 'GET',
      path: '/ruta',
      description: 'Descripción del endpoint',
      params: [
        { name: 'param', type: 'string', required: true }
      ],
      auth: false
    }
  ]
}
```

### Personalizar Colores

Los colores se definen en `index.css` usando variables CSS. Personaliza el tema en `:root` (light) y `[data-theme="dark"]` (dark).

## 🌐 Deployment

### Build para Producción

```bash
npm run build
```

Los archivos optimizados se generarán en la carpeta `dist/`.

### Deployment en Servidores Estáticos

Puedes deployar la carpeta `dist/` en cualquier servidor estático:

- **Netlify**: Arrastra la carpeta `dist/` a netlify.com/drop
- **Vercel**: `vercel --prod` desde el directorio `docs-frontend`
- **GitHub Pages**: Sube el contenido de `dist/` a la rama `gh-pages`
- **Servidor Web**: Copia `dist/*` a tu directorio web público

## 📝 Notas

- La documentación es completamente estática y no requiere backend
- Los ejemplos de código son editables y se pueden copiar
- El tema oscuro se guarda en localStorage
- Compatible con todos los navegadores modernos

## 📄 Licencia

Este proyecto es parte del sistema BOTzero.
