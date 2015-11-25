<?php

namespace Common\Rbac;

/**
 * @todo Remove this class when we are fully integrated with OpenAM
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ZfcUser extends User
{
    private $password;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}
