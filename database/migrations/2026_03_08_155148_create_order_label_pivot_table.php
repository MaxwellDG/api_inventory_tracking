<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_label', function (Blueprint $table) {
            $table->string('order_id');
            $table->foreignId('label_id')->constrained('labels')->onDelete('cascade');
            $table->primary(['order_id', 'label_id']);
            $table->foreign('order_id')->references('uuid')->on('orders')->onDelete('cascade');
        });

        // Backfill: match existing text labels to label records and create pivot entries
        $orders = DB::table('orders')
            ->whereNotNull('label')
            ->where('label', '!=', 'None')
            ->where('label', '!=', '')
            ->select('uuid', 'company_id', 'label')
            ->get();

        foreach ($orders as $order) {
            $label = DB::table('labels')
                ->where('company_id', $order->company_id)
                ->where('name', $order->label)
                ->first();

            if ($label) {
                DB::table('order_label')->insertOrIgnore([
                    'order_id' => $order->uuid,
                    'label_id' => $label->id,
                ]);
            }
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_label');

        Schema::table('orders', function (Blueprint $table) {
            $table->string('label')->default('None')->after('status');
        });
    }
};
