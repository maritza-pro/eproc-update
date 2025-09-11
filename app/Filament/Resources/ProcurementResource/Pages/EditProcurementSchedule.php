<?php

declare(strict_types = 1);

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Filament\Resources\ProcurementResource;
use App\Models\Procurement;
use App\Models\ProcurementSchedule;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class EditProcurementSchedule extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ProcurementResource::class;

    protected static ?string $title = 'Edit Procurement Schedule';

    protected static string $view = 'filament.resources.procurement-resource.pages.edit-procurement-schedule';

    public ?array $data = [];

    public Procurement $ownerRecord;

    public ProcurementSchedule $record;

    public function mount(Procurement $ownerRecord, ProcurementSchedule $record): void
    {
        $this->ownerRecord = $ownerRecord;

        $this->record = $record;

        abort_unless($this->record->procurement_id == $this->ownerRecord->id, 404);

        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        $procurement = $this->ownerRecord;

        return $form
            ->schema([
                Forms\Components\Select::make('schedule_id')
                    ->label((string) __('Schedule'))
                    ->relationship(
                        name: 'schedule',
                        titleAttribute: 'name',
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('start_date')
                    ->label((string) __('Start Date'))
                    ->required()
                    ->minDate($procurement->start_date)
                    ->maxDate($procurement->end_date),
                Forms\Components\DatePicker::make('end_date')
                    ->label((string) __('End Date'))
                    ->required()
                    ->afterOrEqual('start_date')
                    ->minDate($procurement->start_date)
                    ->maxDate($procurement->end_date),
                Forms\Components\Toggle::make('is_submission_needed')
                    ->label((string) __('Submission Needed'))
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->label((string) __('Description'))
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title((string) __('Company Information updated successfully'))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
{
    return [
        Action::make('back')
            ->label((string) __('Back'))
            ->icon('heroicon-o-arrow-left')
            ->color('gray')
            ->url(
                ProcurementResource::getUrl('view', ['record' => $this->ownerRecord])
            ),
    ];
}
}
