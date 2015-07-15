<?php

/*
 * This file is part of Tuli, a static analyzer for PHP
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace Tuli;

class TypeResolver {
    
    protected $components;

    public function __construct(array $components) {
        $this->components = $components;
    }

    public function allowsNull(Type $type) {
        if (($type->type & Type::TYPE_NULL) !== 0) {
            return true;
        }
        return false;
    }

    public function resolves(Type $a, Type $b) {
        if ($a->equals($b)) {
            return true;
        }
        if ($b->type === Type::TYPE_CALLABLE) {
            // TODO: do a true callable check
            // Callable is hard to do statically... 
            return true;
        }
        if ($a->type === Type::TYPE_LONG && $b->type === Type::TYPE_DOUBLE) {
            return true;
        }
        if ($a->type === Type::TYPE_USER && $b->type === Type::TYPE_USER) {
            foreach ($b->userTypes as $bt) {
                $bt = strtolower($bt);
                foreach ($a->userTypes as $at) {
                    $at = strtolower($at);
                    if (!isset($this->components['resolves'][$bt][$at])) {
                        continue 2;
                    }
                }
                // We got here, means we found an B type that's resolved by all A types
                return true;
            }
            // That means there is no A type that fully resolves at least one B type
            return false;
        }
        if (($b->type & $a->type) === $a->type) {
            return true;
        }
        return false;
    }

}