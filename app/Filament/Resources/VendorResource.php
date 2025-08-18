<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\Pages\CreateVendor;
use App\Filament\Resources\VendorResource\Pages\EditVendor;
use App\Models\Vendor;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Hexters\HexaLite\HasHexaLite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class VendorResource extends Resource
{
    use Gate {
        Gate::defineGates insteadof HasHexaLite;
    }
    use HasHexaLite;

    protected static ?string $model = Vendor::class;

    protected static ?string $modelLabel = 'Vendor';

    protected static ?string $navigationGroup = 'Vendors';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view' => Pages\ViewVendor::route('/{record}'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ActivitylogRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            VendorResource\Widgets\OverviewVendorWidget::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->modifyQueryUsing(function (Builder $query) {
                $query->unless(Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope'), function (Builder $query) {
                    $query->where('user_id', Auth::id());
                });
            })
            ->columns([
                Tables\Columns\IconColumn::make('verification_status')
                    ->label('Status')
                    ->icon(fn (VendorStatus $state): string => $state->getIcon())
                    ->color(fn (VendorStatus $state): string => $state->getColor())
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('company_name')->searchable(),
                Tables\Columns\TextColumn::make('businessField.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('bankVendors.bank.name')
                    ->label('Bank')
                    ->searchable(
                        query: fn (Builder $query, string $search): Builder => $query->whereHas('bankVendors.bank', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('bankVendors', function ($q) use ($search) {
                            $q->where('account_number', 'like', "%{$search}%");
                        })
                    )
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('taxonomies.name')->label('Vendor Type')->badge()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\ViewField::make('verification_status')
                            ->view('filament.forms.components.status-badge')
                            ->hidden(fn ($livewire): bool => $livewire instanceof CreateVendor)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('ⓘ Verification Notes')
                            ->disabled()
                            ->autosize()
                            ->helperText('Please check the notes above and update your details below before resubmitting.')
                            ->visible(fn (?Vendor $record): bool => $record?->verification_status === VendorStatus::Rejected),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')->required(),
                                Forms\Components\Select::make('business_field_id')->relationship('businessField', 'name')->searchable()->preload()->required()->label('Business Field'),
                                Forms\Components\TextInput::make('email')->email()->required(),
                                Forms\Components\TextInput::make('phone')->tel(),
                                Forms\Components\TextInput::make('tax_number'),
                                Forms\Components\TextInput::make('business_number'),
                                Forms\Components\TextInput::make('license_number'),
                                Forms\Components\Select::make('taxonomies')->relationship('taxonomies', 'name')->searchable()->preload()->required()->label('Vendor Type'),
                                Forms\Components\Select::make('user_id')->visible(! $withoutGlobalScope)->relationship('user', 'name')->required()->searchable()->default($withoutGlobalScope ? Auth::id() : null)->disabled($withoutGlobalScope)->dehydrated(),
                            ]),

                        Forms\Components\Tabs::make('Tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('General Information')
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->relationship(
                                                'vendorProfile',
                                                condition: fn (?array $state): bool => collect($state ?? [])
                                                    ->filter(fn ($v) => filled($v))
                                                    ->isNotEmpty()
                                            )
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('business_entity_type')
                                                            ->label('Business Entity Type')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('tax_identification_number')
                                                            ->label('Tax Identification Number (NPWP)')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('business_identification_number')
                                                            ->label('Business Registration Number (NIB)')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('website')
                                                            ->label('Website')
                                                            ->url()
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('established_year')
                                                            ->label('Year Established')
                                                            ->numeric()
                                                            ->minValue(1900)
                                                            ->maxValue(now()->year),

                                                        Forms\Components\TextInput::make('employee_count')
                                                            ->label('Number of Employees')
                                                            ->numeric()
                                                            ->nullable(),
                                                    ]),

                                                Forms\Components\Textarea::make('head_office_address')
                                                    ->autosize()
                                                    ->label('Head Office Address')
                                                    ->nullable(),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('PIC Contact')
                                    ->schema([
                                        Forms\Components\Repeater::make('vendorContacts')
                                            ->relationship()
                                            ->label('')
                                            ->addActionLabel('Add PIC Contact')
                                            ->collapsible()
                                            ->collapsed()
                                            ->defaultItems(0)
                                            ->itemLabel(function (array $state): ?string {
                                                $parts = [];

                                                if (! empty($state['name'])) {
                                                    $firstName = explode(' ', $state['name'])[0];
                                                    $parts[] = $firstName;
                                                }

                                                if (! empty($state['position'])) {
                                                    $parts[] = $state['position'];
                                                }

                                                if (! empty($parts)) {
                                                    return implode(' · ', $parts);
                                                }

                                                return 'New PIC Contact';
                                            })
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Full Name')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('position')
                                                            ->label('Job Title / Position')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('phone_number')
                                                            ->label('Phone Number')
                                                            ->tel()
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('email')
                                                            ->label('Email Address')
                                                            ->email()
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('identity_number')
                                                            ->label('National ID (KTP) Number')
                                                            ->nullable(),

                                                        Forms\Components\View::make('vendor_contact_attachment_viewer')
                                                            ->viewData([
                                                                'collectionName' => 'vendor_contact_attachment',
                                                                'viewLabel' => 'Contact Attachment',
                                                            ])
                                                            ->view('filament.forms.components.attachment-viewer')
                                                            ->visibleOn('view'),

                                                        Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_contact_attachment')
                                                            ->collection('vendor_contact_attachment')
                                                            ->maxFiles(1)
                                                            ->label('Contact Attachment (JPEG, PNG, PDF, max 2MB)')
                                                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                                                            ->maxSize(2048)
                                                            ->downloadable()
                                                            ->hiddenOn('view'),
                                                    ]),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Legality & Licensing')
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->relationship(
                                                'vendorDeed',
                                                condition: fn (?array $state): bool => collect($state ?? [])
                                                    ->filter(fn ($v) => filled($v))
                                                    ->isNotEmpty()
                                            )
                                            ->schema([
                                                Forms\Components\Section::make('Deed Information')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('deed_number')->label('Deed Number')->nullable(),
                                                            Forms\Components\DatePicker::make('deed_date')->label('Deed Date')->nullable(),

                                                            Forms\Components\TextInput::make('deed_notary_name')->label('Notary Name')->nullable(),
                                                            Forms\Components\TextInput::make('approval_number')->label('Approval Number (Kemenkumham)')->nullable(),

                                                            Forms\Components\TextInput::make('latest_amendment_number')->label('Latest Amendment Number')->nullable(),
                                                            Forms\Components\DatePicker::make('latest_amendment_date')->label('Latest Amendment Date')->nullable(),

                                                            Forms\Components\TextInput::make('latest_amendment_notary')->label('Latest Amendment Notary')->nullable(),
                                                            Forms\Components\TextInput::make('latest_approval_number')->label('Latest Approval Number (Kemenkumham)')->nullable(),

                                                            Forms\Components\View::make('vendor_deed_attachment_viewer')
                                                                ->viewData([
                                                                    'collectionName' => 'vendor_deed_attachment',
                                                                    'viewLabel' => 'Deed Attachment',
                                                                ])
                                                                ->view('filament.forms.components.attachment-viewer')
                                                                ->visibleOn('view'),
                                                            Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_deed_attachment')
                                                                ->collection('vendor_deed_attachment')
                                                                ->label('Deed Attachment (PDF, max 2MB)')
                                                                ->acceptedFileTypes(['application/pdf'])
                                                                ->maxSize(2048)
                                                                ->maxFiles(1)
                                                                ->downloadable()
                                                                ->hiddenOn('view'),
                                                        ]),
                                                    ]),
                                            ]),
                                        Forms\Components\Group::make()
                                            ->relationship('vendorBusinessLicense')
                                            ->schema([
                                                Forms\Components\Section::make('Business License Information')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('license_number')->label('License Number')->nullable(),
                                                            Forms\Components\DatePicker::make('issued_at')->label('Issued Date')->nullable(),

                                                            Forms\Components\TextInput::make('issued_by')->label('Issued By')->nullable(),
                                                            Forms\Components\DatePicker::make('expires_at')->label('Expires Date')->nullable(),

                                                            Forms\Components\View::make('vendor_license_attachment_viewer')
                                                                ->viewData([
                                                                    'collectionName' => 'vendor_license_attachment',
                                                                    'viewLabel' => 'Business License Attachment',
                                                                ])
                                                                ->view('filament.forms.components.attachment-viewer')
                                                                ->visibleOn('view'),
                                                            Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_license_attachment')
                                                                ->collection('vendor_license_attachment')
                                                                ->label('Business License Attachment (PDF, max 2MB)')
                                                                ->acceptedFileTypes(['application/pdf'])
                                                                ->maxSize(2048)
                                                                ->maxFiles(1)
                                                                ->downloadable()
                                                                ->hiddenOn('view'),
                                                        ]),
                                                    ]),
                                            ]),
                                        Forms\Components\Group::make()
                                            ->relationship('vendorTaxRegistration')
                                            ->schema([
                                                Forms\Components\Section::make('Tax Registration Certificate Information')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('name')->label('Name')->nullable(),
                                                            Forms\Components\TextInput::make('certificate_number')->label('Certificate Number')->nullable(),

                                                            Forms\Components\TextInput::make('confirmation_status')->label('Confirmation Status')->nullable(),
                                                            Forms\Components\Select::make('tax_obligation')
                                                                ->label('Tax Obligation')
                                                                ->options([
                                                                    'collecting' => 'Collecting',
                                                                    'depositing' => 'Depositing',
                                                                    'reporting' => 'Reporting VAT/Luxury Tax',
                                                                ]),

                                                            Forms\Components\TextInput::make('registered_tax_office')
                                                                ->label('Registered Tax Office')
                                                                ->nullable(),
                                                            Forms\Components\TextArea::make('address')
                                                                ->label('Address')
                                                                ->autosize()
                                                                ->nullable(),

                                                            Forms\Components\View::make('vendor_tax_registration_attachment_viewer')
                                                                ->viewData([
                                                                    'collectionName' => 'vendor_tax_registration_attachment',
                                                                    'viewLabel' => 'Tax Registration Certificate Attachment',
                                                                ])
                                                                ->view('filament.forms.components.attachment-viewer')
                                                                ->visibleOn('view'),
                                                            Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_tax_registration_attachment')
                                                                ->collection('vendor_tax_registration_attachment')
                                                                ->label('Tax Registration Certificate Attachment (PDF, max 2MB)')
                                                                ->acceptedFileTypes(['application/pdf'])
                                                                ->maxSize(2048)
                                                                ->maxFiles(1)
                                                                ->downloadable()
                                                                ->hiddenOn('view'),
                                                        ]),
                                                    ]),
                                            ]),
                                        Forms\Components\Group::make()
                                            ->relationship('vendorBusinessCertificate')
                                            ->schema([
                                                Forms\Components\Section::make('Business Certificate Information')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('certificate_number')->label('Certificate Number')->nullable(),
                                                            Forms\Components\DatePicker::make('issued_at')->label('Issued Date')->nullable(),

                                                            Forms\Components\TextInput::make('issued_by')->label('Issued By')->nullable(),
                                                            Forms\Components\DatePicker::make('expires_at')->label('Expires Date')->nullable(),

                                                            Forms\Components\TextInput::make('classification')->label('Classification')->nullable(),
                                                            Forms\Components\View::make('vendor_certificate_attachment_viewer')
                                                                ->viewData([
                                                                    'collectionName' => 'vendor_certificate_attachment',
                                                                    'viewLabel' => 'Business Certificate Attachment',
                                                                ])
                                                                ->view('filament.forms.components.attachment-viewer')
                                                                ->visibleOn('view'),
                                                            Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_certificate_attachment')
                                                                ->collection('vendor_certificate_attachment')
                                                                ->label('Business Certificate Attachment (PDF, max 2MB)')
                                                                ->acceptedFileTypes(['application/pdf'])
                                                                ->maxSize(2048)
                                                                ->maxFiles(1)
                                                                ->downloadable()
                                                                ->hiddenOn('view'),
                                                        ]),
                                                    ]),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Financial')
                                    ->schema([
                                        Forms\Components\Repeater::make('bankVendors')
                                            ->relationship()
                                            ->label('')
                                            ->addActionLabel('Add Bank Account')
                                            ->collapsible()
                                            ->collapsed()
                                            ->defaultItems(0)
                                            ->itemLabel(function (array $state): ?string {
                                                $account = $state['account_name'] ?? null;
                                                $bankName = null;

                                                if (! empty($state['bank_id'])) {
                                                    $bankName = \App\Models\Bank::query()
                                                        ->whereKey($state['bank_id'])
                                                        ->value('name');
                                                }

                                                if ($bankName && $account) {
                                                    return "{$bankName} - {$account}";
                                                }

                                                return 'New Bank Account';
                                            })
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('bank_id')
                                                            ->relationship(
                                                                name: 'bank',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true))
                                                            ->searchable()
                                                            ->preload()
                                                            ->nullable()
                                                            ->label('Bank Name'),
                                                        Forms\Components\TextInput::make('account_name')
                                                            ->label('Account Name')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('account_number')
                                                            ->label('Account Number')
                                                            ->nullable(),

                                                        Forms\Components\Toggle::make('is_active')
                                                            ->nullable(),
                                                        Forms\Components\View::make('recent_financial_report_attachment_viewer')
                                                            ->viewData([
                                                                'collectionName' => 'recent_financial_report_attachment',
                                                                'viewLabel' => 'Recent Financial Report attachment',
                                                            ])
                                                            ->view('filament.forms.components.attachment-viewer')
                                                            ->visibleOn('view'),
                                                        Forms\Components\SpatieMediaLibraryFileUpload::make('recent_financial_report_attachment')
                                                            ->collection('recent_financial_report_attachment')
                                                            ->label('Recent Financial Report (PDF, max 2MB)')
                                                            ->acceptedFileTypes(['application/pdf'])
                                                            ->maxSize(2048)
                                                            ->maxFiles(1)
                                                            ->downloadable()
                                                            ->hiddenOn('view'),
                                                    ]),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Expertise')
                                    ->schema([
                                        Forms\Components\Repeater::make('vendorExpertises')
                                            ->relationship()
                                            ->label('')
                                            ->addActionLabel('Add Expertise')
                                            ->collapsible()
                                            ->collapsed()
                                            ->columns(1)
                                            ->defaultItems(0)
                                            ->itemLabel(function (array $state): ?string {
                                                if (! empty($state['expertise'])) {
                                                    return '- ' . $state['expertise'];
                                                }

                                                return 'New Expertise';
                                            })
                                            ->schema([
                                                Forms\Components\TextInput::make('expertise')
                                                    ->label('Expertise')
                                                    ->nullable(),

                                                Forms\Components\Select::make('expertise_level')
                                                    ->label('Expertise Level')
                                                    ->nullable()
                                                    ->options([
                                                        'basic' => 'Basic',
                                                        'intermediate' => 'Intermediate',
                                                        'expert' => 'Expert',
                                                    ]),

                                                Forms\Components\Textarea::make('description')
                                                    ->label('Description')
                                                    ->autosize()
                                                    ->nullable()
                                                    ->maxLength(100),

                                                Forms\Components\View::make('vendor_expertise_attachment_viewer')
                                                    ->viewData([
                                                        'collectionName' => 'vendor_expertise_attachment',
                                                        'viewLabel' => 'Expertise Attachment',
                                                    ])
                                                    ->view('filament.forms.components.attachment-viewer')
                                                    ->visibleOn('view'),

                                                Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_expertise_attachment')
                                                    ->collection('vendor_expertise_attachment')
                                                    ->maxFiles(1)
                                                    ->label('Expertise Attachment (PDF, max 2MB)')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->maxSize(2048)
                                                    ->downloadable()
                                                    ->hiddenOn('view'),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Experience')
                                    ->schema([
                                        Forms\Components\Repeater::make('vendorExperiences')
                                            ->relationship()
                                            ->label('')
                                            ->addActionLabel('Add Experience')
                                            ->collapsible()
                                            ->collapsed()
                                            ->defaultItems(0)
                                            ->itemLabel(function (array $state): ?string {
                                                if (! empty($state['project_name'])) {
                                                    return '- ' . $state['project_name'];
                                                }

                                                return 'New Experience';
                                            })
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('project_name')
                                                            ->label('Project Name')
                                                            ->nullable(),
                                                        Forms\Components\Select::make('business_field_id')->relationship('businessField', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->nullable()
                                                            ->label('Business Field'),

                                                        Forms\Components\TextInput::make('location')
                                                            ->label('Project Location')
                                                            ->nullable(),
                                                        Forms\Components\TextInput::make('stakeholder')
                                                            ->label('Stakeholder')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('contract_number')
                                                            ->label('Contract Number')
                                                            ->nullable(),
                                                        Forms\Components\TextInput::make('project_value')
                                                            ->label('Project Value')
                                                            ->nullable(),

                                                        Forms\Components\DatePicker::make('start_date')
                                                            ->label('Start Date')
                                                            ->nullable(),
                                                        Forms\Components\DatePicker::make('end_date')
                                                            ->label('End Date')
                                                            ->nullable(),

                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Description')
                                                            ->autosize()
                                                            ->nullable()
                                                            ->maxLength(100),

                                                        Forms\Components\View::make('vendor_experience_attachment_viewer')
                                                            ->viewData([
                                                                'collectionName' => 'vendor_experience_attachment',
                                                                'viewLabel' => 'Experience Attachment',
                                                            ])
                                                            ->view('filament.forms.components.attachment-viewer')
                                                            ->visibleOn('view'),
                                                        Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_experience_attachment')
                                                            ->collection('vendor_experience_attachment')
                                                            ->maxFiles(1)
                                                            ->label('Experience Attachment (PDF, max 2MB)')
                                                            ->acceptedFileTypes(['application/pdf'])
                                                            ->maxSize(2048)
                                                            ->downloadable()
                                                            ->hiddenOn('view'),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Section::make('Statement & Agreement')
                            ->visible(
                                fn ($livewire): bool => $livewire instanceof CreateVendor
                                && $withoutGlobalScope
                            )
                            ->schema([
                                Actions::make([
                                    Actions\Action::make('view_agreement')
                                        ->label('Please read the terms carefully before proceeding.')
                                        ->link()
                                        ->color('primary')
                                        ->modalHeading('Statement & Agreement')
                                        ->modalContent(fn () => view('filament.forms.components.statement-and-agreement'))
                                        ->modalSubmitAction(false)
                                        ->modalCancelActionLabel('Close')
                                        ->modalFooterActionsAlignment(Alignment::End)
                                        ->modalWidth('3xl'),
                                ]),
                                Forms\Components\Checkbox::make('agreement')
                                    ->label('By checking the box or clicking "Submit" on this application, the vendor acknowledges that they have read, understood, and agree to be bound by the above Statement and Agreement.')
                                    ->accepted()
                                    ->required()
                                    ->validationMessages([
                                        'accepted' => 'Please accept the Statement & Agreement to continue.',
                                    ]),
                            ])
                            ->collapsible(),
                        Forms\Components\Group::make()
                            ->visible(
                                fn ($livewire): bool => $livewire instanceof EditVendor
                                && ! $withoutGlobalScope
                                && $livewire->getRecord()->verification_status === VendorStatus::Pending
                            )
                            ->schema([
                                Forms\Components\ToggleButtons::make('verification_status')
                                    ->label('Verification Action')
                                    ->inline()
                                    ->required()
                                    ->live()
                                    ->options([
                                        VendorStatus::Approved->value => VendorStatus::Approved->getLabel(),
                                        VendorStatus::Rejected->value => VendorStatus::Rejected->getLabel(),
                                    ])
                                    ->icons([
                                        VendorStatus::Approved->value => 'heroicon-o-check-circle',
                                        VendorStatus::Rejected->value => 'heroicon-o-x-circle',
                                    ])
                                    ->colors([
                                        VendorStatus::Approved->value => 'success',
                                        VendorStatus::Rejected->value => 'danger',
                                    ]),

                                Forms\Components\Textarea::make('rejection_reason')
                                    ->label('Reason for Rejection')
                                    ->rows(4)
                                    ->autosize()
                                    ->visible(fn (Get $get): bool => $get('verification_status') === VendorStatus::Rejected->value)
                                    ->required(fn (Get $get): bool => $get('verification_status') === VendorStatus::Rejected->value),
                            ]),

                    ]),

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getResubmitAction(): Action
    {
        return Action::make('resubmit')
            ->label('Resubmit Verification')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->visible(fn ($record): bool => $record->verification_status === VendorStatus::Rejected)
            ->requiresConfirmation()
            // TODO : There's a typo in the modal heading. 'Verfication' should be 'Verification'.
            ->modalHeading('Resubmit Vendor Verification?')
            ->modalDescription('Your vendor information will be reopened for updates and sent for review again. Do you want to continue?')
            ->modalSubmitActionLabel('Yes, Resubmit')
            ->action(function ($record) {
                $record->update([
                    'verification_status' => VendorStatus::Pending,
                ]);

                Notification::make()
                    ->title('Your vendor verification has been resubmitted.')
                    ->success()
                    ->send();
            });
    }
}
