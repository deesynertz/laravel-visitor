<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyVisitingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('property-visitor.table_names');
        $columnNames = config('property-visitor.column_names');

        Schema::create($tableNames['property_visitings'], function (Blueprint $table) {
            $table->id();
            $table->string('property_code', 12)->unique(); # receice uniq value as string of 12 character
            $table->morphs('propertyable');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create($tableNames['property_has_visitors'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnUpdate();
            $table->foreignId($columnNames['property_visiting_key'])
                ->constrained($tableNames['property_visitings'], 'id')
                ->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create($tableNames['property_custodians'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnUpdate();
            $table->foreignId($columnNames['property_visiting_key'])
                ->constrained($tableNames['property_visitings'], 'id')
                ->cascadeOnUpdate();
            $table->dateTime('shift_start');
            $table->dateTime('shift_end');
            $table->boolean('status');
            $table->timestamps();
        });

        Schema::create($tableNames['visitor_common_reasons'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained($tableNames['visitor_common_reasons'], 'id')
                ->cascadeOnUpdate();
            $table->string('content');
            $table->timestamps();
        });

        Schema::create($tableNames['visitor_line_items'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->id();
            $table->foreignId($columnNames['property_visitor_key'])
                ->constrained($tableNames['property_has_visitors'], 'id')
                ->cascadeOnUpdate();
            $table->foreignId($columnNames['visitor_reason_key'])
                ->constrained($tableNames['visitor_common_reasons'], 'id')
                ->cascadeOnUpdate();
            $table->foreignId($columnNames['property_custodian_key'])
                ->nullable()
                ->constrained($tableNames['property_custodians'], 'id')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->nullableMorphs('visitorable');
            $table->dateTime('starting');
            $table->dateTime('ending')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('auto_signout')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_line_items');
        Schema::dropIfExists('visitor_common_reasons');
        Schema::dropIfExists('property_has_visitors');
        Schema::dropIfExists('property_visitings');
    }
}
