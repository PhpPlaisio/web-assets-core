<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\WebAssets\Test;

use PHPUnit\Framework\TestCase;
use SetBased\Abc\Helper\Html;
use SetBased\Abc\WebAssets\CoreWebAssets;

/**
 * Test cases for class CoreWebAssets.
 */
class CoreWebAssetsTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public static function setUpBeforeClass()
  {
    CoreWebAssets::$assetDir           = __DIR__;
    CoreWebAssets::$cssRootRelativeUrl = '/css/';
    CoreWebAssets::$jsRootRelativeUrl  = '/js/';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle with null.
   */
  public function testAppendPageTitle01()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('Hello');
    $assets->appendPageTitle(null);

    self::assertSame('Hello', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle with empty string.
   */
  public function testAppendPageTitle02()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('Hello');
    $assets->appendPageTitle('');

    self::assertSame('Hello', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle with non empty string.
   */
  public function testAppendPageTitle03()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('Hello');
    $assets->appendPageTitle('World');

    self::assertSame('Hello - World', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendClassSpecificSource.
   */
  public function testCssAppendClassSpecificSource1()
  {
    $assets = new CoreWebAssets();

    $assets->cssAppendClassSpecificSource('SetBased\\Foo\\Bar');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendClassSpecificSource with media.
   */
  public function testCssAppendClassSpecificSource2()
  {
    $assets = new CoreWebAssets();

    $assets->cssAppendClassSpecificSource('SetBased\\Foo\\Bar', 'printer');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.printer.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendLine with null.
   */
  public function testCssAppendLine()
  {
    $assets = new CoreWebAssets();

    $assets->cssAppendLine('body');
    $assets->cssAppendLine('{');
    $assets->cssAppendLine('color: red;');
    $assets->cssAppendLine('}');

    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<style type="text/css" media="all">body{color: red;}</style>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource.
   */
  public function testCssAppendSource1()
  {
    $assets = new CoreWebAssets();

    $assets->cssAppendSource('foo.css');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource with media.
   */
  public function testCssAppendSource2()
  {
    $assets = new CoreWebAssets();

    $assets->cssAppendSource('foo.css', 'printer');
    $assets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method cssAppendSource with missing CSS file.
   *
   * @expectedException \LogicException
   */
  public function testCssAppendSource3()
  {
    $assets = new CoreWebAssets();

    $assets->cssAppendSource('not-found.css');
    $assets->echoCascadingStyleSheets();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle with null.
   */
  public function testEchoPageTitle01()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle(null);
    $assets->echoPageTitle();

    $this->expectOutputString('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle with empty string.
   */
  public function testEchoPageTitle02()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('');
    $assets->echoPageTitle();

    self::assertSame('', $this->getActualOutput());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle with some string.
   */
  public function testEchoPageTitle03()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('Hello World');
    $assets->echoPageTitle();

    $this->expectOutputString('<title>Hello World</title>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmClassSpecificFunctionCall.
   */
  public function testJsAdmClassSpecificFunctionCall1()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmClassSpecificFunctionCall('SetBased\\Foo\\Bar', 'main');
    $assets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\/Bar\\"],function(page){\'use strict\';page.main();});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmClassSpecificFunctionCall with arguments.
   */
  public function testJsAdmClassSpecificFunctionCall2()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmClassSpecificFunctionCall('SetBased\\Foo\\Bar', 'main', ['foo', 1]);
    $assets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\/Bar\\"],function(page){\'use strict\';page.main(\\"foo\\",1);});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall.
   */
  public function testJsAdmFunctionCall1()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmFunctionCall('SetBased/Foo', 'main');
    $assets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\\"],function(page){\'use strict\';page.main();});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall with arguments.
   */
  public function testJsAdmFunctionCall2()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmFunctionCall('SetBased/Foo', 'main', ['foo', false]);
    $assets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\\"],function(page){\'use strict\';page.main(\\"foo\\",false);});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmFunctionCall with missing JS file.
   *
   * @expectedException \LogicException
   */
  public function testJsAdmFunctionCall3()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmFunctionCall('SetBased/Foo/Bax', 'main', ['foo', false]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmOptimizedSetPageSpecificMain.
   */
  public function testJsAdmOptimizedSetPageSpecificMain()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmOptimizedSetPageSpecificMain("/js/SetBased/Foo/Bar.main.js");
    $assets->echoJavaScript();

    $this->expectOutputString('<script src="/js/SetBased/Foo/Bar.main.js"></script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmSetPageSpecificMain.
   */
  public function testJsAdmSetPageSpecificMain1()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmSetPageSpecificMain('SetBased\\Foo\\Bar');
    $assets->echoJavaScript();

    $this->expectOutputString('<script src="/js/require.js" data-main="/js/SetBased/Foo/Bar.main.js"></script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method jsAdmSetPageSpecificMain with missing JS file.
   *
   * @expectedException \LogicException
   */
  public function testJsAdmSetPageSpecificMain2()
  {
    $assets = new CoreWebAssets();

    $assets->jsAdmSetPageSpecificMain('SetBased\\Foo\\Bax');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method metaAddElement.
   */
  public function testMetaAddElement()
  {
    $assets = new CoreWebAssets();

    $assets->metaAddElement(['foo' => 'bar']);
    Html::$encoding = null;
    $assets->echoMetaTags();
    Html::$encoding = 'UTF-8';

    $this->expectOutputString('<meta foo="bar"/><meta/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method metaAddKeyword.
   */
  public function testMetaAddKeyword()
  {
    $assets = new CoreWebAssets();

    $assets->metaAddKeyword('foo');
    $assets->metaAddKeyword('bar');
    Html::$encoding = null;
    $assets->echoMetaTags();
    Html::$encoding = 'UTF-8';

    $this->expectOutputString('<meta name="keywords" content="foo,bar"/><meta/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method metaAddKeywords.
   */
  public function testMetaAddKeywords()
  {
    $assets = new CoreWebAssets();

    $assets->metaAddKeywords(['foo', 'bar']);
    Html::$encoding = null;
    $assets->echoMetaTags();
    Html::$encoding = 'UTF-8';

    $this->expectOutputString('<meta name="keywords" content="foo,bar"/><meta/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with null.
   */
  public function testSetPageTitle01()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle(null);

    self::assertSame('', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with empty string.
   */
  public function testSetPageTitle02()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('');

    self::assertSame('', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with non empty string.
   */
  public function testSetPageTitle03()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('Hello World');

    self::assertSame('Hello World', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle with non empty string overriding previous set title.
   */
  public function testSetPageTitle04()
  {
    $assets = new CoreWebAssets();

    $assets->setPageTitle('Hello World');
    $assets->setPageTitle('Bye Bye');

    self::assertSame('Bye Bye', $assets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
