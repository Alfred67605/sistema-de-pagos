# ⛏️ TORMAN MINERÍA - SCPM (Sistema de Control de Pagos Mineros ERP)

Sistema ERP Web Premium de Gestión Minera desarrollado con **Laravel 13**, **TailwindCSS**, **Vite**, **Chart.js** y **Alpine.js** para la administración operacional, control de liquidaciones de personal minero, anticipos, bocaminas, caja de fondos y comercialización de minerales.

---

## ⚡ Ejecución Rápida en Local (Windows)

El sistema está configurado para operar exclusivamente en entorno local seguro (`127.0.0.1` / loopback):

1. **Iniciar el sistema**: Haz doble clic en [`iniciar_sistema.bat`](file:///c:/Users/llano/Downloads/sistema-pagos/iniciar_sistema.bat) o ejecuta en terminal:
   ```cmd
   iniciar_sistema.bat
   ```
   *Acceso directo:* [http://127.0.0.1:8000](http://127.0.0.1:8000)
2. **Credenciales de Acceso:**
   - **Usuario:** `admin@mina.com`
   - **Contraseña:** `admin123`
3. **Detener el sistema**: Haz doble clic en [`detener_sistema.bat`](file:///c:/Users/llano/Downloads/sistema-pagos/detener_sistema.bat) o ejecuta en terminal:
   ```cmd
   detener_sistema.bat
   ```

---

## 💎 Identidad Visual y Diseño Minero Profesional

- **Paleta de Color Minera:** Fondo azul noche / grafito (`#090d16`, `#0f172a`, `#131d31`) con acentos ámbar, cobre y dorado mineral (`#f59e0b`, `#d97706`).
- **Recursos Visuales Locales:**
  - *Login Hero:* Fotografía de galería subterránea con minero en EPP, rieles industriales y lámparas mineras (`public/images/mining/login_hero.jpg`).
  - *Dashboard Panoramic Banner:* Excavación subterránea con maquinaria pesada, arcos de acero y luz cálida de túnel (`public/images/mining/dashboard_banner.jpg`).
  - *Texturas Minerales:* Textura orgánica de roca oscura con vetas metálicas (`public/images/mining/mineral_texture.jpg`).
- **Movimiento Cinemático:** Transiciones Ken Burns suaves en el login, iluminación ambiental pulsante inspirada en linternas de mina, y respeto estricto a `prefers-reduced-motion`.
- **Tipografía y Legibilidad:** Fuentes `Outfit` y `Plus Jakarta Sans`, con cifras tabulares (`font-mono tabular-nums`) en importes (Bs.) y superficies uniformes detrás de tablas y formularios.
- **Navegación Fluida:** Sidebar plegable en desktop (260px a 80px) y drawer deslizante en dispositivos móviles y tablets.
- **Monitoreo Real de Backend:** Indicador en la barra superior con latencia en milisegundos (`/health`), sin estados estáticos simulados.

---

## ⚡ Base de Datos, Rendimiento y Triggers de Integridad

El sistema implementa una arquitectura de base de datos optimizada sobre SQLite mediante la migración [`2026_09_28_120000_add_performance_indexes_and_audit_triggers.php`](file:///c:/Users/llano/Downloads/sistema-pagos/database/migrations/2026_09_28_120000_add_performance_indexes_and_audit_triggers.php):

### 1. Triggers de Auditoría e Integridad
* **`audit_logs`:** Tabla de auditoría inmutable que registra cambios críticos en pagos, anticipos y transacciones de mineral (valores anteriores y nuevos serializados en JSON con marca de tiempo).
* **Triggers de Auditoría:** `trg_audit_pagos_update`, `trg_audit_pagos_delete`, `trg_audit_anticipos_update`, `trg_audit_anticipos_delete`, `trg_audit_transacciones_update`, `trg_audit_transacciones_delete`.
* **Triggers de Integridad Contable:**
  - `trg_integrity_anticipos_saldo_non_negative`: Bloquea con `RAISE(ABORT)` cualquier intento de dejar saldo negativo en un anticipo.
  - `trg_integrity_pagos_neto_non_negative_ins` / `upd`: Bloquea liquidaciones con monto neto negativo.

### 2. Índices de Rendimiento y Optimización de Consultas
* Índices compuestos en foreign keys y filtros frecuentes (`idx_pagos_trabajador_fecha`, `idx_anticipos_saldo`, `idx_trabajadores_bocamina_estado`, `idx_transacciones_tipo_fecha`, `idx_pago_items_pago_id`).
* Transformación de planes de ejecución (`EXPLAIN QUERY PLAN`) de escaneos completos (`SCAN TABLE`) a búsquedas binarias directas (`SEARCH TABLE USING INDEX`).
* Eliminación de problemas N+1 en controladores (`ReporteController` y `PagoController`) mediante eager loading selectivo (`with('trabajador.bocamina')`) y agregación directa en base de datos.

### 3. Comando de Verificación y Reconciliación
Para auditar la integridad de saldos en cualquier momento:
```bash
php artisan db:verify-integrity
```
Para forzar la reconciliación matemática de anticipos contra los pagos descontados:
```bash
php artisan db:verify-integrity --reconcile
```

---

## 📋 Requisitos del Entorno

Antes de comenzar, asegúrate de tener instalado en tu computadora:

* **PHP** >= 8.4 y < 9.0 (`^8.4` en `composer.json`) (con extensiones `pdo_sqlite`, `mbstring`, `openssl`, `curl`)
* **Composer** (gestor de dependencias de PHP)
* **Node.js** >= 18.x y **NPM**
* **Git**

---

## 🚀 Pasos para Clonar e Instalar el Proyecto

Sigue estos pasos en orden para levantar el sistema localmente desde cero:

### 1. Clonar el Repositorio
Abre tu terminal y ejecuta:
```bash
git clone https://github.com/lucybeltran/sistema-pagos.git
cd sistema-pagos
```

### 2. Instalar Dependencias de PHP (Composer)
```bash
composer install
```

### 3. Instalar Dependencias de JavaScript (NPM)
```bash
npm install
```

### 4. Configurar el Archivo de Entorno (`.env`)
Copia el archivo de ejemplo para crear tu archivo `.env`:
* **Windows (PowerShell):**
  ```powershell
  copy .env.example .env
  ```
* **Linux / macOS / Git Bash:**
  ```bash
  cp .env.example .env
  ```

### 5. Generar la Clave de la Aplicación (App Key)
```bash
php artisan key:generate
```

### 6. Configurar y Preparar la Base de Datos (SQLite)

El proyecto utiliza **SQLite** por defecto con zona horaria oficial `America/La_Paz`.

1. Crea el archivo de base de datos vacío si no existe:
   * **Windows (PowerShell):**
     ```powershell
     New-Item -ItemType File -Path database/database.sqlite -Force
     ```
   * **Linux / macOS / Git Bash:**
     ```bash
     touch database/database.sqlite
     ```
2. Ejecutar las migraciones y poblar la base de datos con los datos iniciales y el usuario administrador:
   ```bash
   php artisan migrate:fresh --seed
   ```

### 7. Compilar los Assets (CSS / JS con Vite)

Para compilar los estilos y scripts en modo desarrollo:
```bash
npm run dev
```
*(Opcional: Si quieres compilar para producción, ejecuta `npm run build`)*

### 8. Iniciar el Servidor de Desarrollo (Laravel)

En otra ventana de la terminal, ejecuta:
```bash
php artisan serve
```

El sistema estará disponible en: **`http://127.0.0.1:8000`**

---

## 🔑 Credenciales de Acceso por Defecto

Al ejecutar el seeder (`php artisan db:seed`), se creará automáticamente la cuenta del administrador:

* **Correo Electrónico:** `admin@mina.com`
* **Contraseña:** `admin123`

---

## ✨ Módulos y Funcionalidades del Sistema

### 📊 1. Tablero Principal (Dashboard)
- **Cajas de KPI**: Saldo Sobrante en Caja, Total Recargado, Gastos en Planillas y Gastos en Anticipos.
- **Top Producción**: Ranking visual de trabajadores con mayor volumen contratado.
- **Gráficos interactivos**: Evolución mensual de pagos y producción por bocamina.

### 👷 2. Personal y Contratos (Trabajadores)
- **Registro Inteligente**: Formulario con asignación de Código Correlativo, CI, Teléfono, Bocamina, Cargo y Tipo de Contrato (Metros, Volquetas, Sereno, Contratista).
- **Acceso a Historial Directo**: Botón **Historial** en cada fila para acceder inmediatamente al reporte individual pre-filtrado del trabajador.
- **Exportación Rápida**: Botón **Exportar a Reportes** para pasar directamente al módulo de reportes.

### 💰 3. Liquidación de Pagos y Caja Chica
- **Liquidación Automática**: Cálculo en tiempo real de Subtotal, Bonos (+), Descuentos (-) y Deducción de Anticipos (-).
- **Control de Sobrantes de Efectivo**: Conversión automática a vales de anticipo cuando se entrega un monto mayor al neto liquidado.
- **Edición de Pagos**: Opción de corregir un pago registrado erróneamente mediante la ruta `Editar Pago`. Todo pago modificado muestra el distintivo **`[EDITADO]`**.
- **Caja Chica / Inyecciones**: Gestión de recargas a la caja chica con cuadre perfecto de fondos.

### 💵 4. Historial de Anticipos
- **Filtrado en Tiempo Real (AJAX)**: Filtros instantáneos por Bocamina, Trabajador y Estado de Saldo (`Pendiente` o `Descontado`) sin recargar la página.
- **Generación e Impresión de Vales**: Vales imprimibles con numeración correlativa y montos en letras en formato oficial bancario (`SON: XX/100 BOLIVIANOS`).

### 📊 5. Módulo de Reportes del Personal (4 Pestañas ERP)
1. 📈 **Resumen General**: Tarjetas KPI (`Total Pagado`, `Total Anticipos`, `Saldo de Caja del Personal` y `Trabajadores Activos`) más gráficos interactivos de gastos por semana y gastos por bocamina.
2. 👷 **Trabajadores**: Filtros combinados por Trabajador, Cargo, Contrato, Bocamina y Rango de Fechas.
3. ⛏️ **Bocaminas**: Totales gastados por bocamina y resumen individual por trabajador.
4. 💵 **Anticipos**: Reporte detallado de vales emitidos, montos anticipados, saldos pendientes y montos descontados.
* **Exportación y Salida**: Botones globales para **Imprimir**, **Exportar a PDF** y **Exportar a Excel** en todas las pestañas.

### 💎 6. Comercialización de Minerales (Módulo 2)
- Control de transacciones de compra y venta de lotes minerales, peso neto seco, leyes y liquidaciones de venta.

---

## 🛠️ Tecnologías Utilizadas

* **Backend**: Laravel 13.x (`^13.8` en `composer.json`; `v13.16.1` en `composer.lock`) (PHP >= 8.4 y < 9.0)
* **Frontend**: Blade, Tailwind CSS 4.0, Alpine.js, Chart.js
* **Build Tool**: Vite 8.x
* **Base de Datos**: SQLite (con zona horaria `America/La_Paz`)

---

## 📝 Licencia

Este proyecto es software privado para control operativo de empresas mineras. Todos los derechos reservados.
