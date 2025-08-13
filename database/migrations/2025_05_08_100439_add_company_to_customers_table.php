<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modificar los timestamps si es necesario
        DB::statement("ALTER TABLE customers MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL;");
        
        Schema::table('customers', function (Blueprint $table) {
            
            $table->string('company')->nullable()->after('contact_mail');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {

            $table->dropColumn('company'); 
            $table->dropSoftDeletes();
           
            
        });
    }
};
