<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CourseResource\Pages;
use App\Filament\Admin\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    // Must match Filament v4's HasNavigation trait property type.
    protected static string | BackedEnum | null $navigationIcon = Heroicon::BookOpen;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(3),
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('college_class_id')
                    ->relationship('collegeClass', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Class'),
                Forms\Components\Select::make('faculty_id')
                    ->relationship('faculty', 'employee_id')
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Faculty $record): string => $record->user?->name ?? $record->employee_id)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Faculty'),
                SpatieMediaLibraryFileUpload::make('thumbnail')
                    ->disk('r2')
                    ->collection('course-thumbnails')
                    ->image()
                    ->maxSize(1024)
                    ->label('Course Thumbnail'),
                SpatieMediaLibraryFileUpload::make('materials')
                    ->disk('r2')
                    ->collection('course-materials')
                    ->multiple()
                    ->enableDownload()
                    ->maxSize(5120)
                    ->label('Course Materials'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('collegeClass.name')
                    ->label('Class')
                    ->searchable(),
                Tables\Columns\TextColumn::make('faculty.user.name')
                    ->label('Faculty')
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection('course-thumbnails')
                    ->label('Thumbnail'),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
