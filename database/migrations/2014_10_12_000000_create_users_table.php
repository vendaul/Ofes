<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Users table is imported from SQL file - no migration needed
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('empid', 15)->default('N/A');
            $table->string('fname', 50)->default('N/A');
            $table->string('mname', 50)->default('N/A');
            $table->string('lname', 50)->default('N/A');
            $table->string('extension', 5)->default('N/A');
            $table->string('fullname', 250)->default('N/A');
            $table->string('maiden_name', 150)->default('N/A');
            $table->date('birthday')->nullable();
            $table->string('birthplace', 200)->default('N/A');
            $table->string('citizenship', 30)->default('N/A');
            $table->string('citizenship_type', 30)->default('N/A');
            $table->string('citizenship_country', 150)->default('N/A');
            $table->string('gender', 10)->nullable();
            $table->string('tribe', 100)->nullable();
            $table->string('marital_status', 20)->default('N/A');
            $table->string('other_status', 20)->default('N/A');
            $table->string('religion', 100)->nullable();
            $table->string('disability', 100)->nullable();
            $table->string('specialization', 100)->nullable();
            $table->string('height', 10)->default('N/A');
            $table->string('weight', 10)->default('N/A');
            $table->string('bloodtype', 5)->default('N/A');
            $table->string('gsis_id', 20)->default('N/A');
            $table->string('umid_id', 20)->default('N/A');
            $table->string('pagibig_id', 20)->default('N/A');
            $table->string('philhealth_id', 20)->default('N/A');
            $table->string('sss_id', 20)->default('N/A');
            $table->string('philsys_id', 20)->default('N/A');
            $table->string('tin_no', 20)->default('N/A');
            $table->string('agency_emp_no', 20)->default('N/A');
            $table->string('add1_lot_no', 20)->default('N/A');
            $table->string('add1_street', 50)->default('N/A');
            $table->string('add1_sub', 100)->default('N/A');
            $table->string('add1_brgy', 100)->default('N/A');
            $table->string('add1_city', 100)->default('N/A');
            $table->string('add1_prov', 100)->default('N/A');
            $table->string('add1_zip', 10)->default('N/A');
            $table->string('add2_lot_no', 20)->default('N/A');
            $table->string('add2_street', 50)->default('N/A');
            $table->string('add2_sub', 100)->default('N/A');
            $table->string('add2_brgy', 100)->default('N/A');
            $table->string('add2_city', 100)->default('N/A');
            $table->string('add2_prov', 100)->default('N/A');
            $table->string('add2_zip', 10)->default('N/A');
            $table->string('tel_no', 50)->default('N/A');
            $table->string('contact', 50)->default('N/A');
            $table->string('slug', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('password', 5000)->nullable();
            $table->string('areacode', 20)->nullable();
            $table->string('academic_rank', 50)->default('N/A');
            $table->string('hep', 50)->default('N/A');
            $table->string('hep_course', 255)->nullable();
            $table->string('hep_units', 255)->nullable();
            $table->string('user_access', 2)->nullable();
            $table->string('user_data_man_role', 1)->default('4');
            $table->string('sias_role', 2)->nullable();
            $table->string('user_role', 2)->nullable();
            $table->string('user_hr_role', 2)->nullable();
            $table->string('user_dept_role', 2)->nullable();
            $table->string('user_data_role', 2)->nullable();
            $table->string('department', 150)->nullable();
            $table->integer('college')->nullable();
            $table->string('position', 150)->nullable();
            $table->string('parentethical_id', 10)->nullable();
            $table->string('emp_cat', 30)->nullable();
            $table->string('r_status', 1)->nullable();
            $table->string('deactivation_reason', 20)->nullable();
            $table->string('is_approved', 1)->nullable();
            $table->string('validator_comment', 50)->nullable();
            $table->integer('account_id')->nullable();
            $table->integer('is_admin')->nullable();
            $table->string('role', 50)->nullable();
            $table->integer('user_journal_role')->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('verification_code', 6)->nullable();
            $table->timestamp('verification_created_at')->nullable();
            $table->string('is_requested_vrc', 1)->default('N');
            $table->timestamp('password_reset_at')->nullable();
            $table->integer('created_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
            $table->integer('activated_user_id')->nullable();
            $table->string('is_project_based', 1)->default('N');
            $table->fullText('fullname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};