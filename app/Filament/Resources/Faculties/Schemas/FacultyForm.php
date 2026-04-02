<?php

namespace App\Filament\Resources\Faculties\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FacultyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('department_id')
                    ->required()
                    ->numeric(),
                TextInput::make('employee_id')
                    ->required(),
                TextInput::make('qualification'),
                DatePicker::make('joining_date'),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('specialization')
                    ->columnSpanFull(),
            ]);
    }
}
