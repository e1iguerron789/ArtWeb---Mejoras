<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\CategoriaInteres;
use App\Models\OpcionInteres;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::with('categoria:id,nombre', 'opcion:id,nombre,categoria_interes_id')
            ->get();

        return inertia('Eventos/Index', [
            'eventos' => $eventos //para
        ]);
    }

    public function create()
    {
        return inertia('Eventos/Create', [
            'categorias' => CategoriaInteres::all(),
            'opciones'   => OpcionInteres::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',

            'categoria_interes_id' => 'required|exists:categorias_interes,id',
            'opcion_interes_id'    => 'required|exists:opcion_intereses,id',

            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',

            'direccion_texto' => 'required|string',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        $validated['user_id'] = auth()->id();

        Evento::create($validated);

        return redirect()->route('eventos.mapa')
            ->with('success', 'Evento creado correctamente');
    }

    // ============================================================
    //  GET: carga la VISTA del mapa
    // ============================================================
    public function mapa()
    {
        $usuario = auth()->user();

        // Intereses del usuario
        $interesesNombres = $usuario->intereses()
            ->with('opcion')
            ->get()
            ->pluck('opcion.nombre')
            ->filter()
            ->values()
            ->toArray();

        // Eventos SIN cálculo aún
        $eventos = Evento::with([
            'categoria:id,nombre',
            'opcion:id,nombre,categoria_interes_id'
        ])
        ->get([
            'id','titulo','categoria_interes_id','opcion_interes_id',
            'latitud','longitud','fecha','hora_inicio','hora_fin','direccion_texto'
        ]);

        return inertia('Eventos/Mapa', [
            'eventos'                 => $eventos,
            'interesesUsuarioNombres' => $interesesNombres,
        ]);
    }

    // ============================================================
    //  POST: recibe ubicación → calcula distancia → retorna JSON
    // ============================================================
    public function actualizarMapa(Request $request)
    {
        $usuario = auth()->user();

        $latUsuario = $request->lat_usuario;
        $lngUsuario = $request->lng_usuario;

        // Si el navegador no envía nada → usar la guardada (si existe)
        if (!$latUsuario || !$lngUsuario) {
            $latUsuario = $usuario->latitud;
            $lngUsuario = $usuario->longitud;
        }

        // Intereses
        $interesesIds = $usuario->intereses()->pluck('opcion_interes_id')->toArray();

        $interesesNombres = $usuario->intereses()
            ->with('opcion')
            ->get()
            ->pluck('opcion.nombre')
            ->filter()
            ->values()
            ->toArray();

        // Eventos con relaciones
        $eventos = Evento::with([
            'categoria:id,nombre',
            'opcion:id,nombre,categoria_interes_id'
        ])->get();

        // CÁLCULOS
        foreach ($eventos as $ev) {

            $score = 0;

            // 1) Interés exacto
            if (in_array($ev->opcion_interes_id, $interesesIds)) {
                $score += 50;
            }

            // 2) Nombre de interés
            if ($ev->opcion && in_array($ev->opcion->nombre, $interesesNombres)) {
                $score += 30;
            }

        

            // 3) Distancia REAL (Haversine)
            $ev->distancia = null;

            if ($latUsuario && $lngUsuario) {

                $theta = $lngUsuario - $ev->longitud;

                $dist = sin(deg2rad($latUsuario)) * sin(deg2rad($ev->latitud)) +
                        cos(deg2rad($latUsuario)) * cos(deg2rad($ev->latitud)) *
                        cos(deg2rad($theta));

                $dist = acos($dist);// pasa de cos(ángulo) a ángulo (en radianes)                      
                $dist = rad2deg($dist);// convierte ese ángulo a grados
                $km = $dist * 60 * 1.1515 * 1.609344;

                $ev->distancia = round($km, 3);

                if ($km < 1) $score += 20;
                elseif ($km < 3) $score += 10;
            }

            $ev->relevancia = $score;
        }

        // Ordenar eventos
        $eventos = $eventos->sortByDesc('relevancia')->values();

        //  Vue espera "eventos"
        return response()->json([
            'eventos' => $eventos
        ]);
    }
}
