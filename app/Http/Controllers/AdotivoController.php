<?php

namespace Casa\Http\Controllers;

use Casa\Etnia;
use Casa\Adotivo;
use Casa\Adotante;
use Casa\Restricao;
use Casa\Escolaridade;
use Casa\AdotivoStatus;
use Casa\Nascionalidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Casa\Http\Requests\AdotivoRequest;

class AdotivoController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $adotivos = Adotivo::orderBy('nome')->paginate(10);
        return view('adotivo.index', compact('adotivos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $adotantes = Adotante::pluck('nome', 'id');
        $status    = AdotivoStatus::pluck('nome', 'id');     
        $etnias    = Etnia::pluck('nome', 'id');
        $nascionalidades = Nascionalidade::pluck('nome', 'id');
        $escolaridades = Escolaridade::where('id', '<', 6)->pluck('nome', 'id'); 
        $restricoes = Restricao::pluck('nome', 'id');

        return view('adotivo.create', compact(
            'adotantes', 
            'status',
            'etnias',
            'nascionalidades',
            'escolaridades',
            'restricoes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdotivoRequest $request) {
        $adotivo = new Adotivo($request->all());

        #Usuário logado no sistema.
        $usuario = Auth::user();

        $adotivo->setInstituicao($usuario->instituicao_id);
        $adotivo->setUsuario($usuario->id);

        $adotivo->save();

        flash("Adotivo ".$adotivo->nome." Incluído com Sucesso!", 'success');
        return redirect('adotivos');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $adotivo   = Adotivo::findOrFail($id);
        $adotantes = Adotante::all()->pluck('nome', 'id');
        $status    = AdotivoStatus::pluck('nome', 'id');
        $etnias    = Etnia::pluck('nome', 'id');
        $nascionalidades = Nascionalidade::pluck('nome', 'id');
        $escolaridades = Escolaridade::where('id', '<', 6)->pluck('nome', 'id'); 
        $adotante  = $adotivo->adotantes()->first();
        $restricoes = Restricao::pluck('nome', 'id');

        return view('adotivo.edit', compact(
            'adotivo', 
            'adotantes', 
            'adotante',
            'status',
            'etnias',
            'nascionalidades',
            'escolaridades',
            'restricoes'
        ));   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdotivoRequest  $request, $id) {
        $adotivo = Adotivo::findOrFail($id);
        $adotivo->update($request->all());

        flash("Adotivo ".$adotivo->nome." Alterado com Sucesso!", "success");

        return redirect('adotivos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        Adotivo::findOrFail($id)->delete();

        flash("Adotivo Inativado(a) com Sucesso", 'danger');
        return json_encode(['status' => true]);
    }

    public function buscar(Request $request) {
        $adotivos = Adotivo::where('nome', 'like', '%'.$request->inputBusca.'%')
        ->orderBy('nome')
        ->paginate(10);
        return view('adotivo.index', compact('adotivos'));
    }
}
