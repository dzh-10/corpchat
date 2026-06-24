<?php

namespace App\Filament\Pages\Settings;

use App\Settings\NotificationSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NotificationSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'إعدادات الإشعارات';

    protected static ?int $navigationSort = 4;

    protected static string $settings = NotificationSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('قنوات الإشعار')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('enable_browser_notifications')->label('إشعارات المتصفح'),
                    Forms\Components\Toggle::make('enable_email_notifications')->label('إشعارات البريد الإلكتروني'),
                    Forms\Components\Toggle::make('enable_sound_notifications')->label('إشعارات صوتية'),
                    Forms\Components\Select::make('notification_sound')
                        ->label('الصوت المستخدم')
                        ->options(['default' => 'افتراضي', 'ping' => 'Ping', 'chime' => 'Chime'])
                        ->visible(fn ($get) => $get('enable_sound_notifications')),
                ]),

            Section::make('أحداث الإشعار')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('notify_on_new_message')->label('رسالة جديدة في الدردشة'),
                    Forms\Components\Toggle::make('notify_on_new_email')->label('بريد إلكتروني جديد من عميل'),
                    Forms\Components\Toggle::make('notify_on_mention')->label('عند الإشارة @mention'),
                    Forms\Components\Toggle::make('notify_on_group_message')->label('رسالة في مجموعة'),
                ]),

            Section::make('تجميع الإشعارات')
                ->schema([
                    Forms\Components\TextInput::make('notification_digest_hours')
                        ->label('تجميع الإشعارات كل (ساعات) — 0 = إرسال فوري')
                        ->numeric()
                        ->default(0),
                ]),
        ]);
    }
}
