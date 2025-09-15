<?php

declare(strict_types=1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Enums\VendorBusinessEntityType;
use App\Filament\Resources\VendorResource;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class VendorInformation extends Page implements HasForms
{
    use HasUnsavedDataChangesAlert, InteractsWithForms, InteractsWithRecord;

    protected static string $resource = VendorResource::class;

    protected static ?string $title = 'Company information';

    protected static string $view = 'filament.resources.vendor-resource.pages.vendor-information';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        $withoutGlobalScope = Auth::user()?->can(VendorResource::getModelLabel() . '.withoutGlobalScope');

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Fieldset::make((string) __('ⓘ Vendor Status'))
                            ->schema([
                                Forms\Components\View::make('blacklist_flag')
                                    ->view('filament.forms.components.blacklist-badge')
                                    ->columnSpanFull(),
                            ])
                            ->hidden(fn (?Vendor $record) => ! $record?->is_blacklisted)
                            ->columnSpanFull(),
                        Forms\Components\Fieldset::make((string) __('ⓘ Verification Status'))
                            ->schema([
                                Forms\Components\View::make('verification_status')
                                    ->view('filament.forms.components.status-badge')
                                    ->columnSpanFull(),
                            ])
                            ->hidden(fn (?Vendor $record) => $record?->is_blacklisted)
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(12)
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\View::make('vendor_logo_attachment_viewer')
                                        ->viewData([
                                            'collectionName' => 'vendor_logo_attachment',
                                            'viewLabel' => (string) __('Company Logo'),
                                        ])
                                        ->view('filament.forms.components.logo-viewer')
                                        ->visibleOn('view'),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_logo_attachment')
                                        ->collection('vendor_logo_attachment')
                                        ->maxFiles(1)
                                        ->label((string) __('Company Logo (JPEG, PNG, max 2MB)'))
                                        ->acceptedFileTypes(['image/*'])
                                        ->maxSize(2048)
                                        ->downloadable()
                                        ->hiddenOn('view'),
                                ])
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 3,
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->relationship('vendorProfile')
                                            ->schema([
                                                Forms\Components\Select::make('business_entity_type')->options(VendorBusinessEntityType::class)->searchable()->preload()->live()->required()->label((string) __('Business Entity Type')),
                                            ]),
                                        Forms\Components\TextInput::make('company_name')->label((string) __('Company Name'))->required()->prefix(fn (Get $get): string => VendorBusinessEntityType::fromMixed($get('vendorProfile.business_entity_type'))?->prefix() ?? ''),
                                        Forms\Components\Select::make('business_field_id')->relationship('businessField', 'name')->searchable()->preload()->required()->label((string) __('Business Field')),
                                        Forms\Components\TextInput::make('email')->email()->required(),
                                        Forms\Components\TextInput::make('phone')->label((string) __('Phone Number'))->tel(),
                                        Forms\Components\Select::make('vendor_type_id')->visible($withoutGlobalScope)->relationship('vendorType', 'name')->searchable()->preload()->label((string) __('Vendor Type')),
                                        Forms\Components\Select::make('user_id')->label((string) __('User'))->visible($withoutGlobalScope)->relationship('user', 'name')->required()->searchable(),
                                    ])
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 9,
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data')
            // @phpstan-ignore argument.type
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var \App\Models\Vendor $vendor */
        $vendor = $this->getRecord();

        $vendor->fill($data);
        $vendor->save();

        Notification::make()
            ->title((string) __('Company Information updated successfully'))
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Company Information';
    }
}
