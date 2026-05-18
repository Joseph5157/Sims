<?php

namespace App\Filament\Admin\Resources;

use App\Enums\GradingType;
use App\Enums\SubjectType;
use App\Filament\Admin\Resources\SubjectResource\Pages\CreateSubject;
use App\Filament\Admin\Resources\SubjectResource\Pages\EditSubject;
use App\Filament\Admin\Resources\SubjectResource\Pages\ListSubjects;
use App\Models\Subject;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                Forms\Components\Select::make('college_class_id')
                    ->label('Class')
                    ->relationship('collegeClass', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('faculty_id')
                    ->label('Faculty')
                    ->relationship('faculty', 'employee_id')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\Select::make('subject_type')
                    ->label('Subject Type')
                    ->options(collect(SubjectType::cases())->mapWithKeys(
                        fn (SubjectType $t): array => [$t->value => $t->label()]
                    ))
                    ->required()
                    ->default(SubjectType::Theory->value),

                Forms\Components\Select::make('grading_type')
                    ->label('Grading Type')
                    ->options(collect(GradingType::cases())->mapWithKeys(
                        fn (GradingType $t): array => [$t->value => $t->label()]
                    ))
                    ->required()
                    ->default(GradingType::Marks->value),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Forms\Components\TextInput::make('credits')
                    ->numeric()
                    ->default(3)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('faculty.employee_id')
                    ->label('Faculty')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (SubjectType $state): string => $state->label())
                    ->color(fn (SubjectType $state): string => match ($state) {
                        SubjectType::Theory => 'info',
                        SubjectType::Practical => 'warning',
                        SubjectType::Elective => 'primary',
                        SubjectType::Project => 'success',
                    }),

                Tables\Columns\TextColumn::make('grading_type')
                    ->label('Grading')
                    ->badge()
                    ->formatStateUsing(fn (GradingType $state): string => $state->label())
                    ->color('gray'),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Type')
                    ->options(collect(SubjectType::cases())->mapWithKeys(
                        fn (SubjectType $t): array => [$t->value => $t->label()]
                    )),

                Tables\Filters\SelectFilter::make('college_class_id')
                    ->label('Class')
                    ->relationship('collegeClass', 'name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubjects::route('/'),
            'create' => CreateSubject::route('/create'),
            'edit' => EditSubject::route('/{record}/edit'),
        ];
    }
}
