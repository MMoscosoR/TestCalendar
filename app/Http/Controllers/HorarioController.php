<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use http\Env\Response;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function store(Request $request){
        $data=$request->validate([
            'form_date'=>'date|required',
            'form_from'=>'required',
            'form_to'=>'required',
            'nombre_curso'=>'required',
            'descripcion'=>'nullable'
        ]);
        $response=Horario::create($data);
        if($response){
            return response()->json([
                'id'=>$response->id,
                'title'=>$response->nombre_curso,
                'start'=>$response->form_date.'T'.$response->form_from,
                'end'=>$response->form_date.'T'.$response->form_to,
                'description'=>$response->descripcion??'Sin descripción'
            ]);
        }else{
            return response()->json('Se producko un error',501);
        }

    }
    public function index(Request $request){
        $from=date('Y-m-d',strtotime($request->start));
        $to=date('Y-m-d',strtotime($request->end));

        $horarios=Horario::whereBetween('form_date',[$from,$to])->get();
        $eventos=$horarios->map(function($horario){
           return [
               'id'=>$horario->id,
               'title'=>$horario->nombre_curso,
               'start'=>$horario->form_date.'T'.$horario->form_from,
               'end'=>$horario->form_date.'T'.$horario->form_to,
               'description'=>$horario->descripcion??'Sin descripción'
           ];
        });
        return response()->json($eventos);
    }
    public function show(){

    }

    public function update(Request $request,Horario $horario){
        $data=$request->validate([
            'form_date'=>'date|required',
            'form_from'=>'required',
            'form_to'=>'required',
            'nombre_curso'=>'required',
            'descripcion'=>'nullable'
        ]);
        $horario->nombre_curso=$data['nombre_curso'];
        $horario->form_date=$data['form_date'];
        $horario->descripcion=$data['descripcion'];
        $horario->form_to=$data['form_to'];
        $horario->form_from=$data['form_from'];
        $horario->save();
        return response()->json($request);
    }

    public function destroy(Horario $horario){
        if($horario->delete()){
            return response()->json('ok');
        }else{
            return response()->json('Ocurrio un error',501);
        }
    }
}
