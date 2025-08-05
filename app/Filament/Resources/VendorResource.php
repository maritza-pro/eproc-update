<?php

declare(strict_types = 1);

namespace App\Filament\Resources;

use App\Concerns\Resource\Gate;
use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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

    public static function form(Form $form): Form
    {
        $withoutGlobalScope = ! Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope');

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')->required(),
                                Forms\Components\Select::make('business_field_id')->relationship('businessField', 'name')->searchable()->preload()->required()->label('Business Field'),
                                Forms\Components\TextInput::make('email')->email()->required(),
                                Forms\Components\TextInput::make('phone')->tel(),
                                Forms\Components\TextInput::make('tax_number'),
                                Forms\Components\TextInput::make('business_number'),
                                Forms\Components\TextInput::make('license_number'),
                                Forms\Components\Select::make('bank_vendor_id')
                                    ->label('Akun Bank Vendor')
                                    ->relationship(
                                        name: 'bankVendor',
                                        titleAttribute: 'account_number',
                                        modifyQueryUsing: fn(Builder $query) => $query->with(['bank'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn($record): string => "{$record->bank->name} - {$record->account_number} ({$record->account_name})")
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih akun bank yang sudah ada'),
                                Forms\Components\Select::make('taxonomies')->relationship('taxonomies', 'name')->searchable()->preload()->required()->label('Vendor Type'),
                                Forms\Components\Toggle::make('is_verified')->required()->disabled($withoutGlobalScope),
                                Forms\Components\Select::make('user_id')->relationship('user', 'name')->required()->searchable()->default($withoutGlobalScope ? Auth::id() : null)->disabled($withoutGlobalScope)->dehydrated(),
                            ]),

                        Forms\Components\Tabs::make('Tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('General Information')
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->relationship('vendorProfile')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('business_entity_type')
                                                            ->label('Business Entity Type')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('npwp')
                                                            ->label('Tax Identification Number (NPWP)')
                                                            ->nullable(),

                                                        Forms\Components\TextInput::make('nib')
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
                                                    ->label('Head Office Address'),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make('PIC Contact')
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->relationship('vendorPic')
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

                                                        Forms\Components\TextInput::make('ktp_number')
                                                            ->label('National ID (KTP) Number')
                                                            ->nullable(),

                                                        Forms\Components\View::make('attachment_viewer')
                                                            ->view('filament.forms.components.attachment-viewer')
                                                            ->visibleOn('view'),

                                                        Forms\Components\SpatieMediaLibraryFileUpload::make('attachment')
                                                            ->collection('attachment')
                                                            ->maxFiles(1)
                                                            ->label('Attachment (JPEG, PNG, PDF, max 2MB)')
                                                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                                                            ->maxSize(2048)
                                                            ->downloadable()
                                                            ->hiddenOn('view'),
                                                    ])
                                            ])
                                    ])
                            ])
                    ])

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

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
                Tables\Columns\IconColumn::make('is_verified')->boolean()->alignCenter(),
                Tables\Columns\TextColumn::make('company_name')->searchable(),
                Tables\Columns\TextColumn::make('businessField.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('bankVendor.bank.name')
                    ->label('Bank')
                    ->searchable(
                        query: fn(Builder $query, string $search): Builder => $query->whereHas('bankVendor.bank', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('bankVendor', function ($q) use ($search) {
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
}
