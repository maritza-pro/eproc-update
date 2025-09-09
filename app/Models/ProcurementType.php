<?php

declare(strict_types = 1);

namespace App\Models;

use App\Concerns\Model\WithTaxonomy;
use App\Models\Scopes\TaxonomyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([TaxonomyScope::class])]
class ProcurementType extends Taxonomy
{
    use WithTaxonomy;

    public function procurements(): HasMany
    {
        return $this->hasMany(related: Procurement::class);
    }
}
