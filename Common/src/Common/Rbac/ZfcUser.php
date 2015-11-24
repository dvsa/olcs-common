<?php

namespace Common\Rbac;

/**
 * @NOTE This is temporary to bridge between zfcuser and openam
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
