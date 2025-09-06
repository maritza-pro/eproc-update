<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorExperiences extends ManageRelatedRecords
{
    protected static string $relationship = 'vendorExperiences';

    protected static string $resource = VendorResource::class;

    protected static ?string $title = 'Experiences';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('project_name')
            ->defaultSort('start_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('businessField.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stakeholder')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\ViewColumn::make('vendor_experience_attachment')
                    ->label((string) __('Attachment'))
                    ->viewData([
                        'collectionName' => 'vendor_experience_attachment',
                        'viewLabel' => 'Experience Attachment',
                    ])
                    ->view('filament.forms.components.table-attachment-viewer'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label((string) __('Add Experience'))
                    ->modalHeading('Add Experience')
                    ->createAnother(false)
                    ->modalFooterActionsAlignment(Alignment::End),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalFooterActionsAlignment(Alignment::End),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('project_name')
                    ->label((string) __('Project Name'))
                    ->nullable(),
                Forms\Components\Select::make('business_field_id')->relationship('businessField', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label((string) __('Business Field')),

                Forms\Components\TextInput::make('location')
                    ->label((string) __('Project Location'))
                    ->nullable(),
                Forms\Components\TextInput::make('stakeholder')
                    ->label((string) __('Stakeholder'))
                    ->nullable(),

                Forms\Components\TextInput::make('contract_number')
                    ->label((string) __('Contract Number'))
                    ->nullable(),
                Forms\Components\TextInput::make('project_value')
                    ->label((string) __('Project Value'))
                    ->nullable(),

                Forms\Components\DatePicker::make('start_date')
                    ->label((string) __('Start Date'))
                    ->nullable(),
                Forms\Components\DatePicker::make('end_date')
                    ->label((string) __('End Date'))
                    ->nullable(),

                Forms\Components\Textarea::make('description')
                    ->label((string) __('Description'))
                    ->autosize()
                    ->nullable()
                    ->maxLength(100),

                Forms\Components\SpatieMediaLibraryFileUpload::make('vendor_experience_attachment')
                    ->collection('vendor_experience_attachment')
                    ->maxFiles(1)
                    ->label((string) __('Experience Attachment (PDF, max 2MB)'))
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->downloadable()
                    ->hiddenOn('view'),
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return 'Experiences';
    }
}
