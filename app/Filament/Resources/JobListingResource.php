<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobListingResource\Pages;
use App\Models\JobListing;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;

class JobListingResource extends Resource
{
    protected static ?string $model = JobListing::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Job Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')->required(),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')->label('Employer')->required(),
            Forms\Components\RichEditor::make('description')->required()->columnSpanFull(),
            Forms\Components\TextInput::make('location')->required(),
            Forms\Components\Select::make('job_type')
                ->options([
                    'full-time'  => 'Full Time',
                    'part-time'  => 'Part Time',
                    'remote'     => 'Remote',
                    'contract'   => 'Contract',
                    'internship' => 'Internship',
                ])->required(),
            Forms\Components\TextInput::make('salary_min')->numeric()->prefix('$'),
            Forms\Components\TextInput::make('salary_max')->numeric()->prefix('$'),
            Forms\Components\Select::make('status')
                ->options(['draft' => 'Draft', 'active' => 'Active', 'closed' => 'Closed']),
            Forms\Components\Toggle::make('is_featured')->label('Featured'),
            Forms\Components\DateTimePicker::make('featured_until'),
            Forms\Components\DateTimePicker::make('expires_at'),
            Forms\Components\Select::make('tags')
                ->relationship('tags', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.company_name')->label('Company'),
                Tables\Columns\TextColumn::make('category.name')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'draft'  => 'warning',
                        'closed' => 'danger',
                        default  => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')->label('Applications'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'closed' => 'Closed']),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJobListings::route('/'),
            'create' => Pages\CreateJobListing::route('/create'),
            'edit'   => Pages\EditJobListing::route('/{record}/edit'),
        ];
    }
}
