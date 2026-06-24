<?php

namespace App\Filament\Pages\Settings;

use App\Settings\MailSettings;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;

class MailSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-envelope';
    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات البريد';
    protected static ?int    $navigationSort  = 2;
    protected static string  $settings = MailSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('إعدادات SMTP (الإرسال)')
                ->icon('heroicon-o-paper-airplane')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('mail_host')
                        ->label('خادم SMTP')
                        ->default('smtp.hostinger.com')
                        ->required(),
                    Forms\Components\TextInput::make('mail_port')
                        ->label('المنفذ')
                        ->numeric()
                        ->default(465),
                    Forms\Components\Select::make('mail_encryption')
                        ->label('نوع التشفير')
                        ->options(['ssl' => 'SSL', 'tls' => 'TLS', '' => 'بدون'])
                        ->default('ssl'),
                    Forms\Components\TextInput::make('mail_username')
                        ->label('اسم المستخدم / البريد')
                        ->email(),
                    Forms\Components\TextInput::make('mail_password')
                        ->label('كلمة المرور')
                        ->password()
                        ->revealable(),
                    Forms\Components\TextInput::make('mail_from_address')
                        ->label('بريد المُرسِل')
                        ->email(),
                    Forms\Components\TextInput::make('mail_from_name')
                        ->label('اسم المُرسِل')
                        ->columnSpanFull(),
                ]),

            Section::make('إعدادات IMAP (الاستقبال)')
                ->icon('heroicon-o-inbox-arrow-down')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('imap_host')
                        ->label('خادم IMAP')
                        ->default('imap.hostinger.com'),
                    Forms\Components\TextInput::make('imap_port')
                        ->label('المنفذ')
                        ->numeric()
                        ->default(993),
                    Forms\Components\Select::make('imap_encryption')
                        ->label('التشفير')
                        ->options(['ssl' => 'SSL', 'tls' => 'TLS'])
                        ->default('ssl'),
                    Forms\Components\Toggle::make('imap_validate_cert')
                        ->label('التحقق من شهادة SSL')
                        ->default(true),
                    Forms\Components\TextInput::make('imap_username')
                        ->label('اسم المستخدم'),
                    Forms\Components\TextInput::make('imap_password')
                        ->label('كلمة المرور')
                        ->password()
                        ->revealable(),
                    Forms\Components\TextInput::make('imap_sync_folder')
                        ->label('مجلد المزامنة')
                        ->default('INBOX'),
                    Forms\Components\TextInput::make('imap_sync_interval')
                        ->label('فترة المزامنة (بالدقائق)')
                        ->numeric()
                        ->default(1),
                ]),

            // زر اختبار الاتصال
            Actions::make([
                Action::make('testSmtp')
                    ->label('اختبار SMTP')
                    ->icon('heroicon-o-signal')
                    ->color('info')
                    ->action(function (\Filament\Schemas\Components\Utilities\Get $get) {
                        try {
                            $host = $get('mail_host');
                            $port = $get('mail_port');
                            $encryption = $get('mail_encryption');
                            $username = $get('mail_username');
                            $password = $get('mail_password');
                            $fromAddress = $get('mail_from_address');
                            $fromName = $get('mail_from_name');
                            $toAddress = auth()->user()->email;

                            $mailer = \Mail::build([
                                'transport' => 'smtp',
                                'host' => $host,
                                'port' => $port,
                                'encryption' => $encryption,
                                'username' => $username,
                                'password' => $password,
                                'timeout' => 10,
                            ]);

                            $mailer->raw('Test CorpChat SMTP', function ($msg) use ($fromAddress, $fromName, $toAddress) {
                                $msg->from($fromAddress, $fromName)
                                    ->to($toAddress)
                                    ->subject('SMTP Test');
                            });

                            Notification::make()->title('✅ SMTP يعمل بنجاح')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('❌ فشل الاتصال: ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ]),
        ]);
    }
}
