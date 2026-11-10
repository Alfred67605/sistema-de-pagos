<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TipoContrato;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Inicializa el sistema en blanco únicamente con la cuenta administradora
     * y las opciones base del selector de tipos de contrato.
     */
    public function run(): void
    {
        // 1. Usuario Administrador por defecto para acceso inicial
        User::firstOrCreate(
            ['email' => 'admin@mina.com'],
            [
                'name' => 'Administrador Minero',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Opciones del selector de Tipos de Contrato (necesarias para el desplegable de registro de personal)
        $tiposContrato = [
            'Por saco',
            'Por volqueta',
            'Por metro avanzado',
            'Por viaje',
            'Mensual',
            'Diario',
        ];

        foreach ($tiposContrato as $nombre) {
            TipoContrato::firstOrCreate(
                ['nombre' => $nombre],
                ['estado' => 'activo']
            );
        }
    }
}
