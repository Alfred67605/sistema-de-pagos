<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class BackupController extends Controller
{
    protected string $backupDir;

    /**
     * Ordered list of tables for safe export and foreign-key-safe restoration.
     */
    protected array $backupTables = [
        'users',
        'bocaminas',
        'tipos_contrato',
        'tipos_trabajo',
        'trabajadores',
        'fondos_pagos',
        'anticipos',
        'pagos',
        'pago_items',
        'pago_anticipo',
        'servicios_externos',
        'caja_minerales_recargas',
        'transacciones_minerales',
        'lote_analisis',
    ];

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
    }

    public function index()
    {
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }

        // Load settings and check if a scheduled backup should run automatically
        $settingsPath = storage_path('app/backup_settings.json');
        $settings = [
            'activo' => false,
            'frecuencia' => 'mensual',
            'hora' => '23:30',
            'last_auto_backup' => null,
        ];

        if (File::exists($settingsPath)) {
            $decoded = json_decode(File::get($settingsPath), true);
            if (is_array($decoded)) {
                $settings = array_merge($settings, $decoded);
            }
        }

        $this->checkAndRunScheduledBackup($settings, $settingsPath);

        // List backup ZIP files in directory
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'zip') {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'size' => $this->formatBytes($file->getSize()),
                    'raw_size' => $file->getSize(),
                ];
            }
        }

        usort($backups, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return view('backups.index', compact('backups', 'settings'));
    }

    public function store()
    {
        try {
            $filename = $this->generateBackupArchive('respaldo');
            return redirect()->route('backups.index')
                ->with('success', "Respaldo generado correctamente ({$filename}).");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error al generar respaldo: ' . $e->getMessage());
        }
    }

    /**
     * Generates a complete ZIP backup containing:
     * 1. backup_data.json (Universal portable snapshot for PostgreSQL, SQLite, MySQL)
     * 2. database.sqlite (Binary SQLite file when using SQLite)
     * 3. public/uploads/* (User avatars & uploaded images)
     */
    protected function generateBackupArchive(string $prefix = 'respaldo'): string
    {
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }

        $filename = $prefix . '_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        // 1. Build universal JSON snapshot of all database tables
        $snapshot = [
            'meta' => [
                'app' => 'Sistema Minero TORMAN',
                'version' => '2.5',
                'created_at' => date('c'),
                'driver' => config('database.default'),
            ],
            'tables' => [],
        ];

        foreach ($this->backupTables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    $rows = DB::table($table)->get()->map(function ($row) {
                        return (array) $row;
                    })->toArray();
                    $snapshot['tables'][$table] = $rows;
                }
            } catch (\Throwable $e) {
                // Skip table if inaccessible
            }
        }

        $entries = [];
        $entries['backup_data.json'] = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // 2. Include raw database.sqlite if available on disk
        $sqlitePath = config('database.connections.sqlite.database', database_path('database.sqlite'));
        if (!File::exists($sqlitePath)) {
            $sqlitePath = database_path('database.sqlite');
        }

        if (File::exists($sqlitePath)) {
            try {
                if (config('database.default') === 'sqlite') {
                    DB::statement('PRAGMA wal_checkpoint(PASSIVE);');
                }
            } catch (\Throwable $e) {
            }
            $rawSqlite = @file_get_contents($sqlitePath);
            if ($rawSqlite !== false && strlen($rawSqlite) > 0) {
                $entries['database.sqlite'] = $rawSqlite;
            }
        }

        // 3. Include uploaded avatars/images from public/uploads if present
        $uploadsDir = public_path('uploads');
        if (File::exists($uploadsDir)) {
            foreach (File::allFiles($uploadsDir) as $upFile) {
                $relPath = 'uploads/' . str_replace('\\', '/', $upFile->getRelativePathname());
                $content = @file_get_contents($upFile->getRealPath());
                if ($content !== false) {
                    $entries[$relPath] = $content;
                }
            }
        }

        // 4. Write ZIP archive (using ZipArchive or pure-PHP PKZIP fallback)
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('No se pudo crear el archivo ZIP en ' . $this->backupDir);
            }
            foreach ($entries as $entryName => $content) {
                $zip->addFromString($entryName, $content);
            }
            $zip->close();
        } else {
            $this->writeZipPurePhp($zipPath, $entries);
        }

        if (!File::exists($zipPath) || File::size($zipPath) === 0) {
            throw new \RuntimeException('El archivo de respaldo generado está vacío o no pudo guardarse en disco.');
        }

        return $filename;
    }

    public function download($filename)
    {
        $safeFilename = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $safeFilename;

        if (!File::exists($filePath)) {
            return redirect()->route('backups.index')->with('error', 'Archivo de respaldo no encontrado.');
        }

        return Response::download($filePath, $safeFilename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'nullable|file',
            'backup_file_name' => 'nullable|string',
        ]);

        $tempExtractPath = storage_path('app/temp_restore_' . uniqid());

        try {
            $zipRealPath = null;
            if ($request->hasFile('backup_file')) {
                $zipRealPath = $request->file('backup_file')->getRealPath();
            } elseif ($request->filled('backup_file_name')) {
                $safeName = basename($request->input('backup_file_name'));
                $zipRealPath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;
                if (!File::exists($zipRealPath)) {
                    return redirect()->back()->with('error', 'Archivo de respaldo no encontrado.');
                }
            } else {
                return redirect()->back()->with('error', 'Debes subir un archivo ZIP o seleccionar un respaldo de la lista.');
            }

            File::makeDirectory($tempExtractPath, 0755, true);

            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($zipRealPath) === true) {
                    $zip->extractTo($tempExtractPath);
                    $zip->close();
                } else {
                    $this->extractZipPurePhp($zipRealPath, $tempExtractPath);
                }
            } else {
                $this->extractZipPurePhp($zipRealPath, $tempExtractPath);
            }

            $jsonBackupFile = $tempExtractPath . DIRECTORY_SEPARATOR . 'backup_data.json';
            $extractedSqlite = $tempExtractPath . DIRECTORY_SEPARATOR . 'database.sqlite';

            if (File::exists($jsonBackupFile)) {
                $payload = json_decode(File::get($jsonBackupFile), true);
                if (!is_array($payload) || !isset($payload['tables'])) {
                    throw new \RuntimeException('El archivo backup_data.json dentro del respaldo es inválido.');
                }
                $this->restoreFromStructuredJson($payload['tables']);
            } elseif (File::exists($extractedSqlite)) {
                $this->restoreFromSqliteFile($extractedSqlite);
            } else {
                throw new \RuntimeException('El archivo ZIP no contiene un respaldo válido (backup_data.json o database.sqlite).');
            }

            // Restore uploads directory if present in backup
            $extractedUploads = $tempExtractPath . DIRECTORY_SEPARATOR . 'uploads';
            if (File::exists($extractedUploads)) {
                File::copyDirectory($extractedUploads, public_path('uploads'));
            }

            File::deleteDirectory($tempExtractPath);

            return redirect()->route('backups.index')
                ->with('success', 'Base de datos restaurada correctamente desde el respaldo seleccionado.');
        } catch (\Throwable $e) {
            if (File::exists($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }
            return redirect()->back()
                ->with('error', 'Error durante la restauración: ' . $e->getMessage());
        }
    }

    protected function restoreFromStructuredJson(array $tablesData): void
    {
        $driver = config('database.default');

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = replica;');
        }

        DB::beginTransaction();
        try {
            // Delete in reverse dependency order
            foreach (array_reverse($this->backupTables) as $table) {
                if (isset($tablesData[$table]) && Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }

            // Insert in forward dependency order
            foreach ($this->backupTables as $table) {
                if (!empty($tablesData[$table]) && Schema::hasTable($table)) {
                    foreach (array_chunk($tablesData[$table], 200) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }

                    // Reset sequence in PostgreSQL so subsequent inserts get the right ID
                    if ($driver === 'pgsql' && Schema::hasColumn($table, 'id')) {
                        DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), COALESCE((SELECT MAX(id) FROM {$table}), 1), true);");
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        } finally {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } elseif ($driver === 'pgsql') {
                DB::statement('SET session_replication_role = DEFAULT;');
            }
        }
    }

    protected function restoreFromSqliteFile(string $extractedSqlite): void
    {
        $driver = config('database.default');

        if ($driver === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database', database_path('database.sqlite'));
            DB::disconnect('sqlite');
            File::copy($extractedSqlite, $dbPath);
            DB::reconnect('sqlite');
            return;
        }

        // Cross-engine restore: read tables from the SQLite file using PDO and import into PostgreSQL/MySQL
        $pdo = new \PDO('sqlite:' . $extractedSqlite);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $tablesData = [];

        foreach ($this->backupTables as $table) {
            try {
                $stmt = $pdo->query("SELECT * FROM {$table}");
                if ($stmt) {
                    $tablesData[$table] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }
            } catch (\Throwable $e) {
            }
        }
        $pdo = null;

        $this->restoreFromStructuredJson($tablesData);
    }

    public function destroy($filename)
    {
        $safeFilename = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $safeFilename;

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('backups.index')->with('success', 'Respaldo eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'Archivo no encontrado.');
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'activo' => 'nullable|boolean',
            'frecuencia' => 'required|string|in:diario,semanal,mensual',
            'hora' => 'required|string',
        ]);

        try {
            $settingsPath = storage_path('app/backup_settings.json');
            $existing = [];
            if (File::exists($settingsPath)) {
                $existing = json_decode(File::get($settingsPath), true) ?: [];
            }

            $settings = array_merge($existing, [
                'activo' => $request->has('activo') ? (bool) $request->activo : false,
                'frecuencia' => $request->frecuencia,
                'hora' => $request->hora,
            ]);

            File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));

            return redirect()->route('backups.index')
                ->with('success', 'Configuración de respaldos automáticos guardada correctamente.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Error al guardar la configuración: ' . $e->getMessage());
        }
    }

    protected function checkAndRunScheduledBackup(array &$settings, string $settingsPath): void
    {
        if (empty($settings['activo'])) {
            return;
        }

        try {
            $lastRun = !empty($settings['last_auto_backup']) ? strtotime($settings['last_auto_backup']) : 0;
            $now = time();
            $freq = $settings['frecuencia'] ?? 'mensual';

            $shouldRun = false;
            if ($lastRun === 0) {
                $shouldRun = true;
            } elseif ($freq === 'diario' && ($now - $lastRun) >= 86400) {
                $shouldRun = true;
            } elseif ($freq === 'semanal' && ($now - $lastRun) >= 604800) {
                $shouldRun = true;
            } elseif ($freq === 'mensual' && ($now - $lastRun) >= 2592000) {
                $shouldRun = true;
            }

            if ($shouldRun) {
                $this->generateBackupArchive('respaldo_auto');
                $settings['last_auto_backup'] = date('Y-m-d H:i:s');
                File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
            }
        } catch (\Throwable $e) {
            // Do not block index rendering if background auto-backup fails
        }
    }

    /**
     * Pure-PHP ZIP writer fallback when ext-zip is not compiled in the container.
     */
    protected function writeZipPurePhp(string $zipPath, array $entries): void
    {
        $localHeaders = '';
        $centralDir = '';
        $offset = 0;
        $count = 0;

        foreach ($entries as $name => $data) {
            $name = str_replace('\\', '/', $name);
            $uncompressedSize = strlen($data);
            $crc = crc32($data);
            $compressedData = gzdeflate($data, 6);
            $compressedSize = strlen($compressedData);
            $nameLen = strlen($name);

            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0,
                8,
                0,
                0,
                $crc,
                $compressedSize,
                $uncompressedSize,
                $nameLen,
                0
            ) . $name . $compressedData;

            $centralHeader = pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0,
                8,
                0,
                0,
                $crc,
                $compressedSize,
                $uncompressedSize,
                $nameLen,
                0,
                0,
                0,
                0,
                32,
                $offset
            ) . $name;

            $localHeaders .= $localHeader;
            $centralDir .= $centralHeader;
            $offset += strlen($localHeader);
            $count++;
        }

        $endOfCentralDir = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            $count,
            $count,
            strlen($centralDir),
            $offset,
            0
        );

        File::put($zipPath, $localHeaders . $centralDir . $endOfCentralDir);
    }

    /**
     * Pure-PHP ZIP extractor fallback when ext-zip is not compiled in the container.
     */
    protected function extractZipPurePhp(string $zipPath, string $destDir): void
    {
        $raw = File::get($zipPath);
        $len = strlen($raw);
        $pos = 0;

        while ($pos + 30 <= $len) {
            $sig = unpack('V', substr($raw, $pos, 4))[1];
            if ($sig !== 0x04034b50) {
                break;
            }

            $header = unpack('vver/vflag/vmethod/vtime/vdate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen', substr($raw, $pos + 4, 26));
            $name = substr($raw, $pos + 30, $header['nameLen']);
            $dataPos = $pos + 30 + $header['nameLen'] + $header['extraLen'];
            $compData = substr($raw, $dataPos, $header['compSize']);

            $pos = $dataPos + $header['compSize'];

            if (substr($name, -1) === '/') {
                continue;
            }

            $content = $header['method'] === 8 ? @gzinflate($compData) : $compData;
            if ($content !== false) {
                $targetFile = $destDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $name);
                $parent = dirname($targetFile);
                if (!File::exists($parent)) {
                    File::makeDirectory($parent, 0755, true);
                }
                File::put($targetFile, $content);
            }
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
