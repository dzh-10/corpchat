<?php

namespace App\Filament\Pages\Settings;

use App\Settings\SecuritySettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SecuritySettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'إعدادات الأمان';

    protected static ?int $navigationSort = 5;

    protected static string $settings = SecuritySettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الجلسة والدخول')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('session_timeout_minutes')->label('مهلة انتهاء الجلسة (دقائق)')->numeric(),
                    Forms\Components\TextInput::make('max_login_attempts')->label('الحد الأقصى لمحاولات الدخول')->numeric(),
                    Forms\Components\TextInput::make('lockout_duration_minutes')->label('مدة الحظر بعد تجاوز المحاولات (دقائق)')->numeric(),
                    Forms\Components\Toggle::make('enable_two_factor')->label('تفعيل المصادقة الثنائية (2FA)'),
                    Forms\Components\Toggle::make('require_email_verification')->label('إلزام التحقق من البريد'),
                ]),

            Section::make('كلمة المرور')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('force_password_change')->label('إلزام تغيير كلمة المرور'),
                    Forms\Components\TextInput::make('password_expiry_days')->label('صلاحية كلمة المرور (أيام، 0 = دائمة)')->numeric(),
                ]),

            Section::make('تقييد الوصول بـ IP')
                ->schema([
                    Forms\Components\Toggle::make('restrict_ip_access')->label('تفعيل تقييد IP')->reactive(),
                    Forms\Components\TagsInput::make('allowed_ips')
                        ->label('عناوين IP المسموح بها')
                        ->placeholder('أضف IP ثم اضغط Enter')
                        ->visible(fn ($get) => $get('restrict_ip_access')),
                ]),

            Section::make('التدقيق والمراقبة')
                ->schema([
                    Forms\Components\Toggle::make('log_user_activity')->label('تسجيل أنشطة المستخدمين'),
                ]),
        ]);
    }
}
