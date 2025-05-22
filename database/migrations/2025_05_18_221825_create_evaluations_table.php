<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('ID del estudiante evaluado');
            $table->foreignId('teacher_id')->comment('ID del profesor que realiza la evaluación');
            $table->foreignId('module_id')->comment('ID del módulo evaluado');
            $table->foreignId('unit_id')->comment('ID de la unidad evaluada');
            $table->decimal('score', 3, 1)->comment('Puntuación de 0 a 10');
            $table->text('comments')->nullable()->comment('Comentarios sobre la evaluación (opcional)');
            $table->date('evaluation_date')->comment('Fecha de la evaluación');
            $table->timestamps();

            $table->foreign('student_id', 'fk_evaluations_students')
                ->references('id')->on('students')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('teacher_id', 'fk_evaluations_teachers')
                ->references('id')->on('teachers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('module_id', 'fk_evaluations_modules')
                ->references('id')->on('modules')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('unit_id', 'fk_evaluations_units')
                ->references('id')->on('units')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
