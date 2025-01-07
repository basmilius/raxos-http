<?php
declare(strict_types=1);

namespace Raxos\Http;

use JetBrains\PhpStorm\ArrayShape;
use const UPLOAD_ERR_OK;

/**
 * Class HttpFile
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
final readonly class HttpFile
{

    public bool $isValid;
    public string $contentType;
    public string $name;
    public int $size;
    public string $temporaryFile;

    /**
     * HttpFile constructor.
     *
     * @param array $file
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        private array $file
    )
    {
        $this->isValid = $this->file['error'] === UPLOAD_ERR_OK;
        $this->contentType = $this->file['type'];
        $this->name = $this->file['name'];
        $this->size = $this->file['size'];
        $this->temporaryFile = $this->file['tmp_name'];
    }

    /**
     * Gets debug info.
     *
     * @return array|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ArrayShape([
        'content_type' => 'string',
        'name' => 'string',
        'size' => 'int',
        'temporary_file' => 'string'
    ])]
    public function __debugInfo(): ?array
    {
        return [
            'content_type' => $this->contentType,
            'name' => $this->name,
            'size' => $this->size,
            'temporary_file' => $this->temporaryFile
        ];
    }

}
