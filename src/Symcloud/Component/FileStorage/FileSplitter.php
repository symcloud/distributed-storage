<?php

namespace Symcloud\Component\FileStorage;

class FileSplitter implements FileSplitterInterface
{
    /**
     * @var int
     */
    private $maxLength;

    /**
     * FileSplitter constructor.
     * @param int $maxLength
     */
    public function __construct($maxLength = 255)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @param string $filePath
     * @param callback $callback
     */
    public function split($filePath, $callback)
    {
        $handle = fopen($filePath, 'r');
        $index = 0;

        while (!feof($handle)) {
            $buffer = fgets($handle, $this->maxLength + 1);

            if ($buffer !== false) {
                $callback($index++, $buffer);
            }
        }
    }
}
