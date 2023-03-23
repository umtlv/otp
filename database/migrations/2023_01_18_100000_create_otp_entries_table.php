<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The database schema.
     *
     * @var Builder
     */
    protected $schema;

    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
    }

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return config('otp.database.connection');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->schema->create('otp', function (Blueprint $table) {
            $table->id();

            $table->string('key');
            $table->string('notification_method');
            $table->string('notification_to');
            $table->string('ip_address');
            $table->text('verify_token');
            $table->string('verify_code');
            $table->timestamp('expires_at');
            $table->json('data')->nullable();
            $table->integer('attempts')->default(0);
            $table->boolean('verified')->default(false);

            $table->timestamps();
        });

        $this->schema->create('otp_blacklist', function (Blueprint $table) {
            $table->id();

            $table->string('notification_to');
            $table->string('ip_address')->nullable();
            $table->timestamp('expires_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $this->schema->dropIfExists('otp');
        $this->schema->dropIfExists('otp_blacklist');
    }
};