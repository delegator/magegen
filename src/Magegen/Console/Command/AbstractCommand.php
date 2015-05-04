<?php

namespace Delegator\Magegen\Console\Command;

use Symfony\Component\Console\Command\Command;

class AbstractCommand extends Command
{
    protected $_hookName = null;

    public function stepHook($stage)
    {
        if ($this->_hookName !== null) {
            $directory = "magegen/" . $stage . "_" . $this->_hookName;
            if (is_dir($directory)) {
                $dh = opendir($directory);

                while (false !== ($file = readdir($dh))) {
                    if (is_file($directory . '/' . $file)) {
                        include_once($directory . '/' . $file);
                    }
                }
            }
        }
    }
}
