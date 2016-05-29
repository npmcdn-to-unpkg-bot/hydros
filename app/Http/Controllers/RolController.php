<?php namespace hydros_final\Http\Controllers;

use hydros_final\Http\Requests;
use hydros_final\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use hydros_final\rol as Rol;
use hydros_final\funcionalidad as Funcionalidad;
use View;
use Session;
use Carbon\Carbon;

use Illuminate\Http\Request;

class RolController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$roles = Rol::all();
		return View::make('admin/listadoRoles')->with('roles',$roles);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(){
		$rol = new Rol();
   		 $funcionalidades = [''=>'Asocia una funcionalidad ...'] + Funcionalidad::lists('nombre', 'id');
   		 
   		$view = View::make('admin/altaRoles');
   		$view->rol = $rol;
   		$view->funcionalidades = $funcionalidades;
   		return $view;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		
      	$rol = new Rol();
     	$validacion = $rol->validar(Input::all());
        if ($validacion->passes()) {
                
                // store
			 $rol->nombre = Input::get('nombre');
			 $rol->descripcion = Input::get('descripcion');
			 $rol->created_at = Carbon::now()->format('Y-m-d H:i:s');
			 $rol->save();

            // redirect
            Session::flash('mensaje', 'El rol '. $rol->nombre .'ha sido creado correctamente!');
            return Redirect::to('roles');
                
        } else {
        	//$errores = $validacion->messages();
             return Redirect::to('roles/crear')
             	->withInput()
             	//->with('errores', $errores)
                ->withErrors($validacion);
        }
        
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		$rol = Rol::find($id);
		return View::make('admin/detalleRoles')->with('rol',$rol);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id){
		
	
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id){
		
	}

}
