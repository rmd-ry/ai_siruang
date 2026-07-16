<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Reservasi;
use App\StatusReservasi;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        View::composer('*', function ($view) {
            if (!auth()->check()) {
                return;
            }

            $notifTerbaru = Reservasi::with('kelas')
                ->where('id_user', auth()->id())
                ->whereIn('status', [
                    StatusReservasi::DITERIMA,
                    StatusReservasi::DITOLAK
                ])
                ->latest('updated_at')
                ->take(5)
                ->get();

            $notifUnreadCount = Reservasi::where('id_user', auth()->id())
                ->whereNull('read_at')
                ->where('status', '!=', StatusReservasi::PENDING)
                ->count();

            $view->with([
                'notifPopup' => $notifTerbaru,
                'notifUnreadCount' => $notifUnreadCount,
            ]);
        });
    }
}
