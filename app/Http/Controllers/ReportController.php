<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // REPORTE 1: Clientes por zona geográfica
    public function clientesPorZona()
    {
        // 1. Consulta con Query Builder
        $zonas = DB::table('clients')
            ->select(
                'zona_geografica',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('zona_geografica')
            ->orderByDesc('total')
            ->get();

        // 2. Total general
        $totalGeneral = $zonas->sum('total');

        // 3. Agregar porcentaje
        $zonasConPorcentaje = $zonas->map(function ($zona) use ($totalGeneral) {
            $zona->porcentaje = $totalGeneral > 0
                ? round(($zona->total / $totalGeneral) * 100, 2)
                : 0;
            return $zona;
        });

        // 4. Datos para el gráfico
        $labels = $zonasConPorcentaje->pluck('zona_geografica')->toArray();
        $data = $zonasConPorcentaje->pluck('total')->toArray();

        return view('reports.zonas', compact(
            'zonasConPorcentaje', 'totalGeneral', 'labels', 'data'
        ));
    }
/**
 * REPORTE 2: Interacciones por asesor
 * Usa LEFT JOIN + COUNT condicional
 */
 public function interaccionesPorAsesor()
 {
 $asesores = DB::table('users')
 ->select(
 'users.id',
 'users.name',
 DB::raw("COUNT(CASE WHEN interactions.tipo_interaccion = 'Llamada'
THEN 1 END) AS llamadas"),
 DB::raw("COUNT(CASE WHEN interactions.tipo_interaccion = 'Visita'
THEN 1 END) AS visitas"),
 DB::raw("COUNT(CASE WHEN interactions.tipo_interaccion = 'WhatsApp'
THEN 1 END) AS whatsapp"),
 DB::raw('COUNT(interactions.id) AS total')
 )
 ->leftJoin('clients', 'clients.user_id', '=', 'users.id')
 ->leftJoin('interactions', 'interactions.client_id', '=', 'clients.id')
 ->groupBy('users.id', 'users.name')
 ->orderByDesc('total')
 ->get();
 $labels = $asesores->pluck('name')->toArray();
 $llamadas = $asesores->pluck('llamadas')->toArray();
 $visitas = $asesores->pluck('visitas')->toArray();
 $whatsapp = $asesores->pluck('whatsapp')->toArray();
 return view('reports.interacciones', compact(
 'asesores', 'labels', 'llamadas', 'visitas', 'whatsapp'
 ));
 }
}