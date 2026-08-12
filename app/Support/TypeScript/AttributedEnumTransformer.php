<?php

namespace App\Support\TypeScript;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Data\TransformationContext;
use Spatie\TypeScriptTransformer\PhpNodes\PhpClassNode;
use Spatie\TypeScriptTransformer\Transformed\Transformed;
use Spatie\TypeScriptTransformer\Transformed\Untransformable;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;

/**
 * `EnumTransformer` emits every backed enum in a scanned directory, so pointing
 * the transformer at `app/Enums` would dump backend-only enums into
 * `generated.d.ts`. This adds the same `#[TypeScript]` opt-in that
 * `AttributedClassTransformer` applies to `Data` classes.
 */
class AttributedEnumTransformer extends EnumTransformer
{
    public function transform(
        PhpClassNode $phpClassNode,
        TransformationContext $context
    ): Transformed|Untransformable {
        if (count($phpClassNode->getAttributes(TypeScript::class)) === 0) {
            return Untransformable::create();
        }

        return parent::transform($phpClassNode, $context);
    }
}
