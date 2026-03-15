<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $statuses = [
            ['name' => 'Pending'],     
            ['name' => 'Processing'],
            ['name'=>'Packing'],
            ['name' => 'Shipped'],     
            ['name' => 'Delivered'],   
            ['name' => 'Cancelled'],   
        ];

        foreach ($statuses as $status) {
            OrderStatus::create($status);
        }
    }
}
