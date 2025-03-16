<?php

namespace App\Modules\Version;

use League\Fractal\TransformerAbstract;

class VersionTransformer extends TransformerAbstract
{
    public function transform($version)
    {
        return $version->toArray();
    }
}
