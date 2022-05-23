<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreMasterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_designation', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);

            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_designation_code');
            $table->unique('name_lc','uq_mst_designation_name_lc');
            $table->unique('name_en','uq_mst_designation_name_en');
        });

        Schema::create('mst_fed_province', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);

            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_fed_province_code');
            $table->unique('name_lc','uq_mst_fed_province_name_lc');
            $table->unique('name_en','uq_mst_fed_province_name_en');

        });

        Schema::create('mst_fed_district', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->unsignedSmallInteger('province_id');
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_fed_district_code');
            $table->unique('name_lc','uq_mst_fed_district_name_lc');
            $table->unique('name_en','uq_mst_fed_district_name_en');
            $table->index('province_id','idx_mst_fed_district_province_id');

            $table->foreign('province_id','fk_mst_fed_district_province_id')->references('id')->on('mst_fed_province');

        });

        Schema::create('mst_fed_local_level_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_fed_local_level_type_code');
        });

        Schema::create('mst_fed_local_level', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',20);
            $table->unsignedSmallInteger('district_id');
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->unsignedSmallInteger('level_type_id');
            $table->unsignedSmallInteger('wards_count')->nullable()->default(0);
            $table->boolean('is_tmpp_applicable')->nullable()->default(false);
            $table->string('gps_lat',20)->nullable();
            $table->string('gps_long',20)->nullable();
            $table->string('remarks',500)->nullable();
            $table->string('lmbiscode',100)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_fed_local_level_code');

            $table->foreign('district_id','fk_mst_fed_local_level_district_id')->references('id')->on('mst_fed_district');
            $table->foreign('level_type_id','fk_mst_fed_local_level_level_type_id')->references('id')->on('mst_fed_local_level_type');

        });

        Schema::create('mst_fiscal_year', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('from_date_bs',10)->nullable();
            $table->date('from_date_ad')->nullable();
            $table->string('to_date_bs',10)->nullable();
            $table->date('to_date_ad')->nullable();
            $table->string('remarks',500)->nullable();
            $table->boolean('is_current')->default(false);
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_fiscal_year_code');
            $table->unique('from_date_bs','uq_mst_fiscal_year_from_date_bs');
            $table->unique('from_date_ad','uq_mst_fiscal_year_from_date_ad');

        });

        Schema::create('mst_funding_source', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->float('min')->nullable();
            $table->float('max')->nullable();
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_mst_funding_source_code');
            $table->unique('name_lc','uq_mst_mst_funding_source_name_lc');
            $table->unique('name_en','uq_mst_mst_funding_source_name_en');

        });

        Schema::create('mst_nepali_month', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->unsignedSmallInteger('is_quarterly')->nullable();
            $table->unsignedSmallInteger('is_yearly')->nullable();
            $table->unsignedSmallInteger('is_halfyearly')->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_nepali_month_code');
            $table->unique('name_lc','uq_mst_nepali_month_name_lc');
            $table->unique('name_en','uq_mst_nepali_month_name_en');

        });

        Schema::create('mst_note_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_note_type_code');
            $table->unique('name_lc','uq_mst_note_type_name_lc');
            $table->unique('name_en','uq_mst_note_type_name_en');

        });

        Schema::create('mst_project_category', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_project_category_code');
            $table->unique('name_en','uq_mst_project_category_name_en');
            $table->unique('name_lc','uq_mst_project_category_name_lc');

        });

        Schema::create('mst_project_status', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);
            $table->boolean('is_active')->default('false');


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_project_status_code');
            $table->unique('name_lc','uq_mst_project_status_name_lc');
            $table->unique('name_en','uq_mst_project_status_name_en');
        });

        Schema::create('mst_project_sub_category', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->unsignedSmallInteger('project_category_id');
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);

            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_project_sub_category_code');
            $table->unique('name_en','uq_mst_project_sub_category_name_en');
            $table->unique('name_lc','uq_mst_project_sub_category_name_lc');

            $table->foreign('project_category_id','fk_mst_project_sub_category_project_category_id')->references('id')->on('mst_project_category');

        });

        Schema::create('mst_reporting_interval', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_reporting_interval_code');
            $table->unique('name_lc','uq_mst_reporting_interval_name_lc');
            $table->unique('name_en','uq_mst_reporting_interval_name_en');
        });


        Schema::create('mst_road_connectivity_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_road_connectivity_type_code');
            $table->unique('name_lc','uq_mst_road_connectivity_type_name_lc');
            $table->unique('name_en','uq_mst_road_connectivity_type_name_en');


        });
      
        Schema::create('app_client', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->unsignedSmallInteger('fed_local_level_id')->nullable();
            $table->string('admin_email',200)->nullable();
            $table->string('remarks',1000)->nullable();
            $table->boolean('is_tmpp_applicable')->nullable()->default(false);
            $table->string('lmbiscode',100)->nullable();
            $table->boolean('is_active')->nullable();


            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_app_client_code');

            $table->foreign('fed_local_level_id','fk_app_client_fed_local_level_id')->references('id')->on('mst_fed_local_level');


        });
        Schema::create('app_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('client_id');
            $table->unsignedSmallInteger('fiscal_year_id')->nullable();
            $table->boolean('allow_new_project_demand')->default(false);

            $table->string('incharge_name',200)->nullable();
            $table->string('incharge_designation',200)->nullable();
            $table->string('incharge_phone',50)->nullable();
            $table->string('incharge_mobile',50)->nullable();
            $table->string('incharge_email',50)->nullable();

            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('client_id','fk_app_setting_client_id')->references('id')->on('app_client');
            $table->foreign('fiscal_year_id','fk_app_setting_fiscal_year_id')->references('id')->on('mst_fiscal_year');
        });


        Schema::create('mst_executing_entity_type', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->unique('code','uq_mst_executing_entity_type_code');
            $table->unique('name_lc','uq_mst_executing_entity_type_name_lc');
            $table->unique('name_en','uq_mst_executing_entity_type_name_en');
        });

        Schema::create('mst_executing_entity', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('client_id');
            $table->unsignedSmallInteger('entity_type_id');
            $table->string('code',20);
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->unsignedSmallInteger('add_province_id')->nullable();
            $table->unsignedSmallInteger('add_district_id')->nullable();
            $table->unsignedSmallInteger('add_local_level_id')->nullable();
            $table->unsignedSmallInteger('add_ward_no')->nullable();
            $table->string('add_tole_name',500)->nullable();
            $table->string('add_house_number',50)->nullable();
            $table->string('contact_person',100)->nullable();
            $table->string('contact_person_designation',100)->nullable();
            $table->string('contact_person_phone',50)->nullable();
            $table->string('contact_person_mobile',50)->nullable();
            $table->string('contact_person_email',50)->nullable();
            $table->string('uc_registration_number',50)->nullable();
            $table->string('company_registration_number',50)->nullable();
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('client_id','fk_mst_executing_entity_client_id')->references('id')->on('app_client');
            $table->foreign('entity_type_id','fk_mst_executing_entity_entity_type_id')->references('id')->on('mst_executing_entity_type');
            $table->foreign('add_province_id','fk_mst_executing_entity_add_province_id')->references('id')->on('mst_fed_province');
            $table->foreign('add_district_id','fk_mst_executing_entity_add_district_id')->references('id')->on('mst_fed_district');
            $table->foreign('add_local_level_id','fk_mst_executing_entity_add_local_level_id')->references('id')->on('mst_fed_local_level');

        });

        Schema::create('mst_tmpp_related_staff', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('client_id')->nullable();
            $table->unsignedSmallInteger('entity_type_id');
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->unsignedSmallInteger('designation_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('remarks',500)->nullable();
            $table->smallInteger('display_order')->default(0);


            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('client_id','fk_mst_tmpp_related_staff_client_id')->references('id')->on('app_client');
            $table->foreign('entity_type_id','fk_mst_tmpp_related_staff_entity_type_id')->references('id')->on('mst_executing_entity_type');
            $table->foreign('designation_id','fk_mst_tmpp_related_staff_designation_id')->references('id')->on('mst_designation');

        });

        Schema::create('mst_unit', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->unsignedSmallInteger('category_id');
            $table->string('name_en',200);
            $table->string('name_lc',200);
            $table->smallInteger('display_order')->default(0);

            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('category_id','fk_mst_unit_category_id')->references('id')->on('mst_project_category');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_tmpp_related_staff');
        Schema::dropIfExists('mst_unit');
        Schema::dropIfExists('mst_executing_entity');
        Schema::dropIfExists('mst_executing_entity_type');
        Schema::dropIfExists('app_setting');
        Schema::dropIfExists('app_client');
        Schema::dropIfExists('mst_road_connectivity_type');
        Schema::dropIfExists('mst_reporting_interval');
        Schema::dropIfExists('mst_project_sub_category');
        Schema::dropIfExists('mst_project_status');
        Schema::dropIfExists('mst_project_category');
        Schema::dropIfExists('mst_note_type');
        Schema::dropIfExists('mst_nepali_month');
        Schema::dropIfExists('mst_funding_source');
        Schema::dropIfExists('mst_fiscal_year');
        Schema::dropIfExists('mst_fed_local_level');
        Schema::dropIfExists('mst_fed_local_level_type');
        Schema::dropIfExists('mst_fed_district');
        Schema::dropIfExists('mst_fed_province');
        Schema::dropIfExists('mst_designation');
    }
}
