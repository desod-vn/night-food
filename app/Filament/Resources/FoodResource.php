<?php

namespace App\Filament\Resources;

use App\Constants\NavigationConstant;
use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Models\Food;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Category;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = NavigationConstant::MANAGE_MENU;

    protected static ?string $modelLabel = 'Đồ ăn';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên đồ ăn')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('categories')
                    ->label('Danh mục')
                    ->multiple()
                    ->relationship('categories', 'id')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Giá bán')
                    ->required()
                    ->numeric()
                    ->default(1000)
                    ->prefix('VND'),
                Forms\Components\TextInput::make('cost')
                    ->label('Giá vốn')
                    ->required()
                    ->numeric()
                    ->default(1000)
                    ->gte('price')
                    ->prefix('VND'),
                Forms\Components\FileUpload::make('image')
                    ->label('Hình ảnh')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('300')
                    ->imageResizeTargetHeight('300')
                    ->required(),
                Forms\Components\Textarea::make('commitment')
                    ->label('Cam kết')
                    ->maxLength(255)
                    ->rows(4),
                Forms\Components\RichEditor::make('description')
                    ->label('Mô tả')
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Hình ảnh'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên đồ ăn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Danh mục'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Giá vốn')
                    ->money('VND')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }    

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
