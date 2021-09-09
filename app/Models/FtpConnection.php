<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models;

use League\Flysystem\Sftp\SftpAdapter as SftpAdapter;

/**
 * Class FtpConnection
 * @package App\Models
 */
class FtpConnection
{
    const FILE_TYPE = 'file';
    /**
     * @var
     */
    protected $host;
    /**
     * @var
     */
    protected $username;
    /**
     * @var
     */
    protected $password;
    /**
     * @var
     */
    protected $port;
    /**
     * @var
     */
    protected $ssl;
    /**
     * @var
     */
    protected $lastError;

    /**
     * @var null
     */
    protected $connectionAdapter = null;

    /**
     * @param $host
     * @return mixed
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param $password
     * @return mixed
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $password;
    }

    /**
     * @param $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param $ssl
     * @return $this
     */
    public function setSsl($ssl = true)
    {
        $this->ssl = $ssl;
        return $this;
    }

    /**
     * @param false $dirPath
     * @param false $filePattern
     * @param string $sortBy
     * @param string $sortDir
     * @param false $limit
     */
    public function getFileList($dirPath = '', $filePattern = false, $sortBy = "timestamp", $sortDir = "asc", $limit = false)
    {
        $this->open();
        $this->lastError = false;

        $fileList = [];

        $files = $this->connectionAdapter->listContents($dirPath);

        usort(
            $files,
            function ($a, $b) use ($sortBy) {
                return $a[$sortBy] <=> $b[$sortBy];
            }
        );

        if ($sortDir == "desc") {
            $files = array_reverse($files);
        }

        foreach ($files as $fileData) {
            if ($fileData['type'] != self::FILE_TYPE) {
                continue;
            }

            $pathParts = explode('/', $fileData['path']);
            $fileName = end($pathParts);

            if ($filePattern && !preg_match($filePattern, $fileName)) {
                continue;
            }

            if ($limit && count($fileList) >= $limit) {
                break;
            }

            $fileList[] = $fileData['path'];
        }

        $this->close();
        return $fileList;
    }

    /**
     * @param array $args
     */
    protected function open()
    {
        $config = [];

        if ($this->host) {
            $config['host'] = $this->host;
        }
        if ($this->port) {
            $config['port'] = $this->port;
        }
        if ($this->username) {
            $config['username'] = $this->username;
        }
        if ($this->password) {
            $config['password'] = $this->password;
        }
        if ($this->ssl) {
            $config['ssl'] = $this->ssl;
        }

        $this->connectionAdapter = new SftpAdapter($config);
        $this->connectionAdapter->connect();
        return $this;
    }

    /**
     * @return $this
     */
    protected function close()
    {
        if ($this->connectionAdapter) {
            $this->connectionAdapter->disconnect();
        }
        return $this;
    }

    /**
     * @param $sourcePath
     * @param $targetPath
     */
    public function downloadFile($sourcePath, $targetPath)
    {
        $this->open();
        $this->lastError = false;
        $result = $this->connectionAdapter->getConnection()->get($sourcePath, $targetPath);
        if (!$result) {
            $this->lastError = $this->connectionAdapter->getConnection()->getLastSFTPError();
        }

        $this->close();
        return $result;
    }

    /**
     * @param $sourcePath
     * @param $targetPath
     * @return mixed
     */
    public function moveFile($sourcePath, $targetPath, $createDirIfNotExists = false)
    {
        $this->open();
        $this->lastError = false;

        $targetFileParts = explode('/', $targetPath);
        $targetFile = array_pop($targetFileParts);
        $targetDirPath = implode('/', $targetFileParts);

        if ($createDirIfNotExists) {
            $config = new \League\Flysystem\Config();
            $this->connectionAdapter->createDir($targetDirPath, $config);
        }

        if ($this->connectionAdapter->getConnection()->file_exists($targetPath)) {
            $this->connectionAdapter->delete($targetPath);
        }

        $result = $this->connectionAdapter->rename($sourcePath, $targetPath);
        if (!$result) {
            $this->lastError = $this->connectionAdapter->getConnection()->getLastSFTPError();
        }

        $this->close();
        return $result;
    }

    /**
     * @return mixed
     */
    public function getLastError()
    {
        return $this->lastError;
    }
}
