<?php

namespace App\Filament\Admin\Pages;

use App\Models\SchoolSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSchoolSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.manage-school-settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'School Settings';

    protected static ?string $title = 'School Settings';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill(SchoolSetting::current()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('School Identity')
                    ->description('Basic information displayed across the portal and on certificates.')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('school_name')
                                ->label('School Name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('principal_name')
                                ->label('Principal Name')
                                ->maxLength(255),

                            TextInput::make('affiliation_number')
                                ->label('Affiliation Number')
                                ->maxLength(100),

                            TextInput::make('established_year')
                                ->label('Established Year')
                                ->maxLength(10),
                        ]),

                        TextInput::make('school_motto')
                            ->label('School Motto')
                            ->maxLength(255),

                        Textarea::make('school_address')
                            ->label('School Address')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),

                Section::make('Contact Details')
                    ->description('Phone and email shown in communications.')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('school_phone')
                                ->label('Phone')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('school_email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Report Card Appearance')
                    ->description('Controls how generated report cards look.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        ColorPicker::make('report_card_color')
                            ->label('Report Card Header Color')
                            ->helperText('Used as the header/accent color on printed report cards.'),

                        Textarea::make('report_card_footer_text')
                            ->label('Report Card Footer Text')
                            ->rows(2)
                            ->maxLength(500),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = SchoolSetting::current();
        $setting->fill($data)->save();

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
