<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Auth\Register;
use App\Filament\Widgets\TransactionSumByTypeBarChart;
use App\Filament\Widgets\TransactionSumByTypePieChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
// use Filament\Register;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
	public function panel(Panel $panel): Panel
	{
		return $panel
			->default()
			->id('admin')
			->path('admin')
			// ->login()
			->login(Login::class)
			->font('Poppins')
			->registration(Register::class)
			// ->passwordReset()
			// ->emailVerification()
			->profile()
			->colors([
				'primary' => Color::Fuchsia,
				'danger' => Color::Rose,
				'gray' => Color::Gray,
				'info' => Color::Blue,
				'success' => Color::Emerald,
				'warning' => Color::Orange,
				'yellow' => Color::Yellow,
			])
			->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
			->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
			->pages([
				Pages\Dashboard::class,
			])
			->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
			->widgets([
				Widgets\AccountWidget::class,
				Widgets\FilamentInfoWidget::class,
                TransactionSumByTypePieChart::class,
                TransactionSumByTypeBarChart::class,
			])
			->middleware([
				EncryptCookies::class,
				AddQueuedCookiesToResponse::class,
				StartSession::class,
				AuthenticateSession::class,
				ShareErrorsFromSession::class,
				VerifyCsrfToken::class,
				SubstituteBindings::class,
				DisableBladeIconComponents::class,
				DispatchServingFilamentEvent::class,
			])
			->authMiddleware([
				Authenticate::class,
			]);
	}
}
