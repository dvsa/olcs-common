<?php

namespace Common\Service\Data;

/**
 * Interface CrudInterface
 *
 * @package Common\Service\Data
 */
interface CrudInterface
{
    public function get($id);

    public function create(array $data);

    public function update(array $data);

    public function delete($id);
}
