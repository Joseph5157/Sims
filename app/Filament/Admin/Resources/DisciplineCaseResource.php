<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DisciplineCaseResource\Pages\CreateDisciplineCase;
use App\Filament\Admin\Resources\DisciplineCaseResource\Pages\EditDisciplineCase;
use App\Filament\Admin\Resources\DisciplineCaseResource\Pages\ListDisciplineCases;
use App\Models\DisciplineCase;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DisciplineCaseResource extends Resource
{
    protected static ?string $model = DisciplineCase::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'user.name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('faculty_id')
                    ->relationship('faculty', 'user.name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3),

                Forms\Components\Select::make('severity')
                    ->required()
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),

                Forms\Components\FileUpload::make('attachment')
                    ->optional(),

                Forms\Components\Select::make('status')
                    ->default('open')
                    ->options([
                        'open' => 'Open',
                        'under_review' => 'Under Review',
                        'resolved' => 'Resolved',
                    ]),

                Forms\Components\Hidden::make('admin_id'),

                Forms\Components\DatePicker::make('resolved_at')
                    ->optional()
                    ->visible(fn (callable $get) => $get('status') === 'resolved'),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::id();
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('faculty.user.name')
                    ->label('Faculty')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state))),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'under_review' => 'Under Review',
                        'resolved' => 'Resolved',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            'index' => ListDisciplineCases::route('/'),
            'create' => CreateDisciplineCase::route('/create'),
            'edit' => EditDisciplineCase::route('/{record}/edit'),
        ];
    }
}
