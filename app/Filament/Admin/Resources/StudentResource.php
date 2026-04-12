<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Filament\Admin\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\Facades\Date;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    // Must match Filament v4's HasNavigation trait property type.
    protected static string | BackedEnum | null $navigationIcon = Heroicon::Users;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('User'),
                Forms\Components\TextInput::make('roll_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required(),
                Forms\Components\Select::make('college_class_id')
                    ->relationship('collegeClass', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->nullable(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('admission_year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(now()->year + 1)
                    ->helperText('Start year (e.g. 2026).'),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->disk('r2')
                    ->collection('student-photos')
                    ->image()
                    ->maxSize(1024)
                    ->label('Student Photo'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Faculty members can only see students from their assigned classes
        if ($user?->hasRole('faculty')) {
            $query->whereHas('collegeClass', function (Builder $q) use ($user) {
                $q->whereHas('faculty', function (Builder $fq) use ($user) {
                    $fq->where('user_id', $user->id);
                });
            });
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roll_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('admission_year')
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('student-photos')
                    ->label('Photo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
