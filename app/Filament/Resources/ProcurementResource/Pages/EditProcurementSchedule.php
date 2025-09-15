<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Enums\RequirementType;
use App\Filament\Resources\ProcurementResource;
use App\Models\Procurement;
use App\Models\ProcurementSchedule;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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

    public function mount(Procurement $ownerRecord, ProcurementSchedule $record): void
    {
        $this->ownerRecord = $ownerRecord;

        $this->record = $record;

        abort_unless($this->record->procurement_id === $this->ownerRecord->id, 404);

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
                    ->label((string) __('Submission Needed?'))
                    ->required()
                    ->live(),
                Forms\Components\RichEditor::make('description')
                    ->label((string) __('Description'))
                    ->columnSpanFull(),
                Forms\Components\Section::make((string) __('Requirements'))
                    ->visible(fn (Get $get) => $get('is_submission_needed'))
                    ->dehydrated(fn (Get $get) => $get('is_submission_needed'))
                    ->schema([
                        Forms\Components\Repeater::make('requirements')
                            ->relationship()
                            ->hiddenLabel()
                            ->addActionLabel((string) __('Add Requirement'))
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label((string) __('Title'))
                                    ->required(),

                                Forms\Components\Select::make('type')
                                    ->label((string) __('Type'))
                                    ->options(RequirementType::class)
                                    ->live()
                                    ->required(),

                                Forms\Components\TextInput::make('points')
                                    ->label((string) __('Points'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->visible(fn (Get $get) => ! (self::typeEnum($get)?->isChoice() ?? false))
                                    ->dehydrated(fn (Get $get) => ! (self::typeEnum($get)?->isChoice() ?? false)),

                                Forms\Components\Toggle::make('is_required')
                                    ->label((string) __('Is Required?'))
                                    ->default(false),

                                Forms\Components\Textarea::make('description')
                                    ->label((string) __('Description'))
                                    ->rows(3)
                                    ->nullable()
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('requirementOptions')
                                    ->label((string) __('Options'))
                                    ->relationship('requirementOptions')
                                    ->visible(fn (Get $get) => (self::typeEnum($get)?->isChoice() ?? false))
                                    ->dehydrated(fn (Get $get) => (self::typeEnum($get)?->isChoice() ?? false))
                                    ->minItems(2)
                                    ->addActionLabel((string) __('Add option'))
                                    ->columns(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->label((string) __('Option label'))
                                            ->required()
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('points')
                                            ->label((string) __('Points'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->columnSpan(1),

                                        Forms\Components\Toggle::make('is_correct')
                                            ->label((string) __('Is Correct?'))
                                            ->inline(false),
                                    ])
                                    ->helperText((string) __('At least two options are required')),
                            ]),
                    ]),
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

    private static function typeEnum(Get $get): ?RequirementType
    {
        $type = $get('type');

        return $type instanceof RequirementType ? $type : RequirementType::tryFrom((string) $type);
    }
}
