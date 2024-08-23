<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consulta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
    
        if ($usuario->tipo == 'medico') {
            $consultas = Consulta::where('crm_medico', $usuario->crm_medico)
                                 ->with('medico')
                                 ->get();
            $consultasHoje = Consulta::where('crm_medico', $usuario->crm_medico)
                                     ->whereDate('data', Carbon::today()) 
                                     ->with('medico')
                                     ->get();
        } else {
            $consultas = Consulta::where('rg_usuario', $usuario->rg_usuario)
                                 ->with('paciente')
                                 ->get();
            $consultasHoje = Consulta::where('rg_usuario', $usuario->rg_usuario)
                                     ->whereDate('data', Carbon::today()) 
                                     ->with('paciente')
                                     ->get();
        }
    
        return view('usuarios.dashboard', compact('consultas', 'consultasHoje'));
    }
    
}
