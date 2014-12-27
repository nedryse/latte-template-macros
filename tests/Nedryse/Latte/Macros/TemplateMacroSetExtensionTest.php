<?php

use Nedryse\Latte\Macros\TemplateMacroSetExtension;
use Nette\DI\ContainerBuilder;

class TemplateMacroSetExtensionTest extends PHPUnit_Framework_TestCase {

	public function testLoadConfiguration() {
		/* @var $containerBuilderMock ContainerBuilder */
		$containerBuilderMock = $this->getMockBuilder('Nette\DI\ContainerBuilder')
				->setMethods(array('getDefinition', 'addSetup'))
				->getMock();
		$containerBuilderMock->expects($this->once())
				->method('getDefinition')
				->with($this->equalTo('nette.latteFactory'))
				->will($this->returnSelf());
		$containerBuilderMock->expects($this->once())
				->method('addSetup')
				->with($this->equalTo('?->onCompile[] = function($engine) { Nedryse\Latte\Macros\TemplateMacroSet::install($engine->getCompiler()); }'), $this->equalTo(array('@self')));

		/* @var $templateMacroSetExtension TemplateMacroSetExtension */
		$templateMacroSetExtension = $this->getMockBuilder('Nedryse\Latte\Macros\TemplateMacroSetExtension')
				->setMethods(array('getContainerBuilder'))
				->getMock();
		$templateMacroSetExtension->expects($this->once())
				->method('getContainerBuilder')
				->will($this->returnValue($containerBuilderMock));

		$this->assertNull($templateMacroSetExtension->loadConfiguration());
	}

}
