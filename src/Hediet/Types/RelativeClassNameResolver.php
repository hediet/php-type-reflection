<?php

namespace Hediet\Types;

/**
 * Resolves a relative class name to an absolute class name.
 */
interface RelativeClassNameResolver
{
    /**
     * Resolves a relative class or interface name to an absolute one.
     * Absolute names must not start with a backslash.
     * 
     * @param string $relativeName The relative class or interface name. Does not start with a backslash.
     * @return string The absolute class or interface name. Must not start with a backslash.
     */
    function resolveRelativeName($relativeName);
}
