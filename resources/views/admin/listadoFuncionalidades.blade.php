@extends('app')

@section('css')
    	<link href="/css/listadoRoles.css" rel="stylesheet">
    	<link href="/css/filtrosUsuarios.css" rel="stylesheet">
@endsection

@section('content')

@if (Session::has('mensaje'))
    <div class="alert alert-info">{{ Session::get('mensaje') }}</div>
@endif
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
	        <div class="panel panel-primary filterable" id="style_div_users" >
	            <div class="panel-heading" id="header_users" >
	                <h3 class="panel-title"> Listado de Funcionalidades</h3></h3>
	                <div>
	                     <a href="{{ URL::to('funcionalidades/crear') }}" class="btn btn-primary btn-xs pull-right"><b>+</b> Nueva Funcionalidad</a>
	                </div>
	            </div>
	            
	            <table class="table">
	                <thead>
	                    <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha Alta</th>
                        <th>Fecha Modificación</th>
                        <th class="text-center">Action</th>
	                </thead>
	                <tbody>
	               	@foreach($funcionalidades as $funcionalidad)
			        <tr>
			            <td>{{ $funcionalidad->id }}</td>
                        <td>{{ $funcionalidad->nombre }}</td>
                         <td>{{ str_limit($funcionalidad->descripcion, $limit = 30, $end = '...') }}</td>
                        <td>{{ $funcionalidad->created_at }}</td>
                        <td >{{ $funcionalidad->updated_at }}</td>
                        <td  class="text-center">
                        		{!! Form::open(array('class' => 'form-inline', 'method' => 'GET', 'route' => array('funcionalidades.editar', $funcionalidad->id))) !!}
                        	   		{!! Form::submit('Editar', array('class' => 'btn btn-info btn-xs')) !!}
                        	  {!! Form::close() !!}
                        	  
                        	  {!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('funcionalidad.borrar', $funcionalidad->id))) !!}
                        	   		{!! Form::submit('Eliminar', array('class' => 'btn btn-danger btn-xs')) !!}
                        	  {!! Form::close() !!}
                        </td>
		        	</tr>
			        @endforeach
	                </tbody>
	            </table>
	        </div>

		</div>
	</div>
</div>
@endsection