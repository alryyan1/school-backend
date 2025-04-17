<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_transport_routes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\School;
use App\Models\User; // Assuming driver is a User

return new class extends Migration {
    public function up(): void {
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(School::class)->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Route A - North"
            $table->text('description')->nullable();
            $table->foreignIdFor(User::class, 'driver_id')->nullable()->constrained('users')->nullOnDelete(); // Driver (optional)
            // $table->foreignIdFor(Vehicle::class)->nullable()->constrained()->nullOnDelete(); // Future: Link to specific bus/vehicle
            $table->decimal('fee_amount', 10, 2)->nullable(); // Optional fee for this route
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Optional: Unique route name per school
            // $table->unique(['school_id', 'name']);
        });
    }
    public function down(): void { Schema::dropIfExists('transport_routes'); }
};