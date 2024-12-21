<?php

namespace App\Transformers;

use App\Models\taxi\CancellationReason;
use League\Fractal\TransformerAbstract;

class CancellationReasonTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(CancellationReason $reason)
    {
        return [
            'id' => $reason->id,
            'reason' => $reason->reason,
            'user_type' => $reason->user_type
        ];
    }
}
