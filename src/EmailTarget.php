<?php
namespace Sil\Log;

use yii\helpers\VarDumper;
use yii\log\EmailTarget as yEmailTarget;
use yii\log\Logger;

class EmailTarget extends yEmailTarget
{
    /**
     * Formats a log message for display as a string.
     * @param array $message the log message to be formatted.
     * The message structure follows that in [[Logger::messages]].
     * @return string the formatted message
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = sprintf('[%s] %s', $text->getCode(), $text->getMessage());
            } elseif (is_array($text)) {
                /*
                 * Only VarDump if array, don't want to inadvertently log objects with sensitive info
                 */
                $text = VarDumper::export($text);
            } else {
                $text = sprintf('Unsupported object of type %s sent to logger', get_class($text));
            }
        }

        $prefix = $this->getMessagePrefix($message);
        return date('Y-m-d H:i:s', $timestamp) . " {$prefix}[$level][$category] $text";
    }
}