<?php
declare(strict_types=1);

namespace Plaisio\WebAssets\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\Helper\Html;
use Plaisio\WebAssets\CoreWebAssets;

/**
 * Test cases for class CoreWebAssets.
 */
class CoreWebAssetsTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The kernel for testing.
   *
   * @var TestKernel
   */
  private static $kernel;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public static function setUpBeforeClass(): void
  {
    self::$kernel = new TestKernel();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle with null.
   */
  public function testAppendPageTitle01(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello');
    $assets->appendPageTitle(null);

    self::assertSame('Hello', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle with empty string.
   */
  public function testAppendPageTitle02(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello');
    $assets->appendPageTitle('');

    self::assertSame('Hello', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle with non empty string.
   */
  public function testAppendPageTitle03(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello');
    $assets->appendPageTitle('World');

    self::assertSame('Hello - World', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle with dash.
   */
  public function testAppendPageTitle04(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello, World!');
    $assets->appendPageTitle('-');

    self::assertSame('Hello, World!', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendLine.
   */
  public function testCssAppendLine(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendLine('body');
    $assets->cssAppendLine('{');
    $assets->cssAppendLine('color: red;');
    $assets->cssAppendLine('}');

    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<style media="all">body{color: red;}</style>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource.
   */
  public function testCssAppendSource1(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendSource('SetBased\\Foo\\Bar');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource with media.
   */
  public function testCssAppendSource2(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendSource('SetBased\\Foo\\Bar', 'printer');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.printer.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource.
   */
  public function testCssAppendSource3(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendSource('foo.css');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource with media.
   */
  public function testCssAppendSource4(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendSource('foo.css', 'printer');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource with missing CSS file.
   */
  public function testCssAppendSource5(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $assets->cssAppendSource('not-found.css');
    $assets->echoCascadingStyleSheets();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSourcesList.
   */
  public function testCssAppendSourcesList1(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendSource('foo.css', 'printer');
    $assets->cssAppendSourcesList('SetBased\\Foo\\Bar');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString(implode('', ['<link href="/css/foo.css" media="printer" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/SetBased/Foo/bar1.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/SetBased/Foo/bar2.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/SetBased/Foo/bar3.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo1.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo2.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo3.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/foo4.css" rel="stylesheet" type="text/css"/>']));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSourcesList with non-exiting list file.
   */
  public function testCssAppendSourcesList2(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $assets->cssAppendSourcesList(self::class);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSourcesList with non-exiting CSS file.
   */
  public function testCssAppendSourcesList3(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $this->expectExceptionMessageMatches('/incorrect-list.txt:7/');
    $assets->cssAppendSourcesList('/css/SetBased/Foo/incorrect-list.txt');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSourcesList with a missing header.
   */
  public function testCssAppenddSourcesList4(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $this->expectExceptionMessageMatches('/css/SetBased/Foo/missing-header.txt/');
    $this->expectExceptionMessageMatches('/# plaisio-css-list/');
    $assets->cssAppendSourcesList('/css/SetBased/Foo/missing-header.txt');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for combinations of methods cssAppendLine cssPushLine.
   */
  public function testCssPushAndAppendLine(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendLine('div.two{}');
    $assets->cssPushLine('div.one{}');
    $assets->cssAppendLine('div.three{}');

    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<style media="all">div.one{}div.two{}div.three{}</style>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for combinations of methods cssPushSource and cssAppendSource.
   */
  public function testCssPushAndAppendSource(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssPushSource('foo2.css');
    $assets->cssAppendSource('foo3.css');
    $assets->cssPushSource('foo1.css');

    $assets->echoCascadingStyleSheets();

    $this->expectOutputString(implode('', ['<link href="/css/foo1.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo2.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo3.css" rel="stylesheet" type="text/css"/>']));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushLine.
   */
  public function testCssPushLine(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssPushLine('body{color: blue;}');

    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<style media="all">body{color: blue;}</style>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSource.
   */
  public function testCssPushSource1(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssPushSource('SetBased\\Foo\\Bar');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSource with media.
   */
  public function testCssPushSource2(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssPushSource('SetBased\\Foo\\Bar', 'printer');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.printer.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSource.
   */
  public function testCssPushSource3(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssPushSource('foo.css');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSource with media.
   */
  public function testCssPushSource4(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssPushSource('foo.css', 'printer');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSource with missing CSS file.
   */
  public function testCssPushSource5(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $assets->cssPushSource('not-found.css');
    $assets->echoCascadingStyleSheets();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSourcesList.
   */
  public function testCssPushSourcesList1(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->cssAppendSource('foo.css', 'printer');
    $assets->cssPushSourcesList('SetBased\\Foo\\Bar');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString(implode('', ['<link href="/css/SetBased/Foo/bar1.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/SetBased/Foo/bar2.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/SetBased/Foo/bar3.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo1.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo2.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo3.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/foo4.css" rel="stylesheet" type="text/css"/>',
                                           '<link href="/css/foo.css" media="printer" rel="stylesheet" type="text/css"/>']));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSourcesList with non-exiting list file.
   */
  public function testCssPushSourcesList2(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $assets->cssPushSourcesList(self::class);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSourcesList with non-exiting CSS file.
   */
  public function testCssPushSourcesList3(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $this->expectExceptionMessageMatches('/incorrect-list.txt:13/');
    $assets->cssPushSourcesList('/css/SetBased/Foo/incorrect-list.txt');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssPushSourcesList with a missing header.
   */
  public function testCssPushSourcesList4(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $this->expectExceptionMessageMatches('/css/SetBased/Foo/missing-header.txt/');
    $this->expectExceptionMessageMatches('/# plaisio-css-list/');
    $assets->cssPushSourcesList('/css/SetBased/Foo/missing-header.txt');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle with null.
   */
  public function testEchoPageTitle01(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle(null);
    $assets->echoPageTitle();

    $this->expectOutputString('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle with empty string.
   */
  public function testEchoPageTitle02(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('');
    $assets->echoPageTitle();

    self::assertSame('', $this->getActualOutput());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle with some string.
   */
  public function testEchoPageTitle03(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello World');
    $assets->echoPageTitle();

    $this->expectOutputString('<title>Hello World</title>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall.
   */
  public function testJsAdmFunctionCall1(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->jsAdmFunctionCall('SetBased\\Foo\\Bar', 'main');
    $assets->echoJavaScript();

    $this->expectOutputString('<script>/*<![CDATA[*/php_plaisio_inline_js="require([],function(){require([\\"SetBased\/Foo\/Bar\\"],function(page){\'use strict\';page.main();});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall with arguments.
   */
  public function testJsAdmFunctionCall2(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->jsAdmFunctionCall('SetBased\\Foo\\Bar', 'main', ['foo', 1]);
    $assets->echoJavaScript();

    $this->expectOutputString('<script>/*<![CDATA[*/php_plaisio_inline_js="require([],function(){require([\\"SetBased\/Foo\/Bar\\"],function(page){\'use strict\';page.main(\\"foo\\",1);});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall.
   */
  public function testJsAdmFunctionCall3(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->jsAdmFunctionCall('SetBased/Foo', 'main');
    $assets->echoJavaScript();

    $this->expectOutputString('<script>/*<![CDATA[*/php_plaisio_inline_js="require([],function(){require([\\"SetBased\/Foo\\"],function(page){\'use strict\';page.main();});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall with arguments.
   */
  public function testJsAdmFunctionCall4(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->jsAdmFunctionCall('SetBased/Foo', 'main', ['foo', false]);
    $assets->echoJavaScript();

    $this->expectOutputString('<script>/*<![CDATA[*/php_plaisio_inline_js="require([],function(){require([\\"SetBased\/Foo\\"],function(page){\'use strict\';page.main(\\"foo\\",false);});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall with missing JS file.
   */
  public function testJsAdmFunctionCall5(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $assets->jsAdmFunctionCall('SetBased/Foo/Bax', 'main', ['foo', false]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmOptimizedSetMain.
   */
  public function testJsAdmOptimizedSetMain(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->jsAdmOptimizedSetMain("/js/SetBased/Foo/Bar.main.js");
    $assets->echoJavaScript();

    $this->expectOutputString('<script src="/js/SetBased/Foo/Bar.main.js"></script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmSetPageSpecificMain.
   */
  public function testJsAdmSetPageSpecificMain1(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->jsAdmSetPageSpecificMain('SetBased\\Foo\\Bar');
    $assets->echoJavaScript();

    $this->expectOutputString('<script src="/js/require.js" data-main="/js/SetBased/Foo/Bar.main.js"></script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmSetPageSpecificMain with missing JS file.
   */
  public function testJsAdmSetPageSpecificMain2(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $this->expectException(\LogicException::class);
    $assets->jsAdmSetPageSpecificMain('SetBased\\Foo\\Bax');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method metaAddElement.
   */
  public function testMetaAddElement(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->metaAddElement(['foo' => 'bar']);
    Html::$encoding = '';
    $assets->echoMetaTags();
    Html::$encoding = 'UTF-8';

    $this->expectOutputString('<meta foo="bar"/><meta/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method metaAddKeyword.
   */
  public function testMetaAddKeyword(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->metaAddKeyword('foo');
    $assets->metaAddKeyword('bar');
    Html::$encoding = '';
    $assets->echoMetaTags();
    Html::$encoding = 'UTF-8';

    $this->expectOutputString('<meta name="keywords" content="foo,bar"/><meta/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method metaAddKeywords.
   */
  public function testMetaAddKeywords(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->metaAddKeywords(['foo', 'bar']);
    Html::$encoding = '';
    $assets->echoMetaTags();
    Html::$encoding = 'UTF-8';

    $this->expectOutputString('<meta name="keywords" content="foo,bar"/><meta/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method pushPageTitle with null.
   */
  public function testPushPageTitle01(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello');
    $assets->pushPageTitle(null);

    self::assertSame('Hello', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method pushPageTitle with empty string.
   */
  public function testPushPageTitle02(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello');
    $assets->pushPageTitle('');

    self::assertSame('Hello', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method pushPageTitle with non empty string.
   */
  public function testPushPageTitle03(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->pushPageTitle('Hello');

    self::assertSame('Hello', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method pushPageTitle with non empty string.
   */
  public function testPushPageTitle04(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('World');
    $assets->pushPageTitle('Hello');

    self::assertSame('Hello - World', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method pushPageTitle with dash.
   */
  public function testPushPageTitle05(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello, World!');
    $assets->pushPageTitle('-');

    self::assertSame('Hello, World!', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with null.
   */
  public function testSetPageTitle01(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle(null);

    self::assertSame('', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with empty string.
   */
  public function testSetPageTitle02(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('');

    self::assertSame('', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with non empty string.
   */
  public function testSetPageTitle03(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello World');

    self::assertSame('Hello World', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with non empty string overriding previous set title.
   */
  public function testSetPageTitle04(): void
  {
    $assets = new CoreWebAssets(self::$kernel);

    $assets->setPageTitle('Hello, world');
    $assets->setPageTitle('Bye Bye');

    self::assertSame('Bye Bye', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
