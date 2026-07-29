<?php

namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum UserSort: string
{
    case Newest = 'newest';
    case Oldest = 'oldest';
    case NameAsc = 'name_asc';
    case NameDesc = 'name_desc';
    case EmailAsc = 'email_asc';
    case EmailDesc = 'email_desc';
}
