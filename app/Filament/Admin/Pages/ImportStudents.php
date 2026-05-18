<?php

namespace App\Filament\Admin\Pages;

use App\Exports\StudentImportTemplate;
use App\Imports\FacultyImport;
use App\Imports\StudentsImport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class ImportStudents extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected string $view = 'filament.admin.pages.import-students';

    protected static string|UnitEnum|null $navigationGroup = 'Students';

    protected static ?string $navigationLabel = 'Bulk Import';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public ?array $lastImportResults = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Import Type')
                    ->tabs([
                        Tabs\Tab::make('Students')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                FileUpload::make('student_file')
                                    ->label('Upload Student Excel File')
                                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                                    ->directory('imports')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Faculty')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                FileUpload::make('faculty_file')
                                    ->label('Upload Faculty Excel File')
                                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                                    ->directory('imports')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Student Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('downloadStudentTemplate')
                ->color('info'),
            Action::make('import')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->action('import')
                ->color('success'),
        ];
    }

    public function downloadStudentTemplate()
    {
        return Excel::download(new StudentImportTemplate, 'student_import_template.xlsx');
    }

    public function import()
    {
        $data = $this->form->getState();

        if (! empty($data['student_file'])) {
            $this->importStudents($data['student_file']);
        } elseif (! empty($data['faculty_file'])) {
            $this->importFaculty($data['faculty_file']);
        } else {
            Notification::make()
                ->title('Error')
                ->body('Please upload a file to import.')
                ->danger()
                ->send();

            return;
        }

        $this->form->fill();
    }

    private function importStudents(mixed $filePath): void
    {
        try {
            $import = new StudentsImport;

            if (is_array($filePath)) {
                $file = $filePath[0] ?? null;
                if (! $file) {
                    throw new \Exception('No file provided');
                }
                $path = storage_path('app/'.$file);
            } else {
                $path = storage_path('app/'.$filePath);
            }

            Excel::import($import, $path);

            $this->lastImportResults = [
                'type' => 'students',
                'imported' => $import->imported,
                'skipped' => $import->skipped,
                'errors' => $import->errors,
            ];

            $importedCount = count($import->imported);
            $skippedCount = count($import->skipped);
            $errorCount = count($import->errors);

            Notification::make()
                ->title('Import Complete')
                ->body("Imported: {$importedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function importFaculty(mixed $filePath): void
    {
        try {
            $import = new FacultyImport;

            if (is_array($filePath)) {
                $file = $filePath[0] ?? null;
                if (! $file) {
                    throw new \Exception('No file provided');
                }
                $path = storage_path('app/'.$file);
            } else {
                $path = storage_path('app/'.$filePath);
            }

            Excel::import($import, $path);

            $this->lastImportResults = [
                'type' => 'faculty',
                'imported' => $import->imported,
                'skipped' => $import->skipped,
                'errors' => $import->errors,
            ];

            $importedCount = count($import->imported);
            $skippedCount = count($import->skipped);
            $errorCount = count($import->errors);

            Notification::make()
                ->title('Import Complete')
                ->body("Imported: {$importedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
