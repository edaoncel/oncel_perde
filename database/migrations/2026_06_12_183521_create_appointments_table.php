<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->text('kisisel_bilgiler')->nullable(); 
            $table->string('gender');                     
            $table->string('manken_kilo')->nullable();     
            $table->string('height');                     
            $table->integer('weight')->nullable();         
            $table->integer('chest')->nullable();          
            $table->integer('waist')->nullable();          
            $table->integer('hip')->nullable();            
            $table->text('ekstra_olculer')->nullable();    
            $table->string('clothing_type');               
            $table->string('neck_type')->nullable();       
            $table->string('fabric_type')->nullable();     
            $table->string('fabric_color')->nullable();    
            $table->text('message')->nullable(); 
            $table->longText('cizim_katmani')->nullable(); 
            $table->json('referans_resimler')->nullable(); 
            $table->dateTime('appointment_date')->nullable();
            $table->string('status')->default('pending');  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};