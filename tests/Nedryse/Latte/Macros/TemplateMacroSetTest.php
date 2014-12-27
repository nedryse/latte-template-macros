<?php

use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;
use Nedryse\Latte\Macros\TemplateMacroSet;

class TemplateMacroSetTest extends PHPUnit_Framework_TestCase {

	public function testInstall() {
		$compilerMock = $this->getMock('Latte\Compiler');
		$this->assertInstanceOf('Nedryse\Latte\Macros\TemplateMacroSet', TemplateMacroSet::install($compilerMock));

		$this->markTestSkipped('No way to test static method Nedryse\Latte\Macros\TemplateMacroSet::install');
	}

	public function testMacroTemplate() {
		$returnValue = '<?php echo Latte\Runtime\Filters::escapeHtml(Nedryse\Latte\Macros\TemplateMacroSet::template(call_user_func_array(array($template, \'translate\'), array_merge(array(\'Pokus {:blah} a {:test}\'), $_args = ((is_array(current($_args = array())) === TRUE) ? current($_args) : $_args))), array_merge($template->getParameters(), get_defined_vars(), $_args)), ENT_NOQUOTES) ?>';

		/* @var $macroNodeMock MacroNode */
		$macroNodeMock = $this->getMockBuilder('Latte\MacroNode')
				->disableOriginalConstructor()
				->getMock();

		/* @var $phpWriterMock PhpWriter */
		$phpWriterMock = $this->getMockBuilder('Latte\PhpWriter')
				->disableOriginalConstructor()
				->setMethods(array('write'))
				->getMock();

		$phpWriterMock->expects($this->once())
				->method('write')
				->with($this->equalTo('echo %modify(Nedryse\Latte\Macros\TemplateMacroSet::template(%node.word, array_merge($template->getParameters(), get_defined_vars(),((count($_args = %node.array) === 1) && (is_array(current($_args)) === TRUE) ? current($_args) : $_args))))'))
				->will($this->returnValue($returnValue));

		/* @var $templateMacroSetMock TemplateMacroSet */
		$templateMacroSetMock = $this->getMockBuilder('Nedryse\Latte\Macros\TemplateMacroSet')
				->disableOriginalConstructor()
				->setMethods(NULL)
				->getMock();
		$this->assertSame($returnValue, $templateMacroSetMock->macroTemplate($macroNodeMock, $phpWriterMock));
	}

	public function testMacroTranslatePairStart() {
		/* @var $macroNodeMock MacroNode */
		$macroNodeMock = $this->getMockBuilder('Latte\MacroNode')
				->disableOriginalConstructor()
				->getMock();
		$macroNodeMock->closing = FALSE;
		$macroNodeMock->args = '';

		/* @var $phpWriterMock PhpWriter */
		$phpWriterMock = $this->getMockBuilder('Latte\PhpWriter')
				->disableOriginalConstructor()
				->getMock();

		/* @var $compilerMock Compiler */
		$compilerMock = $this->getMock('Latte\Compiler');

		/* @var $templateMacroSetMock TemplateMacroSet */
		$templateMacroSet = new TemplateMacroSet($compilerMock);

		$this->assertSame('ob_start()', $templateMacroSet->macroTranslate($macroNodeMock, $phpWriterMock));
	}

	public function testMacroTranslatePairEnd() {
		$returnValue = '<?php echo Latte\Runtime\Filters::escapeHtml($template->translate(ob_get_clean()), ENT_NOQUOTES) ?>';

		/* @var $macroNodeMock MacroNode */
		$macroNodeMock = $this->getMockBuilder('Latte\MacroNode')
				->disableOriginalConstructor()
				->getMock();
		$macroNodeMock->closing = TRUE;

		/* @var $phpWriterMock PhpWriter */
		$phpWriterMock = $this->getMockBuilder('Latte\PhpWriter')
				->disableOriginalConstructor()
				->getMock();
		$phpWriterMock->expects($this->once())
				->method('write')
				->with($this->equalTo('echo %modify($template->translate(ob_get_clean()))'))
				->will($this->returnValue($returnValue));

		/* @var $compilerMock Compiler */
		$compilerMock = $this->getMock('Latte\Compiler');

		/* @var $templateMacroSetMock TemplateMacroSet */
		$templateMacroSet = new TemplateMacroSet($compilerMock);

		$this->assertSame($returnValue, $templateMacroSet->macroTranslate($macroNodeMock, $phpWriterMock));
	}

	public function testMacroTranslateNotPair() {
		$returnValue = '<?php echo Latte\Runtime\Filters::escapeHtml(Nedryse\Latte\Macros\TemplateMacroSet::template(call_user_func_array(array($template, \'translate\'), array_merge(array(%node.word), $_args = ((is_array(current($_args = array(\'notempty\'))) === TRUE) ? current($_args) : $_args))), array_merge($template->getParameters(), get_defined_vars(), $_args)), ENT_NOQUOTES) ?>';

		/* @var $macroNodeMock MacroNode */
		$macroNodeMock = $this->getMockBuilder('Latte\MacroNode')
				->disableOriginalConstructor()
				->getMock();
		$macroNodeMock->closing = FALSE;
		$macroNodeMock->args = 'notempty';

		/* @var $phpWriterMock PhpWriter */
		$phpWriterMock = $this->getMockBuilder('Latte\PhpWriter')
				->disableOriginalConstructor()
				->getMock();
		$phpWriterMock->expects($this->once())
				->method('write')
				->with($this->equalTo('echo %modify(Nedryse\Latte\Macros\TemplateMacroSet::template(call_user_func_array(array($template, \'translate\'), array_merge(array(%node.word), $_args = ((is_array(current($_args = %node.array)) === TRUE) ? current($_args) : $_args))), array_merge($template->getParameters(), get_defined_vars(), $_args)))'))
				->will($this->returnValue($returnValue));

		/* @var $compilerMock Compiler */
		$compilerMock = $this->getMock('Latte\Compiler');

		/* @var $templateMacroSetMock TemplateMacroSet */
		$templateMacroSet = new TemplateMacroSet($compilerMock);

		$this->assertSame($returnValue, $templateMacroSet->macroTranslate($macroNodeMock, $phpWriterMock));
	}

	public function testTemplate() {
		$this->markTestSkipped('No way to test static method Nedryse\Latte\Macros\TemplateMacroSet::template');
	}

}
