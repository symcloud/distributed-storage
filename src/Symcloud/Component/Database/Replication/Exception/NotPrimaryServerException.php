<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Replication\Exception;

use Symcloud\Component\Database\Replication\ServerInterface;

class NotPrimaryServerException extends \Exception
{
    /**
     * @var ServerInterface
     */
    private $primaryServer;

    /**
     * NotPrimaryServerException constructor.
     *
     * @param ServerInterface $primaryServer
     */
    public function __construct(ServerInterface $primaryServer)
    {
        parent::__construct(
            sprintf(
                'Server is not primary of this object. Redirect to given Server "%s:%s".',
                $primaryServer->getHost(),
                $primaryServer->getPort()
            )
        );

        $this->primaryServer = $primaryServer;
    }

    /**
     * @return ServerInterface
     */
    public function getPrimaryServer()
    {
        return $this->primaryServer;
    }
}
