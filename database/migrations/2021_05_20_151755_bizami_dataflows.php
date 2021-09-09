<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BizamiDataflows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DataflowsManager::createSchedule(
        'bizami_import_warehouse_states',
            [
                'name' => 'Bizami - Import Warehouse States',
                'status' => \App\Models\Dataflows\Schedule::STATUS_DISABLED,
                'profile_class' => 'App\Dataflows\Profile\Bizami\ImportWarehouseStates',
                'schedule' => '0 0 * * *',
            ]
        );

        DataflowsManager::createSchedule(
            'bizami_import_products',
            [
                'name' => 'Bizami - Import Products',
                'status' => \App\Models\Dataflows\Schedule::STATUS_DISABLED,
                'profile_class' => 'App\Dataflows\Profile\Bizami\ImportProducts',
                'schedule' => '0 0 * * *',
            ]
        );

        DataflowsManager::createSchedule(
            'bizami_import_sale_documents',
            [
                'name' => 'Bizami - Import Sale Documents',
                'status' => \App\Models\Dataflows\Schedule::STATUS_DISABLED,
                'profile_class' => 'App\Dataflows\Profile\Bizami\ImportSaleDocuments',
                'schedule' => '0 0 * * *',
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DataflowsManager::deleteSchedule('bizami_import_warehouse_states');
        DataflowsManager::deleteSchedule('bizami_import_products');
        DataflowsManager::deleteSchedule('bizami_import_sale_documents');
    }
}
