<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some expense categories first
        $categories = ExpenseCategory::factory()
            ->count(10)
            ->active()
            ->create();

        // Get existing users, or create them if they don't exist
        $users = User::all();
        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }

        // Create 50 expenses
        Expense::factory()
            ->count(50)
            ->create([
                'expense_category_id' => $categories->random()->id,
                'created_by' => $users->random()->id,
            ]);

        $this->command->info('Created 50 expenses and 10 expense categories');
    }
}
