<?php

namespace Symcloud\Component\Common;

use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\BlobStorage\Model\BlobModel;

class Factory implements FactoryInterface
{
    /**
     * Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..)
     * @var string
     */
    private $algorithm;

    /**
     * Shared secret key used for generating the HMAC variant of the message digest
     * @var string
     */
    private $key;

    /**
     * {@inheritdoc}
     */
    public function createBlob($data, $hash = null)
    {
        $blob = new BlobModel();
        $blob->setData($data);
        $blob->setHash($hash !== null ? $hash : $this->createHash($data));

        return $blob;
    }

    /**
     * {@inheritdoc}
     */
    public function createHash($data)
    {
        return hash_hmac($this->algorithm, $data, $this->key);
    }
}
