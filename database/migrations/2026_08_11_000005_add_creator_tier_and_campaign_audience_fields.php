<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creators', function (Blueprint $table) {
            if (! Schema::hasColumn('creators', 'state')) {
                $table->string('state', 120)->nullable()->after('city');
            }
            if (! Schema::hasColumn('creators', 'tier')) {
                // nano | micro | mid | macro | mega — computed at import time
                $table->string('tier', 20)->nullable()->after('follower_count_total')->index();
            }
            if (! Schema::hasColumn('creators', 'gender')) {
                // female | male | non_binary | other
                $table->string('gender', 20)->nullable()->after('display_name')->index();
            }
            if (! Schema::hasColumn('creators', 'age_range')) {
                // 13-17 | 18-24 | 25-34 | 35-44 | 45-54 | 55+
                $table->string('age_range', 10)->nullable()->after('gender');
            }
            if (! Schema::hasColumn('creators', 'audience_female_pct')) {
                $table->unsignedTinyInteger('audience_female_pct')->nullable()->after('avg_views');
            }
            if (! Schema::hasColumn('creators', 'audience_male_pct')) {
                $table->unsignedTinyInteger('audience_male_pct')->nullable()->after('audience_female_pct');
            }
            if (! Schema::hasColumn('creators', 'audience_top_age')) {
                $table->string('audience_top_age', 10)->nullable()->after('audience_male_pct');
            }
        });

        Schema::table('campaigns', function (Blueprint $table) {
            if (! Schema::hasColumn('campaigns', 'audience_criteria')) {
                // {cities:[], tiers:[], genders:[], age_ranges:[], languages:[],
                //  min_followers, max_followers, min_engagement,
                //  audience_gender:null|female|male, audience_min_pct:0-100}
                $table->json('audience_criteria')->nullable()->after('exclusivity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('creators', function (Blueprint $table) {
            foreach (['state','tier','gender','age_range','audience_female_pct','audience_male_pct','audience_top_age'] as $c) {
                if (Schema::hasColumn('creators', $c)) $table->dropColumn($c);
            }
        });
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'audience_criteria')) $table->dropColumn('audience_criteria');
        });
    }
};
