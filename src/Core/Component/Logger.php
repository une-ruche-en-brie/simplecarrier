<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace MondialRelay\MondialRelay\Core\Component;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Logger
 *
 * @author Pascal Fischer <contact@scaledev.fr>
 * @since 3.3.2
 */
final class Logger
{
    const LOG_TYPE_INFO = 'INFO';
    const LOG_TYPE_SUCCESS = 'SUCCESS';
    const LOG_TYPE_WARNING = 'WARNING';
    const LOG_TYPE_ERROR = 'ERROR';

    /**
     * The log file's path.
     *
     * @var string
     */
    private $filePath;

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->filePath = _PS_MODULE_DIR_ . 'mondialrelay/logs/php_logs';
        $this->checkFile();
    }

    /**
     * Checks if the logs file exists and creates it if it is not the case.
     *
     * @return void
     */
    private function checkFile()
    {
        if (!is_dir(dirname($this->filePath))) {
            @mkdir(dirname($this->filePath), 0777, true);
        }
    }

    /**
     * Adds a log message.
     *
     * @param string $message
     * @param string|null $type
     * @return void
     */
    public function addLog($message, $type = null)
    {
        $print = '[' . date('Y-m-d H:i:s') . '] ';

        if ($type !== null) {
            $print .= '[' . $type . '] ';
        }

        file_put_contents(
            $this->filePath,
            $print . print_r($message, true) . "\n",
            FILE_APPEND
        );
    }

    /**
     * Adds a log message as "info" type.
     *
     * @param string $message
     * @return void
     */
    public function addInfo($message)
    {
        $this->addLog($message, self::LOG_TYPE_INFO);
    }

    /**
     * Adds a log message as "success" type.
     *
     * @param string $message
     * @return void
     */
    public function addSuccess($message)
    {
        $this->addLog($message, self::LOG_TYPE_SUCCESS);
    }

    /**
     * Adds a log message as "warning" type.
     *
     * @param string $message
     * @return void
     */
    public function addWarning($message)
    {
        $this->addLog($message, self::LOG_TYPE_WARNING);
    }

    /**
     * Adds a log message as "error" type.
     *
     * @param string $message
     * @return void
     */
    public function addError($message)
    {
        $this->addLog($message, self::LOG_TYPE_ERROR);
    }

    /**
     * Clears the logs file.
     *
     * @return bool
     */
    public function clearLogsFile()
    {
        if (is_file($this->filePath)) {
            return unlink($this->filePath);
        }

        return true;
    }
}
