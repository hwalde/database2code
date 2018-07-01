<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Input\MySQL;


use Database2Code\Input\InputConfig;

class MySQLInputConfig implements InputConfig
{

    /** @var $username string */
    protected $username;

    /** @var $password string */
    protected $password;

    /** @var $hostname string */
    protected $hostname;

    /** @var $port int */
    protected $port;

    public function __construct(string $username, string $password, string $hostname = 'localhost', $port = 3306)
    {
        $this->username = $username;
        $this->password = $password;
        $this->hostname = $hostname;
        $this->port = $port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname)
    {
        $this->hostname = $hostname;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port)
    {
        $this->port = $port;
    }

}