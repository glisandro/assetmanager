<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Asset;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Exports\AssetExporter;
use App\Filament\Imports\AssetImporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use GalleryJsonMedia\Form\JsonMediaGallery;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\AssetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Filament\Resources\AssetResource\RelationManagers\AttachmentRelationManager;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            
            ->schema([
                Forms\Components\Select::make('building_id')
                    ->relationship(name: 'building', titleAttribute: 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                /*Forms\Components\FileUpload::make('attachment')
                    ->multiple()
                    ->downloadable()
                    ->reorderable()
                    ->maxFiles(10),*/
                /*JsonMediaGallery::make('attachment')
                    ->directory('page')
                    ->reorderable()
                    ->preserveFilenames()
                    ->acceptedFileTypes(['jpg'])
                    //->visibility() // only public for now - NO S3
                    ->maxSize(4 * 1024)
                    //->minSize()
                    //->maxFiles()
                    //->minFiles()
                    ->replaceNameByTitle() // If you want to show title (alt customProperties) against file name
                    ->image() // only images by default , u need to choose one (images or document)
                    ->document() // only documents (eg: pdf, doc, xls,...)
                    ->downloadable()
                    ->deletable(),   */
                //AttachmentFileUpload::make(),          
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Checkbox::make('active')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(AssetImporter::class),
            ])
            ->columns([
                TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('attachment')
                    ->circular()
                    ->stacked()
                    ->limit(2)
                    ->limitedRemainingText(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('building.name')
                    ->searchable()
                    ->sortable(),

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
               
                Tables\Filters\SelectFilter::make('active')
                ->options([
                    1 => 'Active',
                    0=> 'Inactive',
                ])
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->label('Export')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('primary')
                            ->modalHidden(true)
                            ->exporter(AssetExporter::class)
                    
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttachmentRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
