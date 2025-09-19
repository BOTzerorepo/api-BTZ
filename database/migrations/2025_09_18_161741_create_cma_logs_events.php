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
        Schema::create('cma_logs_events', function (Blueprint $table) {
            $table->id();
            $table->string('status_cma');                          // StusCMA
            $table->string('equipment_reference');                 // equipmentReference
            $table->timestamp('event_created_at');                 // eventCreatedDateTime (UTC)
            $table->string('originator_name');                     // originatorName
            $table->string('partner_name')->nullable();            // partnerName
            $table->string('event_type');                          // eventType
            $table->string('transport_event_type_code')->nullable();   // transportEventTypeCode
            $table->string('equipment_event_type_code')->nullable();   // equipmentEventTypeCode
            $table->string('event_classifier_code');               // eventClassifierCode
            $table->string('carrier_booking_reference')->nullable();   // carrierBookingReference
            $table->string('mode_of_transport')->nullable();       // modeOfTransport
            $table->string('facility_type_code')->nullable();      // facilityTypeCode
            $table->string('location_code')->nullable();           // LocationCode
            $table->decimal('latitude', 10, 6)->nullable();        // latitude
            $table->decimal('longitude', 11, 6)->nullable();       // longitude
            $table->timestamps();

            $table->index(['equipment_reference', 'event_created_at']);
            $table->unique(
                ['equipment_reference', 'event_created_at', 'transport_event_type_code'],
                'uniq_cma_event_triplet'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cma_logs_events');
    }
};
