<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de la ScaleDEV est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter ScaleDEV a l'adresse: contact@scaledev.fr
 * ...........................................................................
 *
 * @author ScaleDEV
 * @copyright Copyright (c) 2022 ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * Support by mail: support@scaledev.fr
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Logger
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
class MondialrelayLogger
{
    /**
     * @var string The logs' directory
     */
    private $logsDir;

    /**
     * @var string The logs file
     */
    private $logsFile;

    /**
     * Logger's constructor.
     *
     * @param string $logsFile The logs file
     */
    public function __construct($logsFile)
    {
        $this->setLogsDir(dirname(__FILE__) . '/../logs');
        $this->setLogsFile($this->getLogsDir() . '/' . $logsFile . '.log');

        if (!is_dir($this->getLogsDir())) {
            @mkdir($this->getLogsDir());
        }
    }

    /**
     * Get an instance of Logger.
     *
     * @param string $logsFile The logs file
     * @return Logger
     */
    public static function getInstance($logsFile)
    {
        return new self($logsFile);
    }

    /**
     * Remove the logs file.
     *
     * @return bool
     */
    public function removeLogsFile()
    {
        if (is_file($this->getLogsFile())) {
            return unlink($this->getLogsFile());
        }

        return true;
    }

    /**
     * Add a log to the logs file.
     *
     * @param mixed $content The log content
     * @return void
     */
    public function addLog($content, $toBreakLine = true)
    {
        if (is_object($content)) {
            $content = (array) $content;
        }

        if (is_array($content)) {
            $this->addLog('[');
            $this->addArrayToLogsFile($content);
            $this->addLog(']');
        } else {
            file_put_contents($this->getLogsFile(), $content . ($toBreakLine ? "\n" : null), FILE_APPEND);
        }
    }

    /**
     * Add an array to the logs file.
     *
     * @param array $array The array to add
     * @param int $tabLevel The tabulation level
     * @return void
     */
    private function addArrayToLogsFile(array $array, $tabLevel = 1)
    {
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $value = (array) $value;
            }

            if (is_array($value)) {
                $this->addLog($this->getTabLevel($tabLevel) . $key . ' => [');
                $this->addArrayToLogsFile($value, $tabLevel + 1);
                $this->addLog($this->getTabLevel($tabLevel) . ']');

                continue;
            }

            $this->addLog($this->getTabLevel($tabLevel) . $key . ' => ', false);
            $this->addLog($value);
        }
    }

    /**
     * Get the tabulation level.
     *
     * @param int $level The tabulation level
     * @return string|null The tabulation level
     */
    private function getTabLevel($level)
    {
        if (!$level) {
            return null;
        }

        $tab = null;

        for ($i = 0; $i < $level; ++$i) {
            $tab .= "\t";
        }

        return $tab;
    }

    /**
     * Get the logs' directory.
     *
     * @return string The logs' directory
     */
    public function getLogsDir()
    {
        return $this->logsDir;
    }

    /**
     * Set the logs' directory.
     *
     * @param string $logsDir The logs' directory
     * @return $this
     */
    private function setLogsDir($logsDir)
    {
        $this->logsDir = $logsDir;
        return $this;
    }

    /**
     * Get the logs file.
     *
     * @return string The logs file
     */
    public function getLogsFile()
    {
        return $this->logsFile;
    }

    /**
     * Set the logs file.
     *
     * @param string $logsFile The logs file
     * @return $this
     */
    private function setLogsFile($logsFile)
    {
        $this->logsFile = $logsFile;
        return $this;
    }
}
