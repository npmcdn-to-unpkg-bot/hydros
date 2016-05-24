<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuncionalidadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
		Schema::create('funcionalidades', function(Blueprint $table){
			$table->increments('funcionalidad_id');
			$table->string('nombre',50);
			$table->string('descripcion',100)->nullable();
			$table->timestamps('fecha_alta');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}