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
final class HttpFile
{

    /**
     * HttpFile constructor.
     *
     * @param array $file
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(private readonly array $file)
    {
    }

    /**
     * Returns TRUE if the upload was valid.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function isValid(): bool
    {
        return $this->file['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Gets the content type.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getContentType(): string
    {
        return $this->file['type'];
    }

    /**
     * Gets the file name.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getName(): string
    {
        return $this->file['name'];
    }

    /**
     * Gets the file size.
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getSize(): int
    {
        return $this->file['size'];
    }

    /**
     * Gets the temporary file path.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getTemporaryFile(): string
    {
        return $this->file['tmp_name'];
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
            'content_type' => $this->getContentType(),
            'name' => $this->getName(),
            'size' => $this->getSize(),
            'temporary_file' => $this->getTemporaryFile()
        ];
    }

}
