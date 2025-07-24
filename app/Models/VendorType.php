<?php

declare(strict_types = 1);

namespace App\Models;

use App\Models\Scopes\TaxonomyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TaxonomyScope::class])]
class VendorType extends Taxonomy {}
