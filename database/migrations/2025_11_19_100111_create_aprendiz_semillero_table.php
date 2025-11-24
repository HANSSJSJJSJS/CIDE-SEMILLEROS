use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aprendiz_semillero', function (Blueprint $table) {
            $table->id();

            // Claves foráneas
            $table->unsignedBigInteger('id_aprendiz');
            $table->unsignedBigInteger('id_semillero');

            // Para no repetir el mismo aprendiz en el mismo semillero
            $table->unique(['id_aprendiz', 'id_semillero']);

            // Si quieres timestamps:
            $table->timestamps();

            // FKs según tus tablas actuales
            $table->foreign('id_aprendiz')
                ->references('id_aprendiz')->on('aprendices')
                ->onDelete('cascade');

            $table->foreign('id_semillero')
                ->references('id_semillero')->on('semilleros')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aprendiz_semillero');
    }
};
