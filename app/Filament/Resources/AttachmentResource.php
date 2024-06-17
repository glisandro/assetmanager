<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Attachment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Tables\Columns\LinkColumn;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Redirect;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AttachmentResource\Pages;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use App\Filament\Resources\AttachmentResource\RelationManagers;

class AttachmentResource extends Resource
{
    protected static ?string $model = Attachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('filename')
                ->preserveFilenames()
                ->downloadable()
                ->openable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Split::make([
                    ImageColumn::make('file')
                        ->getStateUsing(fn ($record) => $record->filename)
                        ->state(function (Attachment $record) {
                            $imageData = getimagesize(Storage::path('public\\' . $record->filename));

                            if ($imageData === false)
                                return Storage::disk('public')->url('attachments.icon.documet.svg');
                            else
                                return $record->filename;
                            
                        }),

                    //static::getFileColumn('filename')->label('File'),
                    TextColumn::make('filename')
                        //->visible(fn ($record) => self::isImage($record) !== true),
                        //->defaultImageUrl(url('/images/placeholder.png')),//->view('tables.columns.attachment-column'),
                    /*TextColumn::make('filename')
                        ->hidden(fn ($record) => $record === 'active')*/
                        //->defaultImageUrl(url('/images/placeholder.png'))
                    //TextColumn::make('filename'),
                    //LinkColumn::make('filename'),
                // Tables\Columns\TextColumn::make('filename')
                //     ->formatStateUsing(
                //         fn (string $state) => self::goTo($state, 'Open', null),
                //     )
                // ]),
                
                //LinkColumn::make('filename'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('open')
                    ->url(fn (Attachment $attachment): string => route('attachment.open', $attachment))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->openUrlInNewTab()
                /*->formatStateUsing(
                    fn (string $state) => self::goTo($state, 'See on new tab', 's'),
                )*/
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Método para determinar dinámicamente la clase de la columna
    protected static function getFileColumn(string $columnName): Tables\Columns\Column
    {
        return Tables\Columns\TextColumn::make($columnName)
            ->formatStateUsing(function ($state) use ($columnName) {
                if (self::isImage($state)) {
                    return ImageColumn::make($columnName);
                }
                return TextColumn::make($columnName)->formatStateUsing(fn ($state) => basename($state));
            });
    }

    // Método para determinar si el archivo es una imagen
    protected static function isImage($filePath): bool
    {
        $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $imageExtensions);
    }

    protected static function isImages($file)
    {
        //$filePath = $file->getPathname();
        $imageData = getimagesize(Storage::url($file));

        if ($imageData !== false)
            return false;
        else
            return true;
    }

    protected static function goTo(string $link, string $title, ?string $tooltip)
    {
        return new HtmlString(Blade::render('filament::components.link', [
            'color' => 'primary',
            'tooltip' => $tooltip,
            'href' => Storage::url($link),
            'target' => '_blank',
            'slot' => $title,
            'icon' => 'heroicon-o-arrow-top-right-on-square',
        ]));
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
            'index' => Pages\ListAttachments::route('/'),
            'create' => Pages\CreateAttachment::route('/create'),
            'edit' => Pages\EditAttachment::route('/{record}/edit'),
        ];
    }
}
