<?php

namespace Loouss\ObsClient\Internal\Signature;

use Loouss\ObsClient\Internal\Common\Model;

interface SignatureInterface
{
    function doAuth(array &$requestConfig, array &$params, Model $model);
}