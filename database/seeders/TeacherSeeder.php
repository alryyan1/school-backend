<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
class TeacherSeeder extends Seeder{
     public function run():void {
        Teacher::factory()->count(10)->create();
     }
}