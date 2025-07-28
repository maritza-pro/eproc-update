<?php

declare(strict_types = 1);

namespace App\Models;

use App\Concerns\Model\WithTaxonomy;
use App\Models\Scopes\TaxonomyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(TaxonomyScope::class)]
class SurveyCategory extends Taxonomy
{
    use WithTaxonomy;

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }
}
