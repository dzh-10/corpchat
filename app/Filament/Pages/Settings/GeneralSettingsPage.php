<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GeneralSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'إعدادات عامة';

    protected static ?int $navigationSort = 1;

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('هوية التطبيق')
                ->icon('heroicon-o-building-office')
                ->schema([
                    Forms\Components\TextInput::make('app_name')
                        ->label('اسم التطبيق')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('app_tagline')
                        ->label('الشعار/العبارة التعريفية')
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('app_logo')
                        ->label('شعار التطبيق')
                        ->image()
                        ->directory('settings/logos'),
                    Forms\Components\FileUpload::make('app_favicon')
                        ->label('أيقونة المتصفح (Favicon)')
                        ->image()
                        ->acceptedFileTypes(['image/x-icon', 'image/png'])
                        ->directory('settings/favicons'),
                ]),

            Section::make('الإقليمية والتوطين')
                ->icon('heroicon-o-globe-alt')
                ->schema([
                    Forms\Components\Select::make('app_language')
                        ->label('اللغة الافتراضية')
                        ->options(['ar' => 'العربية', 'fr' => 'Français', 'en' => 'English'])
                        ->required(),
                    Forms\Components\Select::make('app_timezone')
                        ->label('المنطقة الزمنية')
                        ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                        ->searchable()
                        ->required(),
                ]),

            Section::make('وضع الصيانة')
                ->icon('heroicon-o-wrench-screwdriver')
                ->schema([
                    Forms\Components\Toggle::make('maintenance_mode')
                        ->label('تفعيل وضع الصيانة')
                        ->helperText('سيمنع وصول جميع المستخدمين عدا المدير')
                        ->reactive(),
                    Forms\Components\Textarea::make('maintenance_message')
                        ->label('رسالة الصيانة')
                        ->rows(3)
                        ->visible(fn ($get) => $get('maintenance_mode')),
                ]),
        ]);
    }
}
