<?php

namespace IAWP\Models;

/** @internal */
abstract class Model
{
    public abstract function id() : int;
    public abstract function table_type() : string;
}
