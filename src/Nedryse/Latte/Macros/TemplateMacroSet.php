<?php

namespace Nedryse\Latte\Macros;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

class TemplateMacroSet extends MacroSet {

	protected static $regexp = '~{:(\\w+)}~';

	/**
	 * Register template macro into the latte compiler
	 *
	 * @param Compiler $compiler
	 * @return TemplateMacroSet Provides fluent interface
	 */
	public static function install(Compiler $compiler) {
		$me = new static($compiler);
		$me->addMacro('template', array($me, 'macroTemplate'));
		$me->addMacro('_', array($me, 'macroTranslate'), array($me, 'macroTranslate'));
		return $me;
	}

	/**
	 * Replace latte macro with propper method calling
	 * {template 'string with {:placeholder}', ['placeholder' => 'replacement']|modifiers}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return string
	 */
	public function macroTemplate(MacroNode $node, PhpWriter $writer) {
		return $writer->write('echo %modify(Nedryse\Latte\Macros\TemplateMacroSet::template(%node.word, array_merge($template->getParameters(), get_defined_vars(),((count($_args = %node.array) === 1) && (is_array(current($_args)) === TRUE) ? current($_args) : $_args))))');
	}

	/**
	 * Add support of placeholder replacement into the standart translator macro
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return strings
	 */
	public function macroTranslate(MacroNode $node, PhpWriter $writer) {
		if ($node->closing) {
			return $writer->write('echo %modify($template->translate(ob_get_clean()))');
		} elseif ($node->isEmpty = ($node->args !== '')) {
			return $writer->write('echo %modify(Nedryse\Latte\Macros\TemplateMacroSet::template(call_user_func_array(array($template, \'translate\'), array_merge(array(%node.word), $_args = ((is_array(current($_args = %node.array)) === TRUE) ? current($_args) : $_args))), array_merge($template->getParameters(), get_defined_vars(), $_args)))');
		} else {
			return 'ob_start()';
		}
	}

	/**
	 * Recursively replace {:placeholder} in template
	 * with the content of same-named variable in arguments
	 * Missed placeholders are replaced by NULL
	 *
	 * @param string $template text with {:placeholders}
	 * @param array $arguments
	 * @return void
	 */
	public static function template($template, $arguments = array()) {
		return preg_replace_callback(self::$regexp, function($matches) use($arguments) {
			$replacement = isset($arguments[$matches[1]]) ? $arguments[$matches[1]] : NULL;
			return TemplateMacroSet::template($replacement, $arguments);
		}, $template);
	}

}
