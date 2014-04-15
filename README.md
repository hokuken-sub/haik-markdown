haik Markdown
==============

PHP haik Markdown parser based on PHP Markdown Extra.

This markdown parser has pluggable interface.


by Hokuken
[http://www.hokuken.com] (http://www.hokuken.com/ "Hokuken Inc.")

based on [PHP Markdown] (https://github.com/michelf/php-markdown "michelf/php-markdown")



Requirement
-------------

This library package requires PHP 5.3 or later.


About plugin
--------------

Markdown text can include special syntax for haik-markdown plugins.
These syntaxes are **inline** and **convert** .

### Inline plugin

In below example, See `&deco(red){...};` and `&br;`,
they parse to `<span style="color:red">...</span>` and `<br>\n` .

    Lorem ipsum dolor sit amet, &deco(red){consectetur}; adipisicing elit,
    sed do eiusmod tempor incididunt ut labore et dolore magna
    aliqua.&br; Ut enim ad minim veniam, quis nostrud exercitation
    ullamco laboris nisi ut aliquip ex ea commodo consequat.


Usage
-------

### With basic plugins

    // Preparation to use
    use \Toiee\HaikMarkdown;
    $parser = new HaikMarkdown();
    $plugin_repository = new Toiee\HaikMarkdown\Plugin\Repositories\BasicPluginRepository($parser);
    $parser->registerPluginRepository($plugin_repository);
    
    // Parsing markdown text
    $html = $parser->transform($markdown_text);


### With Twitter Bootstrap plugins


    // Preparation to use
    use \Toiee\HaikMarkdown;
    $parser = new HaikMarkdown();
    $plugin_repository = new Toiee\HaikMarkdown\Plugin\Bootstrap\PluginRepository($parser);
    $parser->registerPluginRepository($plugin_repository);
    
    // Parsing markdown text
    $html = $parser->transform($markdown_text);

