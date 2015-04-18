<?php

namespace Symcloud\Component\Database;

use Symcloud\Component\BlobStorage\Model\BlobInterface;

interface AdapterInterface
{
    /**
     * @param BlobInterface $blob
     * @return BlobInterface
     */
    public function saveBlob($blob);
}
