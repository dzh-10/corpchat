<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ReverbSettings;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;

class ReverbSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-signal';
    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات Reverb (WebSocket)';
    protected static ?int    $navigationSort  = 7;
    protected static string  $settings = ReverbSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('تكوين خادم Reverb')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('reverb_host')->label('اسم المضيف')->default('reverb-server'),
                    Forms\Components\TextInput::make('reverb_port')->label('المنفذ')->numeric()->default(8080),
                    Forms\Components\Select::make('reverb_scheme')->label('البروتوكول')->options(['http' => 'HTTP', 'https' => 'HTTPS']),
                    Forms\Components\TextInput::make('reverb_app_key')->label('مفتاح التطبيق'),
                    Forms\Components\TextInput::make('reverb_app_secret')->label('المفتاح السري')->password()->revealable(),
                    Forms\Components\TextInput::make('reverb_max_connections')->label('الحد الأقصى للاتصالات')->numeric()->default(1000),
                    Forms\Components\Toggle::make('reverb_debug_mode')->label('وضع التصحيح Debug'),
                ]),

            Actions::make([
                Action::make('testReverb')
                    ->label('اختبار اتصال WebSocket')
                    ->icon('heroicon-o-wifi')
                    ->color('success')
                    ->action(function (\Filament\Schemas\Components\Utilities\Get $get) {
                        // اختبار ping للـ Reverb server
                        $host = $get('reverb_host');
                        $port = $get('reverb_port');
                        $connected = @fsockopen($host, $port, $errno, $errstr, 3);
                        if ($connected) {
                            fclose($connected);
                            Notification::make()->title('✅ Reverb يعمل بنجاح على ' . $host . ':' . $port)->success()->send();
                        } else {
                            Notification::make()->title('❌ تعذر الاتصال: ' . $errstr)->danger()->send();
                        }
                    }),
            ]),
        ]);
    }
}
