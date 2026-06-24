<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ChatSettings;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;

class ChatSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-chat-bubble-left-right';
    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات المحادثة';
    protected static ?int    $navigationSort  = 3;
    protected static string  $settings = ChatSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('المرفقات')
                ->icon('heroicon-o-paper-clip')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('allow_file_attachments')->label('السماح بإرفاق الملفات'),
                    Forms\Components\TextInput::make('max_attachment_size_mb')->label('الحجم الأقصى للمرفق (MB)')->numeric(),
                    Forms\Components\TagsInput::make('allowed_attachment_types')->label('أنواع الملفات المسموحة')->columnSpanFull(),
                ]),

            Section::make('سلوك الرسائل')
                ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('enable_read_receipts')->label('تفعيل علامات القراءة'),
                    Forms\Components\Toggle::make('enable_typing_indicators')->label('مؤشر الكتابة'),
                    Forms\Components\Toggle::make('enable_message_reactions')->label('تفاعلات Emoji'),
                    Forms\Components\Toggle::make('allow_message_deletion')->label('السماح بحذف الرسائل'),
                    Forms\Components\Toggle::make('allow_message_editing')->label('السماح بتعديل الرسائل'),
                    Forms\Components\TextInput::make('message_edit_window_minutes')
                        ->label('نافذة التعديل (دقائق)')
                        ->numeric()
                        ->visible(fn ($get) => $get('allow_message_editing')),
                    Forms\Components\TextInput::make('message_retention_days')
                        ->label('أيام الاحتفاظ بالرسائل (0 = دائمًا)')
                        ->numeric(),
                ]),

            Section::make('المجموعات والرسائل المباشرة')
                ->icon('heroicon-o-user-group')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('enable_group_chats')->label('تفعيل المجموعات'),
                    Forms\Components\TextInput::make('max_group_members')->label('الحد الأقصى لأعضاء المجموعة')->numeric(),
                    Forms\Components\Toggle::make('enable_direct_messages')->label('تفعيل الرسائل الخاصة'),
                ]),
        ]);
    }
}
