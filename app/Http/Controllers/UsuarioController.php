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
use Storage;
use FileSystem;

use Illuminate\Http\Request;

class UsuarioController extends Controller {
	
	public function usuarioConectado(){
		return Session::get('usuario');
	}
	
	public function index(){
		$usuarios = Usuario::all(); // Recogemos todos los usuarios
		$roles = Rol::lists('nombre', 'id'); // Devuelve un Array asociativo con los campos [ 'id_rol' => 'nombre_rol']
		return View::make('usuarios.lista')->with(['usuarios' => $usuarios, 'roles' => $roles, 'usuarioConectado', self::usuarioConectado()]);	
	
	}

	public function create()
	{
		$roles = Rol::lists('nombre', 'id'); // Devuelve un Array asociativo con los campos [ 'nombre_rol' => 'id_rol']
		
		$view = View::make('usuarios.alta');
		$view->roles = $roles;
		return $view;
		
		
	}
	


	public function store() {	
		// Creamos el usuario, después le añadimos los datos
		$usuario = new Usuario;
		
		$validacion = $usuario->validar(Input::all());
							//		var_dump($validacion);

		if(!$validacion->fails()){
			// Guardamos el usuario con sus datos y pillamos el estado del guardado
			try{
				$email = $usuario->email = Input::get('email');
				$tipo = $usuario->tipo = Input::get('tipo');
				$usuario->rol = Input::get('rol');
				// Si se ha introducido una nueva contraseña se guarda, sino, no se hace nada
				$usuario->contraseña = \Hash::make(Input::get('contraseña'));
				
				$usuario->nombre = Input::get('nombre');
				$usuario->apellidos = Input::get('apellidos');
				$usuario->telefono = Input::get('telefono');
				
				$usuario->save();
				
			
				
				if($tipo == 0){
					// Una vez creado el usuario [y no es administrador] se crea el directorio con el email del mismo 
				
				$disk = Storage::disk('local');
				$disk->makeDirectory('/'.$usuario->email);	
				/*	$aws = \Storage::disk('s3');
					$aws->makeDirectory($email);
					$aws->copy('_defecto/*', $email.'/'); */
				}
				
				// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
				Session::flash('mensajes','Usuario '. $usuario->email  .' creado correctamente!');
				Session::flash('class', 'success');
				
				
				return redirect('/usuarios');
				
			}catch(\Illuminate\Database\QueryException $e){	// Controlamos la excepcion de BD http://stackoverflow.com/questions/26363271/laravel-check-for-constraint-violation
				$errores = 'HA OCURRIDO UN ERROR DE BASE DE DATOS<br/>' ;

				if($e->errorInfo[1]==1452)  // Violación de clave foránea inexistente
				{
					$errores .= 'El rol "' . $usuario->rol . '" no está registrado. Por favor, elija uno existente.';
					
				}
				elseif($e->errorInfo[1]==1062)// Violación de clave única duplicada
				{
					$errores .= 'El usuario ' . $usuario->email . ' ya está registrado. Por favor, introduzca un email nuevo.';
				}
				else
				{
					$errores .= 'ERROR: ' . $e->errorInfo[1]  . '\n\n' . $e->errorInfo[2];
				}
				Session::flash('mensajes', $errores);
				Session::flash('class', 'danger');
				return redirect()->back()->withInput()->withErrors($errores);
				
			}catch (Exception $e){
				return redirect()->back()->withInput()->withErrors(['Oops... ha ocurrido un error :(' . $e->getCode() .'):']);
			}
		}else{
			return  redirect()->back()->withInput()->withErrors($validacion->messages());	
	
		}
		
		
	}
	

	public function show($id)
	{

		$usuario = Usuario::find($id);
		$roles = Rol::lists('nombre', 'id'); // Devuelve un Array asociativo con los campos [ 'id_rol' => 'nombre_rol']

		return View::make('usuarios.perfil')->with(['usuario' => $usuario, 'roles' => $roles]);
	
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
		$usuario->tipo = Input::get('tipo');
		$usuario->rol = Input::get('rol');
		// Si se ha introducido una nueva contraseña se guarda, sino, no se hace nada
		if(strlen(trim(Input::get('contraseña')))>0){
			$usuario->contraseña = \Hash::make(Input::get('contraseña'));
		}
		$usuario->nombre = Input::get('nombre');
		$usuario->apellidos = Input::get('apellidos');
		$usuario->telefono = Input::get('email');
		
		// Guardamos el usuario con sus datos y pillamos el estado del guardado
		try{
			// Push para guardar los datos y sus relaciones 
			$usuario->save();
			
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensajes','Usuario ' . $usuario->email . ' actualizado correctamente!');
			Session::flash('class', 'success');
		
			
		}catch (Exception $e){
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensajes','Oops... ha ocurrido un error :(');
			Session::flash('class', 'danger');
			return redirect()->back()->withInput();
		}
		
		return Redirect::to('/usuarios/')->with('id', $id);
	}


	public function destroy($id)
	{	

		
		// Guardamos el usuario con sus datos y pillamos el estado del guardado
		try{
			$usuario = Usuario::find($id);
			$email = $usuario->email;
			
			if($email == Session::get('usuario')){
				// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
				Session::flash('mensajes','Error: No puedes eliminar tu propio perfil!');
				Session::flash('class', 'danger');	
				
				return redirect()->back()->withInput();
			}
			
			$usuario->delete();
			
			// Una vez creado el usuario se crea el directorio con el email del mismo 
			Storage::disk('local')->deleteDirectory($email);
			
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensajes', 'El usuario '. $email .' fue eliminado correctamente! ');
			Session::flash('class', 'success');
		
			
		}catch (Exception $e){
			// Pasamos a la sesion los campos 'message' y 'class' con los datos a manejar
			Session::flash('mensajes','Oops... ha ocurrido un error:: ' . $e->getMessage());
			Session::flash('class', 'danger');
			
		}
		$usuarios = Usuario::all(); 
		return redirect('/usuarios')->with('usuarios', $usuarios);
	//	return View::make('admin/listadoUsuarios')->with('usuarios', $usuarios);
	
	
	} 

}
