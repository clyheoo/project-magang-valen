<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $today = Carbon::today();

        $suratMasukHariIni = 0;
        $suratKeluarHariIni = 0;
        $belumDitindak = 0;

        try {
            $suratMasukHariIni = SuratMasuk::whereDate('created_at', $today)->count();
            $belumDitindak = SuratMasuk::where('status', 'baru')->count();
        } catch (\Exception $e) {
            // Tables may not exist yet during migrations
        }

        try {
            $suratKeluarHariIni = SuratKeluar::whereDate('created_at', $today)->count();
        } catch (\Exception $e) {
            // Tables may not exist yet during migrations
        }

        View::share('suratMasukHariIni', $suratMasukHariIni);
        View::share('suratKeluarHariIni', $suratKeluarHariIni);
        View::share('belumDitindak', $belumDitindak);
    }
}
