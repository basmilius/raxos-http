<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Attribute;

use Attribute;

/**
 * Class Optional
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Attribute
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Optional {}
