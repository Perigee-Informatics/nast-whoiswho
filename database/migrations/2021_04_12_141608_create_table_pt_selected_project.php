<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePtSelectedProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pt_selected_project', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('client_id');
            $table->unsignedSmallInteger('project_id')->nullable();
            $table->string('code',20)->nullable();
            $table->string('name_en',200)->nullable();
            $table->string('name_lc',200);
            $table->unsignedSmallInteger('category_id')->nullable();
            $table->unsignedInteger('sub_category_id')->nullable();
            $table->float('project_cost')->nullable()->default(0);
            $table->float('source_federal_percent')->nullable()->default(0);
            $table->float('source_federal_amount')->nullable()->default(0);
            $table->float('source_local_level_percent')->nullable()->default(0);
            $table->float('source_local_level_amount')->nullable()->default(0);
            $table->float('source_donar_percent')->nullable()->default(0);
            $table->float('source_donar_amount')->nullable()->default(0);
            $table->float('contingency_percent')->nullable()->default(0);
            $table->float('contingency_amount')->nullable()->default(0);
            $table->unsignedInteger('project_affected_population')->nullable()->default(0);
            $table->unsignedSmallInteger('project_affected_ward_count')->nullable()->default(0);
            $table->string('project_affected_wards')->nullable();
            $table->string('gps_lat',20)->nullable();
            $table->string('gps_long',20)->nullable();
            $table->boolean('has_dpr')->default(false);
            $table->boolean('is_selected')->nullable()->default(false);
            $table->string('selected_date_bs',10)->nullable();
            $table->date('selected_date_ad')->nullable();
            $table->string('proposed_start_date_bs',10)->nullable();
            $table->date('proposed_start_date_ad')->nullable();
            $table->unsignedSmallInteger('proposed_duration_year')->nullable();
            $table->unsignedSmallInteger('proposed_duration_months')->nullable();
            $table->string('proposed_end_date_bs',10)->nullable();
            $table->date('proposed_end_date_ad')->nullable();
            $table->unsignedSmallInteger('project_status_id')->nullable();
            $table->unsignedSmallInteger('fiscal_year_id')->nullable();
            $table->boolean('is_multi_fiscalyear_project')->nullable()->default(false);
            $table->string('actual_start_date_bs',10)->nullable();
            $table->date('actual_start_date_ad')->nullable();
            $table->string('actual_end_date_bs',10)->nullable();
            $table->date('actual_end_date_ad')->nullable();
            $table->unsignedSmallInteger('actual_duration_year')->nullable();
            $table->unsignedSmallInteger('actual_duration_months')->nullable();
            $table->unsignedSmallInteger('actual_duration_days')->nullable();
         
            $table->unsignedSmallInteger('executing_entity_type_id')->nullable();
            $table->unsignedInteger('executing_entity_id')->nullable();
            $table->string('description_en',2000)->nullable();
            $table->string('description_lc',2000)->nullable();
            $table->string('remarks',500)->nullable();
            $table->string('quantity')->nullable()->default(0);
            $table->unsignedInteger('weightage')->nullable()->default(0);
            $table->unsignedInteger('unit_type')->nullable()->default(0);
            $table->string('lmbiscode',20)->nullable();
            $table->text('file_upload')->nullable();
            
            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->unsignedInteger('deleted_uq_code')->nullable()->default(1);
            
            $table->foreign('client_id','fk_pt_selected_project_client_id')->references('id')->on('app_client');
            $table->foreign('project_id','fk_pt_selected_project_project_id')->references('id')->on('pt_project');
            $table->foreign('category_id','fk_pt_selected_project_category_id')->references('id')->on('mst_project_category');
            $table->foreign('sub_category_id','fk_pt_selected_project_sub_category_id')->references('id')->on('mst_project_sub_category');
            $table->foreign('project_status_id','fk_pt_selected_project_project_status_id')->references('id')->on('mst_project_status');
            $table->foreign('fiscal_year_id','fk_pt_selected_project_fiscal_year_id')->references('id')->on('mst_fiscal_year');
            $table->foreign('executing_entity_type_id','fk_pt_selected_project_executing_entity_type_id')->references('id')->on('mst_executing_entity_type');
            $table->foreign('executing_entity_id','fk_pt_selected_project_executing_entity_id')->references('id')->on('mst_executing_entity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pt_selected_project');
    }
}
