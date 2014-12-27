<?php

use Latte\Compiler;
use Nedryse\Latte\Macros\TemplateMacroSet;

class TemplateMacroSetIntegrationTest extends PHPUnit_Framework_TestCase {

	public function testInstall() {
		$compiler = new Compiler;
		$templateMacroset = TemplateMacroSet::install($compiler);

		$compilerClass = new ReflectionClass('Latte\Compiler');
		$compilerProperty = $compilerClass->getProperty('macros');
		$compilerProperty->setAccessible(TRUE);

		$templateMacrosetClass = new ReflectionClass('Latte\Macros\MacroSet');
		$templateMacrosetProperty = $templateMacrosetClass->getProperty('macros');
		$templateMacrosetProperty->setAccessible(TRUE);

		$compilerMacros = $compilerProperty->getValue($compiler);
		$templateMacrosetMacros = $templateMacrosetProperty->getValue($templateMacroset);

		$this->assertArrayHasKey('template', $templateMacrosetMacros);
		$this->assertInstanceOf('Nedryse\Latte\Macros\TemplateMacroSet', $templateMacrosetMacros['template'][0][0]);
		$this->assertSame('macroTemplate', $templateMacrosetMacros['template'][0][1]);
		$this->assertNull($templateMacrosetMacros['template'][1]);
		$this->assertNull($templateMacrosetMacros['template'][2]);
		$this->assertArrayHasKey('_', $templateMacrosetMacros);
		$this->assertInstanceOf('Nedryse\Latte\Macros\TemplateMacroSet', $templateMacrosetMacros['_'][0][0]);
		$this->assertSame('macroTranslate', $templateMacrosetMacros['_'][0][1]);
		$this->assertInstanceOf('Nedryse\Latte\Macros\TemplateMacroSet', $templateMacrosetMacros['_'][1][0]);
		$this->assertSame('macroTranslate', $templateMacrosetMacros['_'][0][1]);
		$this->assertNull($templateMacrosetMacros['_'][2]);

		$this->assertArrayHasKey('template', $compilerMacros);
		$this->assertInstanceOf('Nedryse\Latte\Macros\TemplateMacroSet', $compilerMacros['template'][0]);
		$this->assertArrayHasKey('_', $compilerMacros);
		$this->assertInstanceOf('Nedryse\Latte\Macros\TemplateMacroSet', $compilerMacros['_'][0]);
	}

	public function templateDataProvider() {
		return array(
			/* {template "test"} */
			array(
				$latteTpl = '{template "test"}',
				$expected = 'test',
				$arguments = array(),
			),
			/* {template "test {:missed_placeholder}"} */
			array(
				$latteTpl = '{template "test {:missed_placeholder}"}',
				$expected = 'test ',
				$arguments = array(),
			),
			/* {template "test {:placeholder}"} */
			array(
				$latteTpl = '{template "test {:placeholder}"}',
				$expected = 'test replacement',
				$arguments = array('placeholder' => 'replacement'),
			),
			/* {template "test {:local_placeholder}"} */
			array(
				$latteTpl = '{var $local_placeholder = "replacement"}{template "test {:local_placeholder}"}',
				$expected = 'test replacement',
				$arguments = array(),
			),
			/* {template "test {:missed_placeholder} and {:placeholder}"} */
			array(
				$latteTpl = '{template "test {:missed_placeholder} and {:placeholder}"}',
				$expected = 'test  and replacement',
				$arguments = array('placeholder' => 'replacement'),
			),
			/* {template "test {:nested_placeholder}"} */
			array(
				$latteTpl = '{template "test {:nested_placeholder}"}',
				$expected = 'test replacement',
				$arguments = array('placeholder' => 'replacement', 'nested_placeholder' => '{:placeholder}'),
			),
			/* {template "test {:placeholder}", array("placeholder" => "replacement")} */
			array(
				$latteTpl = '{template "test {:placeholder}", array("placeholder" => "replacement")}',
				$expected = 'test replacement',
				$arguments = array(),
			),
			/* {template "test {:placeholder}", ["placeholder" => "replacement"]} */
			array(
				$latteTpl = '{template "test {:placeholder}", ["placeholder" => "replacement"]}',
				$expected = 'test replacement',
				$arguments = array(),
			),
			/* {template "test {:placeholder}", "placeholder" => "replacement"} */
			array(
				$latteTpl = '{template "test {:placeholder}", "placeholder" => "replacement"}',
				$expected = 'test replacement',
				$arguments = array(),
			),
			/* {template "test {:overwritten_placeholder}"} */
			array(
				$latteTpl = '{var $overwritten_placeholder = "replacement"}{template "test {:overwritten_placeholder}"}',
				$expected = 'test replacement',
				$arguments = array('localy_overwritten_placeholder' => 'original replacement'),
			),
			/* {template "test {:localy_overwritten_placeholder}"} */
			array(
				$latteTpl = '{template "test {:localy_overwritten_placeholder}", array("localy_overwritten_placeholder" => "replacement")}',
				$expected = 'test replacement',
				$arguments = array('localy_overwritten_placeholder' => 'original replacement'),
			),
			/* {template "test {:placeholder}"|upper} */
			array(
				$latteTpl = '{template "test {:placeholder}"|upper}',
				$expected = 'TEST REPLACEMENT',
				$arguments = array('placeholder' => 'replacement'),
			),
		);
	}

