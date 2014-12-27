<?php

namespace Nedryse\Latte\Macros;

use Nette\DI\CompilerExtension;

/**
 * TemplateMacroSetExtension simplify service loading into the config.neon
 */
class TemplateMacroSetExtension extends CompilerExtension {

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$builder->getDefinition('nette.latteFactory')
				->addSetup('?->onCompile[] = function($engine) { Nedryse\Latte\Macros\TemplateMacroSet::install($engine->getCompiler()); }', array('@self'));
	}

}
