<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Output;


/**
 * This config class is allows you to have your custom output formats
 * Either by using an custom template or by using a custom OutputFile-Gateway class
 *
 * In case you use an own OutputFile-Gateway make sure it implements the
 * Database2Code\Output\OutputFileGatewayInterface
 */
class OutputConfig
{
    /** @var $namespace string */
    private $namespace;

    /** @var $customTemplatePath string */
    protected $customTemplatePath;

    /** @var $customOutputClassname string */
    protected $customOutputClassname;

    /** @var $phpVersion string */
    protected $phpVersion;

    public function hasCustomTemplatePath() : bool
    {
        return isset($this->customTemplatePath);
    }

    public function getCustomTemplatePath(): string
    {
        return $this->customTemplatePath;
    }

    public function setCustomTemplatePath(string $customTemplatePath)
    {
        $this->validateFileReadablility($customTemplatePath);
        $this->customTemplatePath = $customTemplatePath;
    }

    public function hasCustomOutputClassname() : bool
    {
        return isset($this->customOutputClassname);
    }

    public function getCustomOutputClassname(): string
    {
        return $this->customOutputClassname;
    }

    /**
     * @param string $fullyQualifiedClassname A fully qualified name is the Classname including namespace
     */
    public function setCustomOutputClassname(string $fullyQualifiedClassname)
    {
        $this->validateFullyQualifiedClassname($fullyQualifiedClassname);
        $this->customOutputClassname = $fullyQualifiedClassname;
    }

    protected function validateFullyQualifiedClassname($fullyQualifiedClassname) {
        if(!class_exists($fullyQualifiedClassname)) {
            throw new \Error('Unknown class "'.$fullyQualifiedClassname.'"');
        }
    }

    protected function validateFileReadablility(string $customTemplatePath)
    {
        if (!is_file($customTemplatePath)) {
            throw new \Error('Template file "' . $customTemplatePath . '" does not exist!');
        }
        if (!is_readable($customTemplatePath)) {
            throw new \Error('Template file "' . $customTemplatePath . '" is not readabke!');
        }
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function hasNamespace() : bool
    {
        return isset($this->namespace);
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function setPhpVersion(string $phpVersion)
    {
        $this->phpVersion = $phpVersion;
    }

}