	/**
	 * @dataProvider templateDataProvider
	 */
	public function testTemplate($latteTpl, $expected, $arguments) {
		$latte = new Latte\Engine();
		$latte->onCompile[] = function($engine) {
			Nedryse\Latte\Macros\TemplateMacroSet::install($engine->getCompiler());
		};

		$name = tempnam(sys_get_temp_dir(), 'name');
		file_put_contents($name, $latteTpl);
		$this->assertSame($expected, $latte->renderToString($name, $arguments));
		unlink($name);
	}

	public function translateDataProvider() {
		return array(
			/* {_ "test"} */
			array(
				$translated = 'test',
				$latteTpl = '{_ "test"}',
				$expected = 'test',
				$arguments = array(),
			),
			/* {_ "test %s", "test"} */
			array(
				$translated = 'test test',
				$latteTpl = '{_ "test %s", "test"}',
				$expected = 'test test',
				$arguments = array(),
			),
			/* {_ "test {:placeholder}", array("placeholder" => "replacement")} */
			array(
				$translated = 'test {:placeholder}',
				$latteTpl = '{_ "test {:placeholder}", array("placeholder" => "replacement")}',
				$expected = 'test replacement',
				$arguments = array(),
			),
			/* {_ "test {:placeholder}"} */
			array(
				$translated = 'test {:placeholder}',
				$latteTpl = '{_ "test {:placeholder}"}',
				$expected = 'test replacement',
				$arguments = array("placeholder" => "replacement"),
			),
			/* {_ "test {:overwritten_placeholder}"} */
			array(
				$translated = 'test {:overwritten_placeholder}',
				$latteTpl = '{_ "test {:overwritten_placeholder}", array("overwritten_placeholder" => "replacement")}',
				$expected = 'test replacement',
				$arguments = array("overwritten_placeholder" => "overwritten_placeholder"),
			),
			/* {_ "test {:localy_overwritten_placeholder}"} */
			array(
				$translated = 'test {:localy_overwritten_placeholder}',
				$latteTpl = '{var $localy_overwritten_placeholder = "replacement"}{_ "test {:localy_overwritten_placeholder}"}',
				$expected = 'test replacement',
				$arguments = array("localy_overwritten_placeholder" => "localy_overwritten_placeholder"),
			),
			/* {_ "test {:placeholder}"|upper} */
			array(
				$translated = 'test {:placeholder}',
				$latteTpl = '{_ "test {:placeholder}"|upper}',
				$expected = 'TEST REPLACEMENT',
				$arguments = array("placeholder" => "replacement"),
			),
		);
	}

	/**
	 * @dataProvider translateDataProvider
	 */
	public function testTranslate($translated, $latteTpl, $expected, $arguments) {
		$translatorMock = $this->getMockForAbstractClass('Nette\Localization\ITranslator');
		$translatorMock->expects($this->any())
				->method('translate')
				->will($this->returnValue($translated));

		$latte = new Latte\Engine();
		$latte->addFilter('translate', array($translatorMock, 'translate'));
		$latte->onCompile[] = function($engine) {
			Nedryse\Latte\Macros\TemplateMacroSet::install($engine->getCompiler());
		};

		$name = tempnam(sys_get_temp_dir(), 'name');
		file_put_contents($name, $latteTpl);
		$this->assertSame($expected, $latte->renderToString($name, $arguments));
		unlink($name);
	}

}
