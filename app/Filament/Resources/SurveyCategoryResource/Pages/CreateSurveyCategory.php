<?php

declare(strict_types = 1);

namespace App\Filament\Resources\SurveyCategoryResource\Pages;

use App\Filament\Resources\SurveyCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSurveyCategory extends CreateRecord
{
    protected static string $resource = SurveyCategoryResource::class;
}
