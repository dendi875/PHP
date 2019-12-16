<?php

namespace Lego\Scripts;

use Composer\Script\Event;

class ComposerScripts
{
	 /**
     * 执行 `dump-autoload` 命令后的处理程序
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
	public static function postAutoloadDump(Event $event)
	{
		require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

		\FileSystem::createDir('tmpdir');
	}
}
