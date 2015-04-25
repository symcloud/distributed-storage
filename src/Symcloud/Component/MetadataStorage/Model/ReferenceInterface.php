<?php
namespace Symcloud\Component\MetadataStorage\Model;

interface ReferenceInterface
{
    /**
     * @return CommitInterface
     */
    public function getCommit();
}
