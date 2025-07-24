<?php

declare(strict_types = 1);

namespace App\Models;

use App\Concerns\Model\WithTaxonomy;
use App\Models\Scopes\TaxonomyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(TaxonomyScope::class)]
class SurveyCategory extends Taxonomy
{
    use WithTaxonomy;
}
