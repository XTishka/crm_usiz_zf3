<?php

namespace Application\Domain;

interface ValueObjectInterface {

    public static function factory($data = null, $underscoreSeparatedKeys = true);

    public function toArray($underscoreSeparatedKeys = true);

    public function toJson($underscoreSeparatedKeys = true);

}