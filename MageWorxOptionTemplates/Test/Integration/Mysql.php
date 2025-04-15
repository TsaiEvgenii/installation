<?php
/**
 * Mysql
 * @package BelVG
 * @author    artsem.belvg@gmai.com
 * @copyright Copyright © 2012 - 2020
 */

namespace BelVG\MageWorxOptionTemplates\Test\Integration;


class Mysql extends \Magento\TestFramework\Db\AbstractDb
{
    /**
     * Default port
     */
    const DEFAULT_PORT = 3306;

    /**
     * Defaults extra file name
     */
    const DEFAULTS_EXTRA_FILE_NAME = 'defaults_extra.cnf';

    /**
     * @var string
     */
    protected $_dbDumpFile;
    protected string $_defaultsExtraFile;
    protected int $_port;

    public function __construct($host,
                                $user,
                                $password,
                                $schema,
                                $varPath,
                                \Magento\Framework\Shell $shell,
                                $pathToBackUp)
    {

        parent::__construct($host, $user, $password, $schema, $varPath, $shell);
        $this->_port = self::DEFAULT_PORT;
        if (strpos($this->_host, ':') !== false) {
            list($host, $port) = explode(':', $this->_host);
            $this->_host = $host;
            $this->_port = (int) $port;
        }
        $this->_defaultsExtraFile = rtrim($this->_varPath, '\\/') . '/' . self::DEFAULTS_EXTRA_FILE_NAME;
        $this->_dbDumpFile = $pathToBackUp;
    }

    /**
     * @inheritDoc
     */
    public function cleanup()
    {
        // TODO: Implement cleanup() method.
    }

    /**
     * @inheritDoc
     */
    protected function getSetupDbDumpFilename()
    {
        return $this->_dbDumpFile;
    }

    /**
     * @inheritDoc
     */
    public function isDbDumpExists()
    {
        return file_exists($this->getSetupDbDumpFilename());
    }

    /**
     * @inheritDoc
     */
    public function storeDbDump()
    {
        // TODO: Implement storeDbDump() method.
    }

    /**
     * @inheritDoc
     */
    public function restoreFromDbDump()
    {
        $this->ensureDefaultsExtraFile();
        if (!$this->isDbDumpExists()) {
            throw new \LogicException("DB dump file does not exist: " . $this->getSetupDbDumpFilename());
        }
        $this->_shell->execute(
            'mysql --defaults-file=%s --host=%s --port=%s %s < %s',
            [$this->_defaultsExtraFile, $this->_host, $this->_port, $this->_schema, $this->getSetupDbDumpFilename()]
        );
    }
    private function ensureDefaultsExtraFile()
    {
        if (!file_exists($this->_defaultsExtraFile)) {
            $this->assertVarPathWritable();
            $extraConfig = ['[client]', 'user=' . $this->_user, 'password="' . $this->_password . '"'];
            file_put_contents($this->_defaultsExtraFile, implode(PHP_EOL, $extraConfig));
            chmod($this->_defaultsExtraFile, 0640);
        }
    }

    /**
     * @inheritDoc
     */
    public function getVendorName()
    {
        // TODO: Implement getVendorName() method.
    }
}