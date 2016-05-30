@extends('header')

@section('css')
    	<link href="/css/altaRoles.css" rel="stylesheet">
@endsection

@section('content')
 @if (count($errors) > 0)
	<div class="alert alert-danger">
		<strong>Whoops!</strong> Ha ocurrido un problema...<br><br>
		<ul>
			@foreach ($errors as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

   <div class="container">
<div class="col-md-5">
   
    <div class="form-area">  
    {!! Form::open(['url' => 'roles']) !!}
        
        <br style="clear:both">
                    <h3 style="margin-bottom: 25px; text-align: center;">Nuevo Rol</h3>
                
					<div class="form-group">
                        {!! Form::text('nombre', Input::old('nombre'), array('class' => 'form-control','id' => 'nombre','placeholder' => 'nombre')) !!}
                    </div>
					<div class="form-group">
					    {!! Form::select('funcionalidades', $funcionalidades , '' , array('class' => 'form-control')) !!}
					</div>
					
                    <div class="form-group">
                   
                    {!! Form::textarea('descripcion', Input::old('descripcion'), array('class' => 'form-control','id' => 'descripcion','maxlength' => '140', 'rows' => '7')) !!}
                        <span class="help-block"><p id="characterLeft" class="help-block ">Usted ha pasado el limite de caracteres</p></span>                    
                    </div>
            
        {!! Form::submit('Crear Rol', array('class' => 'btn btn-primary pull-right','id' => 'submit')) !!}
        {!! Form::close() !!}
      
    </div>
</div>
<div class="col-md-1" >
    <button id="select_funcionalities_list" style="margin-top: 163px;" class="btn btn-default btn-md"><span class="glyphicon glyphicon-chevron-right"></span></button>
</div>
<div class="col-md-5" >
     <div class="form-area" style="height: 458px;">  
       <h3 style="margin-bottom: 25px; text-align: center;">Funcionalidades asociadas</h3>
       <span id="list_funcionalities" ></span>
        <a href="{{ URL::to('funcionalidades/crear') }}"><button type="button" style="margin-top: 316px;" id="submit" name="funcionalidad" class="btn btn-primary pull-right">Añadir Funcionalidad</button></a>
    </div>
</div>
</div>
@endsection

@section('javascript')
    	<script src="js/altaRoles.js"></script>
@endsection