<?php

namespace App\Http\Controllers;

use App\Models\Inscricao;
use Illuminate\Http\Request;
use App\Models\Vaga;
use Illuminate\Support\Facades\Auth;

class InscricaoController extends Controller
{
    public function add(Vaga $vaga)
    {
        $inscricao = Inscricao::create(['usuario_id' => Auth::id(), 'vaga_id'=> $vaga->id]);

        return redirect()->route('vagas.show', $inscricao->id)
        ->with('success', 'Inscrição adicionada a vaga.');
    }
}
