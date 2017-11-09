<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Test\Helper;

use PHPUnit\Framework\TestCase;
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
  public function testCssAppendClassSpecificSource1()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->cssAppendClassSpecificSource('SetBased\\Foo\\Bar');
    $CoreWebAssets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testCssAppendClassSpecificSource2()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->cssAppendClassSpecificSource('SetBased\\Foo\\Bar', 'printer');
    $CoreWebAssets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/SetBased/Foo/Bar.printer.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testCssAppendSource1()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->cssAppendSource('foo.css');
    $CoreWebAssets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testCssAppendSource2()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->cssAppendSource('foo.css', 'printer');
    $CoreWebAssets->echoCascadingStyleSheets();

    $this->expectOutputString('<link href="/css/foo.css" media="printer" rel="stylesheet" type="text/css"/>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle() with null.
   */
  public function testEchoPageTitle01()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle(null);
    $CoreWebAssets->echoPageTitle();

    $this->expectOutputString('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle() with empty string.
   */
  public function testEchoPageTitle02()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('');
    $CoreWebAssets->echoPageTitle();

    self::assertSame('', $this->getActualOutput());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method echoPageTitle() with some string.
   */
  public function testEchoPageTitle03()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('Hello World');
    $CoreWebAssets->echoPageTitle();

    $this->expectOutputString('<title>Hello World</title>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testJsAdmClassSpecificFunctionCall1()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->jsAdmClassSpecificFunctionCall('SetBased\\Foo\\Bar', 'main');
    $CoreWebAssets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\/Bar\\"],function(page){\'use strict\';page.main();});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testJsAdmClassSpecificFunctionCall2()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->jsAdmClassSpecificFunctionCall('SetBased\\Foo\\Bar', 'main', ['foo', 1]);
    $CoreWebAssets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\/Bar\\"],function(page){\'use strict\';page.main(\\"foo\\",1);});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testJsAdmFunctionCall1()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->jsAdmFunctionCall('SetBased/Foo', 'main');
    $CoreWebAssets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\\"],function(page){\'use strict\';page.main();});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testJsAdmFunctionCall2()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->jsAdmFunctionCall('SetBased/Foo', 'main', ['foo', false]);
    $CoreWebAssets->echoJavaScript();

    $this->expectOutputString('<script type="text/javascript">/*<![CDATA[*/set_based_abc_inline_js="require([],function(){require([\\"SetBased\/Foo\\"],function(page){\'use strict\';page.main(\\"foo\\",false);});});"/*]]>*/</script>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle() with null.
   */
  public function testSetPageTitle01()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle(null);

    self::assertSame('', $CoreWebAssets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle() with empty string.
   */
  public function testSetPageTitle02()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('');

    self::assertSame('', $CoreWebAssets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle() with non empty string.
   */
  public function testSetPageTitle03()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('Hello World');

    self::assertSame('Hello World', $CoreWebAssets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method setPageTitle() with non empty string overriding previous set title.
   */
  public function testSetPageTitle04()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('Hello World');
    $CoreWebAssets->setPageTitle('Bye Bye');

    self::assertSame('Bye Bye', $CoreWebAssets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle() with null.
   */
  public function testAppendPageTitle01()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('Hello');
    $CoreWebAssets->appendPageTitle(null);

    self::assertSame('Hello', $CoreWebAssets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle() with empty string.
   */
  public function testAppendPageTitle02()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('Hello');
    $CoreWebAssets->appendPageTitle('');

    self::assertSame('Hello', $CoreWebAssets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for method appendPageTitle() with non empty string.
   */
  public function testAppendPageTitle03()
  {
    $CoreWebAssets = new CoreWebAssets();

    $CoreWebAssets->setPageTitle('Hello');
    $CoreWebAssets->appendPageTitle('World');

    self::assertSame('Hello - World', $CoreWebAssets->getPageTitle());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
