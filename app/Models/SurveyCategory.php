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

    /**
     * Get the surveys associated with the category.
     *
     * Defines a has-many relationship with the Survey model.
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }
}
