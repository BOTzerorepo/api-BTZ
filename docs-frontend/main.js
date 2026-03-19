// ========================================
// API Endpoints Data
// ========================================

const API_BASE = 'https://tu-dominio.com/api';

const apiData = {
    autenticacion: {
        name: 'Autenticación',
        icon: '🔐',
        endpoints: [
            {
                method: 'POST',
                path: '/register',
                description: 'Registrar un nuevo usuario en el sistema',
                params: [
                    { name: 'username', type: 'string', required: true },
                    { name: 'email', type: 'string', required: true },
                    { name: 'pass', type: 'string', required: true },
                    { name: 'pass_confirmation', type: 'string', required: true },
                    { name: 'name', type: 'string', required: false },
                    { name: 'last_name', type: 'string', required: false },
                    { name: 'celular', type: 'string', required: false },
                    { name: 'empresa', type: 'string', required: false }
                ]
            },
            {
                method: 'POST',
                path: '/login',
                description: 'Iniciar sesión y obtener token JWT',
                params: [
                    { name: 'email', type: 'string', required: false },
                    { name: 'username', type: 'string', required: false },
                    { name: 'pass', type: 'string', required: true }
                ]
            },
            {
                method: 'POST',
                path: '/logout',
                description: 'Cerrar sesión del usuario actual',
                auth: true
            },
            {
                method: 'GET',
                path: '/user',
                description: 'Obtener información del usuario autenticado',
                auth: true
            },
            {
                method: 'POST',
                path: '/auth/forgot-password',
                description: 'Solicitar restablecimiento de contraseña',
                params: [
                    { name: 'email', type: 'string', required: true }
                ]
            },
            {
                method: 'POST',
                path: '/auth/reset-password',
                description: 'Restablecer contraseña con token',
                params: [
                    { name: 'email', type: 'string', required: true },
                    { name: 'token', type: 'string', required: true },
                    { name: 'password', type: 'string', required: true },
                    { name: 'password_confirmation', type: 'string', required: true }
                ]
            }
        ]
    },

    cargas: {
        name: 'Cargas',
        icon: '📦',
        endpoints: [
            {
                method: 'GET',
                path: '/allCargoLastWeek/{user}',
                description: 'Obtener cargas de la semana pasada',
                params: [{ name: 'user', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/allCargoThisWeek/{user}',
                description: 'Obtener cargas de esta semana',
                params: [{ name: 'user', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/allCargoNextWeek/{user}',
                description: 'Obtener cargas de la próxima semana',
                params: [{ name: 'user', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/allCargoFinished/{user}',
                description: 'Obtener cargas finalizadas',
                params: [{ name: 'user', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/allCargo/{user}',
                description: 'Obtener todas las cargas del usuario',
                params: [{ name: 'user', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/carga/{id}/{user}',
                description: 'Obtener detalles de una carga específica',
                params: [
                    { name: 'id', type: 'integer', required: true, inPath: true },
                    { name: 'user', type: 'integer', required: true, inPath: true }
                ]
            },
            {
                method: 'GET',
                path: '/loadForCntr/{cntr}',
                description: 'Obtener carga por número de contenedor',
                params: [{ name: 'cntr', type: 'string', required: true, inPath: true }]
            },
            {
                method: 'POST',
                path: '/carga',
                description: 'Crear una nueva carga',
                auth: true
            },
            {
                method: 'PUT',
                path: '/carga/{id}',
                description: 'Actualizar una carga existente',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }],
                auth: true
            },
            {
                method: 'DELETE',
                path: '/carga/{id}',
                description: 'Eliminar una carga',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }],
                auth: true
            },
            {
                method: 'POST',
                path: '/ingresoFormulario',
                description: 'Guardar formulario de ingreso de carga'
            },
            {
                method: 'POST',
                path: '/issetBooking',
                description: 'Verificar si existe un booking'
            }
        ]
    },

    status: {
        name: 'Estado de Cargas',
        icon: '📊',
        endpoints: [
            {
                method: 'POST',
                path: '/statusCarga',
                description: 'Actualizar el estado de una carga'
            },
            {
                method: 'GET',
                path: '/cargasActivas',
                description: 'Listar todas las cargas activas'
            },
            {
                method: 'GET',
                path: '/cargasActivasCompany',
                description: 'Listar cargas activas de la compañía'
            },
            {
                method: 'GET',
                path: '/cargasActivasTransport/{transport}',
                description: 'Listar cargas activas de un transporte',
                params: [{ name: 'transport', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/status',
                description: 'Listar todos los estados disponibles'
            },
            {
                method: 'GET',
                path: '/ultimoStatus/{id}',
                description: 'Obtener el último estado de una carga',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/historialStatus/{cntr}',
                description: 'Obtener historial de estados de un contenedor',
                params: [{ name: 'cntr', type: 'string', required: true, inPath: true }]
            }
        ]
    },

    transportes: {
        name: 'Transportes',
        icon: '🚛',
        endpoints: [
            {
                method: 'GET',
                path: '/transportes',
                description: 'Listar todos los transportes'
            },
            {
                method: 'GET',
                path: '/transportesCompany',
                description: 'Listar transportes de la compañía'
            },
            {
                method: 'GET',
                path: '/transporte/{id}',
                description: 'Obtener detalles de un transporte',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/transporteRazonSocial/{razonSocial}',
                description: 'Buscar transporte por razón social',
                params: [{ name: 'razonSocial', type: 'string', required: true, inPath: true }]
            },
            {
                method: 'POST',
                path: '/transporte',
                description: 'Crear un nuevo transporte'
            },
            {
                method: 'POST',
                path: '/transporte/{id}',
                description: 'Actualizar un transporte',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'DELETE',
                path: '/transporte/{id}',
                description: 'Eliminar un transporte',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/issetTransport/{cuit}',
                description: 'Verificar si existe un transporte por CUIT',
                params: [{ name: 'cuit', type: 'string', required: true, inPath: true }]
            }
        ]
    },

    conductores: {
        name: 'Conductores',
        icon: '👤',
        endpoints: [
            {
                method: 'GET',
                path: '/drivers',
                description: 'Listar todos los conductores'
            },
            {
                method: 'GET',
                path: '/driversCompany',
                description: 'Listar conductores de la compañía'
            },
            {
                method: 'GET',
                path: '/driversTransport/{idTransport}',
                description: 'Listar conductores de un transporte',
                params: [{ name: 'idTransport', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/driver/{id}',
                description: 'Obtener detalles de un conductor',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'POST',
                path: '/driver',
                description: 'Crear un nuevo conductor'
            },
            {
                method: 'POST',
                path: '/driver/{id}',
                description: 'Actualizar información del conductor',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'POST',
                path: '/driverStatus/{id}',
                description: 'Cambiar estado del conductor',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'DELETE',
                path: '/driver/{id}',
                description: 'Eliminar un conductor',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            }
        ]
    },

    camiones: {
        name: 'Camiones',
        icon: '🚚',
        endpoints: [
            {
                method: 'GET',
                path: '/trucks',
                description: 'Listar todos los camiones'
            },
            {
                method: 'GET',
                path: '/trucks/{customer}',
                description: 'Listar camiones de un cliente',
                params: [{ name: 'customer', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/trucksTransport/{transport}',
                description: 'Listar camiones de un transporte',
                params: [{ name: 'transport', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/truck/{truck}',
                description: 'Obtener detalles de un camión',
                params: [{ name: 'truck', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'POST',
                path: '/truck',
                description: 'Crear un nuevo camión'
            },
            {
                method: 'POST',
                path: '/truck/{truck}',
                description: 'Actualizar información del camión',
                params: [{ name: 'truck', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'DELETE',
                path: '/truck/{truck}',
                description: 'Eliminar un camión',
                params: [{ name: 'truck', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/issetTruck/{domain}',
                description: 'Verificar si existe un camión por dominio',
                params: [{ name: 'domain', type: 'string', required: true, inPath: true }]
            }
        ]
    },

    usuarios: {
        name: 'Usuarios',
        icon: '👥',
        endpoints: [
            {
                method: 'GET',
                path: '/users',
                description: 'Listar todos los usuarios'
            },
            {
                method: 'GET',
                path: '/user/{user}',
                description: 'Obtener detalles de un usuario',
                params: [{ name: 'user', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'PUT',
                path: '/user/{id}',
                description: 'Actualizar información del usuario',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'DELETE',
                path: '/user/{id}',
                description: 'Eliminar un usuario',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/users/without-role',
                description: 'Listar usuarios sin rol asignado'
            },
            {
                method: 'GET',
                path: '/users/by-permiso/{permiso}',
                description: 'Listar usuarios por permiso',
                params: [{ name: 'permiso', type: 'string', required: true, inPath: true }]
            }
        ]
    },

    roles: {
        name: 'Roles y Permisos',
        icon: '🔑',
        endpoints: [
            {
                method: 'GET',
                path: '/roles',
                description: 'Listar todos los roles'
            },
            {
                method: 'POST',
                path: '/roles',
                description: 'Crear un nuevo rol'
            },
            {
                method: 'PUT',
                path: '/roles/{id}',
                description: 'Actualizar un rol',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'DELETE',
                path: '/roles/{id}',
                description: 'Eliminar un rol',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/permissions',
                description: 'Listar todos los permisos'
            },
            {
                method: 'POST',
                path: '/permissions',
                description: 'Crear un nuevo permiso'
            },
            {
                method: 'POST',
                path: '/roles/assign-permissions',
                description: 'Asignar permisos a un rol'
            },
            {
                method: 'POST',
                path: '/users/assign-role',
                description: 'Asignar rol a un usuario'
            }
        ]
    },

    contenedores: {
        name: 'Contenedores',
        icon: '📦',
        endpoints: [
            {
                method: 'GET',
                path: '/cntr',
                description: 'Listar todos los contenedores'
            },
            {
                method: 'GET',
                path: '/cntr/{id}',
                description: 'Obtener detalles de un contenedor',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'POST',
                path: '/cntr',
                description: 'Crear un nuevo contenedor'
            },
            {
                method: 'PUT',
                path: '/cntr/{id}',
                description: 'Actualizar un contenedor',
                params: [{ name: 'id', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'DELETE',
                path: '/cntr/{cntrId}',
                description: 'Eliminar un contenedor',
                params: [{ name: 'cntrId', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/issetCntr/{cntr_number}',
                description: 'Verificar si existe un contenedor',
                params: [{ name: 'cntr_number', type: 'string', required: true, inPath: true }]
            }
        ]
    },

    documentos: {
        name: 'Documentos',
        icon: '📄',
        endpoints: [
            {
                method: 'POST',
                path: '/docs/{booking}',
                description: 'Subir documentos para un booking',
                params: [{ name: 'booking', type: 'string', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/docsCntr/{booking}/{user}/{cntr}',
                description: 'Obtener documentos de un contenedor',
                params: [
                    { name: 'booking', type: 'string', required: true, inPath: true },
                    { name: 'user', type: 'integer', required: true, inPath: true },
                    { name: 'cntr', type: 'string', required: true, inPath: true }
                ]
            },
            {
                method: 'GET',
                path: '/imprimirCarga/{cntr_number}',
                description: 'Generar PDF de carga',
                params: [{ name: 'cntr_number', type: 'string', required: true, inPath: true }]
            }
        ]
    },

    notificaciones: {
        name: 'Notificaciones',
        icon: '🔔',
        endpoints: [
            {
                method: 'GET',
                path: '/notifications/all',
                description: 'Obtener todas las notificaciones'
            },
            {
                method: 'GET',
                path: '/notifications/problems',
                description: 'Obtener notificaciones con problemas'
            },
            {
                method: 'GET',
                path: '/notifications/completed',
                description: 'Obtener notificaciones completadas'
            },
            {
                method: 'GET',
                path: '/notifications/assigned',
                description: 'Obtener notificaciones asignadas'
            },
            {
                method: 'POST',
                path: '/notifications/marcarLeidaAsignada',
                description: 'Marcar notificación asignada como leída'
            }
        ]
    },

    tracking: {
        name: 'Rastreo GPS',
        icon: '🗺️',
        endpoints: [
            {
                method: 'GET',
                path: '/viajes',
                description: 'Listar todos los viajes'
            },
            {
                method: 'GET',
                path: '/viajes/{equipment_reference}',
                description: 'Obtener detalles de un viaje',
                params: [{ name: 'equipment_reference', type: 'string', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/flota',
                description: 'Obtener información de la flota'
            },
            {
                method: 'GET',
                path: '/flotaTransport/{transport}',
                description: 'Obtener flota de un transporte',
                params: [{ name: 'transport', type: 'integer', required: true, inPath: true }]
            },
            {
                method: 'GET',
                path: '/geo/actions',
                description: 'Listar historial de acciones geográficas'
            },
            {
                method: 'GET',
                path: '/geo/active',
                description: 'Obtener estado actual por POI'
            }
        ]
    }
};

// ========================================
// DOM Elements
// ========================================

const searchInput = document.getElementById('search-input');
const sidebarNav = document.getElementById('sidebar-nav');
const endpointsContainer = document.getElementById('endpoints-container');
const themeToggle = document.getElementById('theme-toggle');
const toast = document.getElementById('toast');

// ========================================
// Theme Management
// ========================================

function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
}

// ========================================
// Render Functions
// ========================================

function renderSidebar() {
    sidebarNav.innerHTML = '';

    Object.entries(apiData).forEach(([key, category]) => {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'nav-category';

        const title = document.createElement('div');
        title.className = 'nav-category-title';
        title.textContent = `${category.icon} ${category.name}`;

        const navItem = document.createElement('a');
        navItem.className = 'nav-item';
        navItem.href = `#${key}`;
        navItem.textContent = category.name;

        const badge = document.createElement('span');
        badge.className = 'nav-item-badge';
        badge.textContent = category.endpoints.length;
        navItem.appendChild(badge);

        navItem.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            navItem.classList.add('active');
            document.getElementById(key)?.scrollIntoView({ behavior: 'smooth' });
        });

        categoryDiv.appendChild(title);
        categoryDiv.appendChild(navItem);
        sidebarNav.appendChild(categoryDiv);
    });
}

function renderEndpoints(filteredData = apiData) {
    endpointsContainer.innerHTML = '';

    Object.entries(filteredData).forEach(([key, category]) => {
        if (category.endpoints.length === 0) return;

        const categorySection = document.createElement('div');
        categorySection.id = key;
        categorySection.className = 'endpoint-category';

        const categoryTitle = document.createElement('h3');
        categoryTitle.className = 'section-title';
        categoryTitle.style.marginTop = '2rem';
        categoryTitle.textContent = `${category.icon} ${category.name}`;
        categorySection.appendChild(categoryTitle);

        category.endpoints.forEach(endpoint => {
            const card = createEndpointCard(endpoint);
            categorySection.appendChild(card);
        });

        endpointsContainer.appendChild(categorySection);
    });

    // Update stats
    const totalEndpoints = Object.values(filteredData).reduce((sum, cat) => sum + cat.endpoints.length, 0);
    const totalCategories = Object.keys(filteredData).filter(key => filteredData[key].endpoints.length > 0).length;

    document.getElementById('total-endpoints').textContent = totalEndpoints;
    document.getElementById('total-categories').textContent = totalCategories;

    // Re-apply Prism highlighting
    if (window.Prism) {
        window.Prism.highlightAll();
    }
}

function createEndpointCard(endpoint) {
    const card = document.createElement('div');
    card.className = 'endpoint-card fade-in';

    // Header
    const header = document.createElement('div');
    header.className = 'endpoint-header';

    const methodBadge = document.createElement('span');
    methodBadge.className = `method-badge method-${endpoint.method.toLowerCase()}`;
    methodBadge.textContent = endpoint.method;

    const path = document.createElement('code');
    path.className = 'endpoint-path';
    path.textContent = endpoint.path;

    header.appendChild(methodBadge);
    header.appendChild(path);

    // Description
    if (endpoint.description) {
        const desc = document.createElement('p');
        desc.className = 'endpoint-description';
        desc.textContent = endpoint.description;
        card.appendChild(header);
        card.appendChild(desc);
    } else {
        card.appendChild(header);
    }

    // Details
    const details = document.createElement('div');
    details.className = 'endpoint-details';

    // Parameters
    if (endpoint.params && endpoint.params.length > 0) {
        const paramsSection = document.createElement('div');
        paramsSection.className = 'detail-section';

        const paramsTitle = document.createElement('h4');
        paramsTitle.textContent = 'Parámetros';
        paramsSection.appendChild(paramsTitle);

        const paramsList = document.createElement('div');
        paramsList.className = 'param-list';

        endpoint.params.forEach(param => {
            const paramItem = document.createElement('div');
            paramItem.className = 'param-item';

            const paramName = document.createElement('span');
            paramName.className = 'param-name';
            paramName.textContent = param.name;

            const paramType = document.createElement('span');
            paramType.className = 'param-type';
            paramType.textContent = param.type;

            paramItem.appendChild(paramName);
            paramItem.appendChild(paramType);

            if (param.required) {
                const required = document.createElement('span');
                required.className = 'param-required';
                required.textContent = 'requerido';
                paramItem.appendChild(required);
            }

            if (param.inPath) {
                const inPath = document.createElement('span');
                inPath.className = 'param-type';
                inPath.style.background = 'var(--color-primary-light)';
                inPath.style.color = 'var(--color-primary)';
                inPath.textContent = 'en ruta';
                paramItem.appendChild(inPath);
            }

            paramsList.appendChild(paramItem);
        });

        paramsSection.appendChild(paramsList);
        details.appendChild(paramsSection);
    }

    // Auth required
    if (endpoint.auth) {
        const authSection = document.createElement('div');
        authSection.className = 'detail-section';
        authSection.style.borderLeft = '4px solid var(--color-warning)';

        const authText = document.createElement('p');
        authText.style.margin = '0';
        authText.style.color = 'var(--color-text-secondary)';
        authText.textContent = '🔒 Requiere autenticación JWT';

        authSection.appendChild(authText);
        details.appendChild(authSection);
    }

    // Example request
    const exampleSection = document.createElement('div');
    exampleSection.className = 'detail-section';

    const exampleTitle = document.createElement('h4');
    exampleTitle.textContent = 'Ejemplo de Request';
    exampleSection.appendChild(exampleTitle);

    const codeWrapper = document.createElement('div');
    codeWrapper.className = 'code-block-wrapper';

    const pre = document.createElement('pre');
    const code = document.createElement('code');
    code.className = 'language-javascript';

    let exampleCode = `fetch('${API_BASE}${endpoint.path}', {\n  method: '${endpoint.method}'`;

    if (endpoint.auth) {
        exampleCode += `,\n  headers: {\n    'Authorization': 'Bearer YOUR_TOKEN'`;
        if (endpoint.method === 'POST' || endpoint.method === 'PUT') {
            exampleCode += `,\n    'Content-Type': 'application/json'`;
        }
        exampleCode += `\n  }`;
    } else if (endpoint.method === 'POST' || endpoint.method === 'PUT') {
        exampleCode += `,\n  headers: {\n    'Content-Type': 'application/json'\n  }`;
    }

    if (endpoint.method === 'POST' || endpoint.method === 'PUT') {
        exampleCode += `,\n  body: JSON.stringify({\n    // Tu data aquí\n  })`;
    }

    exampleCode += `\n});`;

    code.textContent = exampleCode;
    pre.appendChild(code);
    codeWrapper.appendChild(pre);

    const copyBtn = document.createElement('button');
    copyBtn.className = 'copy-btn';
    copyBtn.setAttribute('data-copy', exampleCode);
    copyBtn.innerHTML = `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
      <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
    </svg>
  `;
    copyBtn.addEventListener('click', () => copyToClipboard(exampleCode));

    codeWrapper.appendChild(copyBtn);
    exampleSection.appendChild(codeWrapper);
    details.appendChild(exampleSection);

    card.appendChild(details);
    return card;
}

// ========================================
// Search Functionality
// ========================================

function filterEndpoints(query) {
    if (!query.trim()) {
        renderEndpoints(apiData);
        return;
    }

    const filtered = {};
    const lowerQuery = query.toLowerCase();

    Object.entries(apiData).forEach(([key, category]) => {
        const matchingEndpoints = category.endpoints.filter(endpoint => {
            return endpoint.path.toLowerCase().includes(lowerQuery) ||
                endpoint.description?.toLowerCase().includes(lowerQuery) ||
                endpoint.method.toLowerCase().includes(lowerQuery);
        });

        if (matchingEndpoints.length > 0) {
            filtered[key] = {
                ...category,
                endpoints: matchingEndpoints
            };
        }
    });

    renderEndpoints(filtered);
}

// ========================================
// Copy to Clipboard
// ========================================

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast();
    });
}

function showToast() {
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2000);
}

// ========================================
// Event Listeners
// ========================================

themeToggle?.addEventListener('click', toggleTheme);

searchInput?.addEventListener('input', (e) => {
    filterEndpoints(e.target.value);
});

// Copy buttons
document.addEventListener('click', (e) => {
    if (e.target.closest('.copy-btn')) {
        const btn = e.target.closest('.copy-btn');
        const textToCopy = btn.getAttribute('data-copy');
        copyToClipboard(textToCopy);
    }
});

// ========================================
// Initialization
// ========================================

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    renderSidebar();
    renderEndpoints();
});
