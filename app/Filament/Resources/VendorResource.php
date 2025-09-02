<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Enums\VendorBusinessEntityType;
use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\Page;
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
            'company' => Pages\VendorInformation::route('/{record}/company'),
            'contacts' => Pages\VendorContacts::route('/{record}/contacts'),
            'experiences' => Pages\VendorExperiences::route('/{record}/experiences'),
            'legality-licensing' => Pages\VendorLegalityLicensing::route('/{record}/legality-licensing'),
            'financial' => Pages\VendorFinancial::route('/{record}/financial'),
            'verification' => Pages\VendorVerificationStatus::route('/{record}/verification'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ActivitylogRelationManager::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = $page->generateNavigationItems([
            Pages\VendorVerificationStatus::class,
            Pages\VendorInformation::class,
            Pages\VendorContacts::class,
            Pages\VendorExperiences::class,
            Pages\VendorLegalityLicensing::class,
            Pages\VendorFinancial::class,

        ]);

        return array_map(fn ($item) => $item->icon(null), $items);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(fn ($record) => static::getUrl('verification', [
                'record' => $record,
            ]))
            ->modifyQueryUsing(function (Builder $query) {
                $query->unless(Auth::user()?->can(static::getModelLabel().'.withoutGlobalScope'), function (Builder $query) {
                    $query->where('user_id', Auth::id());
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('is_blacklisted')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($record): string => $record->is_blacklisted ? 'Blacklisted' : 'Active')
                    ->color(fn ($record): string => $record->is_blacklisted ? 'gray' : 'success'),
                Tables\Columns\TextColumn::make('company_name')->searchable(),
                Tables\Columns\IconColumn::make('verification_status')
                    ->label('Verification Status')
                    ->icon(fn (VendorStatus $state): string => $state->getIcon())
                    ->color(fn (VendorStatus $state): string => $state->getColor())
                    ->alignCenter(),
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
                Tables\Columns\TextColumn::make('vendorType.name')->label('Vendor Type')->badge()->searchable()->sortable(),
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
        $withoutGlobalScope = Auth::user()?->can(static::getModelLabel().'.withoutGlobalScope');

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(12)
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\View::make('vendor_logo_attachment_viewer')
                                        ->viewData([
                                            'collectionName' => 'vendor_logo_attachment',
                                            'viewLabel' => 'Company Logo',
                                        ])
                                        ->view('filament.forms.components.logo-viewer')
                                        ->visibleOn('view'),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_logo_attachment')
                                        ->collection('vendor_logo_attachment')
                                        ->maxFiles(1)
                                        ->label('Company Logo (JPEG, PNG, max 2MB)')
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
                                                Forms\Components\Select::make('business_entity_type')->options(VendorBusinessEntityType::class)->searchable()->preload()->live()->label('Business Entity Type'),
                                            ]),
                                        Forms\Components\TextInput::make('company_name')->required()->prefix(fn (Get $get): ?string => VendorBusinessEntityType::fromMixed($get('vendorProfile.business_entity_type'))?->prefix() ?? ''),
                                        Forms\Components\Select::make('business_field_id')->relationship('businessField', 'name')->searchable()->preload()->label('Business Field'),
                                        Forms\Components\TextInput::make('email')->email()->required(),
                                        Forms\Components\TextInput::make('phone')->tel(),
                                        Forms\Components\Select::make('vendor_type_id')->visible($withoutGlobalScope)->relationship('vendorType', 'name')->searchable()->preload()->label('Vendor Type'),
                                        Forms\Components\Select::make('user_id')->visible($withoutGlobalScope)->relationship('user', 'name')->required()->searchable(),
                                    ])
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 9,
                                    ]),
                            ]),
                    ]),

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getWidgets(): array
    {
        return [
            VendorResource\Widgets\OverviewVendorWidget::class,
        ];
    }
}
