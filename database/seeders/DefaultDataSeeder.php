<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Status;
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'To Do',        'color' => '#6B7280'],
            ['name' => 'In Progress',  'color' => '#F59E0B'],
            ['name' => 'Checking',     'color' => '#8B5CF6'],
            ['name' => 'Done',         'color' => '#10B981'],
        ];

        foreach ($statuses as $index => $status) {
            Status::firstOrCreate(
                ['name' => $status['name']],
                ['color' => $status['color'], 'order' => $index]
            );
        }

        $categories = [
            ['name' => 'Hardware',      'color' => '#EF4444'],
            ['name' => 'Software',      'color' => '#3B82F6'],
            ['name' => 'Network',       'color' => '#0EA5E9'],
            ['name' => 'Account Access','color' => '#A855F7'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
