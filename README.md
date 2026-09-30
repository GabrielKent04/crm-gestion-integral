 
INFORME TÉCNICO — TALLER SEMANA 9
 
SQL, Query Builder y Reportes del CRM
Proyecto: CRM "Gestión Integral de Negocios" — ALP-365 Docente: Ing. José Daniel Cadenas L. Fecha: 30/09/2026
Estudiante: Gabriel Corobo Cédula: 31561092 Sección: _A_ Grupo N°: ____ GitHub: https://github.com/GabrielKent04/crm-gestion-integral.git
 
📌 EVIDENCIA 1 — Capturas de los 2 reportes funcionando (2%)
Reporte 1 — Clientes por Zona:  
Reporte 2 — Interacciones por Asesor: 
 
 
📌 EVIDENCIA 2 — Código comentado (2%)
Copie el método de UNO de los reportes y coméntelo línea por línea:

// reporte 1: clientes por zona
public function clientesPorZona()
{
    // nos conectamos a la tabla de clientes de la base de datos
    $zonas = DB::table('clients')
        // decimos que columnas queremos traer
        ->select(
            // traemos el nombre de la zona
            'zona_geografica',
            // usamos sql crudo para contar los clientes de esa zona y le ponemos "total"
            DB::raw('COUNT(*) as total')
        )
        // agrupamos por zona para que el count haga su trabajo por cada una
        ->groupBy('zona_geografica')
        // ordenamos los resultados de mayor a menor cantidad
        ->orderByDesc('total')
        // disparamos la consulta para traer los datos
        ->get();

    // sumamos todos los totales para saber cuantos clientes hay en general
    $totalGeneral = $zonas->sum('total');

    // recorremos las zonas con map para calcular el porcentaje de cada una
    $zonasConPorcentaje = $zonas->map(function ($zona) use ($totalGeneral) {
        // comprobamos que el total general no sea 0 para que no explote la division
        $zona->porcentaje = $totalGeneral > 0
            // hacemos la formula del porcentaje y redondeamos a 2 decimales
            ? round(($zona->total / $totalGeneral) * 100, 2)
            // si el total general era 0, el porcentaje queda en 0
            : 0;
        // devolvemos la zona pero ahora con el campo de porcentaje agregado
        return $zona;
    });

    // agarramos solo los nombres de las zonas y los volvemos un arreglo para usarlos de etiquetas
    $labels = $zonasConPorcentaje->pluck('zona_geografica')->toArray();
    // agarramos solo los totales para armar las barras del grafico
    $data = $zonasConPorcentaje->pluck('total')->toArray();

    // mandamos todo esto a la vista blade que esta en la carpeta reports
    return view('reports.zonas', compact(
        // aca pasamos las variables que va a usar la vista y el grafico
        'zonasConPorcentaje', 'totalGeneral', 'labels', 'data'
    ));
}
 
📌 EVIDENCIA 3 — Respuestas a 3 preguntas conceptuales (6%)
Pregunta 1: _______________________________________________ Respuesta:
Pregunta 2: _______________________________________________ Respuesta:
Pregunta 3: _______________________________________________ Respuesta:
 
📌 EVIDENCIA 4 — Reflexión breve (150 palabras) (1%)
1.	¿Qué concepto fue más difícil de entender?
Para m el concepto más difícil de entender fue el uso de LEFT JOIN combinado con DB::raw en el segundo reporte. Específicamente me costó un poco captar cómo usar el COUNT(CASE WHEN...) para ir contando por separado las llamadas, visitas y WhatsApp dentro de la misma consulta de Query Builder sin que se mezclaran los datos, es un salto grande pasar de un SELECT básico a mezclar el código de Laravel con funciones condicionales de SQL.

2.	¿Cómo se aplican estos reportes al CRM?
creo que son los que le dan verdadero valor a la base de datos. El reporte de zonas geográficas permite ver visualmente dónde está la mayor concentración de clientes, lo que ayudaría a la empresa a planificar rutas o enfocar publicidad, por otro lado, el reporte de interacciones por asesor es una excelente herramienta para medir el rendimiento del personal, ya que permite evaluar de forma gráfica quién está trabajando más activamente y por qué canal se comunican má
 
📌 EVIDENCIA 5 — Commit en GitHub (0%)
 
