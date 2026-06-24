<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AppearanceSettings;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;

class AppearanceSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-paint-brush';
    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'المظهر والتصميم';
    protected static ?int    $navigationSort  = 6;
    protected static string  $settings = AppearanceSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الألوان والثيم')
                ->columns(2)
                ->schema([
                    Forms\Components\ColorPicker::make('primary_color')->label('اللون الرئيسي'),
                    Forms\Components\Select::make('theme_mode')
                        ->label('وضع الثيم')
                        ->options(['light' => '☀️ فاتح', 'dark' => '🌙 داكن', 'system' => '🖥️ تلقائي']),
                    Forms\Components\Toggle::make('enable_glassmorphism')->label('تأثير Glassmorphism'),
                    Forms\Components\Toggle::make('enable_animations')->label('تفعيل التأثيرات المتحركة'),
                ]),

            Section::make('الخط والتخطيط')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('font_family')
                        ->label('نوع الخط')
                        ->options(['inter' => 'Inter', 'cairo' => 'Cairo', 'tajawal' => 'Tajawal']),
                    Forms\Components\Select::make('sidebar_style')
                        ->label('نمط الشريط الجانبي')
                        ->options(['full' => 'كامل', 'compact' => 'مضغوط', 'icon-only' => 'أيقونات فقط']),
                    Forms\Components\Select::make('chat_bubble_style')
                        ->label('شكل فقاعات الدردشة')
                        ->options(['modern' => 'عصري', 'classic' => 'كلاسيكي', 'minimal' => 'بسيط']),
                ]),

            Section::make('التاريخ والوقت')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('date_format')
                        ->label('صيغة التاريخ')
                        ->options(['d/m/Y' => 'DD/MM/YYYY', 'Y-m-d' => 'YYYY-MM-DD', 'm/d/Y' => 'MM/DD/YYYY']),
                    Forms\Components\Select::make('time_format')
                        ->label('صيغة الوقت')
                        ->options(['12h' => '12 ساعة', '24h' => '24 ساعة']),
                ]),
        ]);
    }
}
