<?php namespace hydros_final\Http\Controllers;

use hydros_final\Http\Requests;
use hydros_final\Http\Controllers\Controller;
use hydros_final\usuario as Usuario;
use hydros_final\rol as Rol;

use View;
use Input; 
use Carbon\Carbon;
use Session;
use Redirect;
use Log;

use Illuminate\Http\Request;

class UsuarioController extends Controller {
	
	public function index(){
		$usuarios = Usuario::all(); // Recogemos todos los usuarios
		
		return View::make('usuarios.lista')->with('usuarios', $usuarios);	
	
	}


	public function create()
	{
		$roles = Rol::lists('nombre', 'id'); // Devuelve un Array asociativo con los campos [ 'nombre_rol' => 'id_rol']
		$usuario = Usuario::find($id);
		return View::make('usuarios.alta')->with( [ 'usuario' => $usuario, 'roles' => $roles ] ); // Creamos la vista altaUsuario pasandole el Array asociativo de Roles
	}
	
	public function usu(){
		
		return "PENE";
	}


	public function store() {	
		// Creamos el usuario, después le añadimos los datos
		$usuario = new Usuario;
		// Campos obligatorios
		$usuario->email = Input::get('email');
		$usuario->tipo = Input::get('tipo');
		$usuario->rol = Input::get('rol');
		$usuario->contraseña = \Hash::make(Input::get('contraseña'));
		
		$usuario->nombre = Input::get('nombre');
		$usuario->apellidos = Input::get('apellidos');
		$usuario->telefono = Input::get('telefono');
		
		// Guardamos el usuario con sus datos y pillamos el estado del guardado
		try{
			$usuario->save();
			
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensaje','Guardado correctamente!');
			Session::flash('class', 'success');
		
			
		}catch(\Illuminate\Database\QueryException $e){			// http://stackoverflow.com/questions/26363271/laravel-check-for-constraint-violation
			// Controlamos la excepcion de BD
			
			if($e->errorInfo[1]==1452)  // Violación de clave foránea inexistent e
			{
				Session::flash('mensaje','El rol "' . $usuario->rol . '" no está registrado. Por favor, elija uno existente.');
				
			}
			elseif($e->errorInfo[1]==1062)// Violación de clave única duplicada
			{
				Log::info('User failed to login.', ['id' => $usuario->email]);
				Session::flash('mensaje','El usuario ' . $usuario->email . ' ya está registrado. Por favor, introduzca un email nuevo.');
			}
			else
			{
				Session::flash('mensaje','ERROR: ' . $e->errorInfo[1]  . '\n\n' . $e->errorInfo[2] );
			}
			
			Session::flash('class', 'danger');
			return redirect()->back()->withInput();
			
		}catch (Exception $e){
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensaje','Oops... ha ocurrido un error :(');
			Session::flash('class', 'danger');
			return redirect()->back()->withInput();
		}
		
		return redirect('usuarios');
		
	}
	
	
	

	public function show($id)
	{

		$usuario = Usuario::find($id);
		return View::make('usuarios.perfil')->with('usuario', $usuario);
	
	}

	public function edit($id)
	{
		$usuario = Usuario::find($id);
		$roles = Rol::lists('nombre', 'id');
		return View::make('usuarios.editar')->with(['usuario' => $usuario,'roles' => $roles]);
	}

	public function update($id)
	{	
		
		// Buscamos al usuario con sus datos
		$usuario = Usuario::find($id);
		// Sobreescribimos los datos 
		$usuario->email = Input::get('email');
		$usuario->tipo = Input::get('tipo');
		$usuario->rol = Input::get('rol');
		$usuario->contraseña = \Hash::make(Input::get('contraseña'));
		
		$usuario->nombre = Input::get('nombre');
		$usuario->apellidos = Input::get('apellidos');
		$usuario->telefono = Input::get('email');
		
		// Guardamos el usuario con sus datos y pillamos el estado del guardado
		try{
			// Push para guardar los datos y sus relaciones 
			$usuario->push();
			
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensaje','Usuario ' . $usuario->email . ' guardado correctamente!');
			Session::flash('class', 'success');
		
			
		}catch (Exception $e){
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensaje','Oops... ha ocurrido un error :(');
			Session::flash('class', 'danger');
			return redirect()->back()->withInput();
		}
		
		return redirect('usuarios.lista');
	}


	public function destroy($id)
	{	

		
		// Guardamos el usuario con sus datos y pillamos el estado del guardado
		try{
			$usuario = Usuario::where('id', $id)->delete();
			
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensaje', ' Eliminado correctamente!');
			Session::flash('class', 'success');
		
			
		}catch (Exception $e){
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensaje','Oops... ha ocurrido un error:: ' . $e->getMessage());
			Session::flash('class', 'danger');
			
		}
		$usuarios = Usuario::all(); 
		return redirect('usuarios.lista');
	//	return View::make('admin/listadoUsuarios')->with('usuarios', $usuarios);
	
	
	} 

}
