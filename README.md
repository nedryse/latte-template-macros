#nedryse/latte-template-macroset (cc)#
Pavel Železný (2bfree), 2014 ([pavelzelezny.cz](http://pavelzelezny.cz))

## Requirements ##

[Nette Framework 2.2.0](http://nette.org) or higher

## Documentation ##

Placeholder replacement [Latte macro](http://doc.nette.org/en/2.2/default-macros) provides simple way to put the content of variables into the right places.

Macroset provides `{template ...}` macro and overwrites `{_ ...}` macro with same mechanism.
First argument of these macros can contain any number of `{:placeholders}` in the string that will be replaced by the same named variables.

Replacement variables are taken from the template like `$template->placeholder`, are overwritten by local variable like `{var $placeholder}` and are overwritten by arguments of macros like `{template ..., 'placeholder' => 'replacement', ...}` or `{template ..., array('placeholder' => 'replacement', ...)}`.
Replacement variables can contain `{:nested_placeholders}`, that will be replaced also.

The functionality of `{_ ...}` macro is maintained. All arguments of the macro are sended to the translator unchanged. Result of the translator will be used as template that can contain `{:placeholders}`. With this functionality you can change the possition of given arguments to provide propper number of translator plurals as the first argument of the macro. 

## Instalation ##

Prefered way to intall is by [Composer](http://getcomposer.org)

	composer require nedryse/latte-template-macroset:~1.0.0

Or by manualy adding into the [composer.json](https://getcomposer.org/doc/04-schema.md#json-schema)

	{
		"require":{
			"nedryse/latte-template-macroset": "~1.0.0"
		}
	}

## Setup ##

Add following code into the [config.neon](http://doc.nette.org/en/2.2/configuring#toc-framework-configuration)

	common:
		extensions:
			latteTemplateMacroSet: Nedryse\Latte\Macros\TemplateMacroSetExtension

## Usage ##

The simpliest usage is like the following code:

	{template '<strong>{:userName}</strong>', 'userName' => $user->getIdentity()->getId()}

Replacement variables can be given by the array also like in the following code:

	{var $replacements = array('userName' => $user->getIdentity()->getId())}
	{template '<strong>{:userName}</strong>', $replacements}

Replacement variables can be given from `{var ...}` or from `$template` like in the following code:

	{var $userName => $user->getIdentity()->getId()}
	{template '<strong>{:userName}</strong>'}

Replacements can be overwritten in the order `$template`, `{var ...}`, `{template ..., replacements}` like in the following code:

	{var $userName => $user->getIdentity()->getId()}
	{template '<strong>{:userName}</strong>', 'userName' => 'test'}

Both macros also support [filters](http://doc.nette.org/en/2.2/default-filters) like in the following code:

	{template '<strong>{:userName}</strong>', 'userName' => 'test'|upper}

Replacements can contain `{:nested_placeholders}` to be used as customizable template mechanism like in the following code:

	{var $idTpl = '<strong>{:userId}</strong>'}
	{var $nameTpl = '{:userName}'}
	{var $pairTpl = '<li>{:idTpl} - {:nameTpl}</li>'}
	{var $usersTpl = '<ul>{:usersListTpl}</ul>'}
	{capture $usersListTpl}
		{foreach $users as $userId => $userName}
			{template $pairTpl}
		{/foreach}
	{/capture}

	{_'User (%s): {:usersTpl}', count($users)}