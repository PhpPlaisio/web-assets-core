<?php
declare(strict_types=1);

namespace Plaisio\WebAssets;

use Plaisio\Helper\Html;
use Plaisio\Helper\Url;
use Plaisio\PlaisioObject;
use SetBased\Exception\LogicException;
use Webmozart\PathUtil\Path;

/**
 * Helper class for setting web assets (things like CSS, JavaScript and image files) and generating HTML code for
 * including web assets.
 */
class CoreWebAssets extends PlaisioObject implements WebAssets
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The root-relative URL for storing CSS files. Note: Must have a leading and a trailing slash.
   *
   * @var string
   */
  public static $cssRootRelativeUrl = '/css/';

  /**
   * The root-relative URL for storing JavaScript files. Note: Must have a leading and a trailing slash.
   *
   * @var string
   */
  public static $jsRootRelativeUrl = '/js/';

  /**
   * The separator between parts of the page title.
   *
   * @var string
   */
  public static $separator = ' - ';

  /**
   * CSS code to be included on the page.
   *
   * @var string[]
   */
  protected $css = [];

  /**
   * List with CSS sources to be included on the page.
   *
   * @var array[]
   */
  protected $cssSources = [];

  /**
   * JavaScript code to be included on the page.
   *
   * @var string
   */
  protected $javaScript;

  /**
   * The attributes of the script element in the page trailer (i.e. near the end html tag). Example:
   * ```
   * [ 'src' => '/js/requirejs.js', 'data-main' => '/js/main.js' ]
   * ```
   *
   * @var array
   */
  protected $jsTrailerAttributes;

  /**
   * The keywords to be included in a meta tag for this page.
   *
   * var string[]
   */
  protected $keywords = [];

  /**
   * The attributes of the meta elements of the page.
   *
   * @var array[]
   */
  protected $metaAttributes = [];

  /**
   * The title of the page.
   *
   * @var string
   */
  protected $title = '';

  //--------------------------------------------------------------------------------------------------------------------

  /**
   * Appends with a separator a string to the page title.
   *
   * @param string|null $postfix The string to eb append to the page title.
   *
   * @see echoPageTitle()
   * @see pushPageTitle()
   * @see setPageTitle()
   * @see setPageTitle()
   *
   * @api
   * @since 1.0.0
   */
  public function appendPageTitle(?string $postfix): void
  {
    if ($postfix===null || $postfix==='' || $postfix==='-') return;

    if ($this->title!=='')
    {
      $this->title .= self::$separator;
    }

    $this->title .= $postfix;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a line with a CSS snippet to the internal CSS.
   *
   * @param string|null $cssLine The line with CSS snippet.
   *
   * @api
   * @since 1.0.0
   */
  public function cssAppendLine(?string $cssLine): void
  {
    $this->css[] = $cssLine;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a CCS file to the header to the page.
   *
   * @param string      $location The location to the CSS source. One of:
   *                              <ul>
   *                              <li> A relative of absolute URL.
   *                              <li> The __CLASS__ or __TRAIT__ magical constant.
   *                              <li> Name of a class with specified by the ::class resolution operator.
   *                              </ul>
   *                              When a class name is given, backslashes will be translated to forward slashes and
   *                              extension .css will be added to construct the filename relative to the resource root
   *                              of the CSS source.
   * @param string|null $media    The media for which the CSS source is optimized for. Note: use null for 'all'
   *                              devices;
   *                              null is preferred over 'all'.
   *
   * @api
   * @since 1.0.0
   */
  public function cssAppendSource(string $location, ?string $media = null): void
  {
    $uri = $this->cssResolveLocation($location, $media, '.css');
    $this->assertUriExists($uri);
    $this->cssOptimizedAppendSource($uri, $media);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a list of CCS files at the end of the list of CSS files in the header of the page.
   *
   * @param string      $location The location to the CSS source. One of:
   *                              <ul>
   *                              <li> A relative URL.
   *                              <li> The __CLASS__ or __TRAIT__ magical constant.
   *                              <li> Name of a class specified by the ::class resolution operator.
   *                              </ul>
   *                              When a class name is given, backslashes will be translated to forward slashes and
   *                              extension .txt will be added to construct the filename relative to the resource root.
   * @param string|null $media    The media for which the CSS source is optimized for. Note: use null for 'all'
   *                              devices; null is preferred over 'all'.
   *
   * @api
   * @since 1.0.0
   */
  public function cssAppendSourcesList(string $location, ?string $media = null): void
  {
    [$path, $lines] = $this->cssReadListLocation($location, $media);
    foreach ($lines as $i => $line)
    {
      $line = trim($line);
      if ($line!=='' && $line[0]!=='#')
      {
        $uri = $this->cssResolveListItem($line, $path, $i + 1);
        $this->cssOptimizedAppendSource($uri, $media);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds an optimized CCS file to the page.
   *
   * Do not use this method directly. Use {@link cssAppendPageSpecificSource} instead.
   *
   * @param string      $url   The URL to the CSS source.
   * @param string|null $media The media for which the CSS source is optimized for. Note: use null for 'all'
   *                           devices; null is preferred over 'all'.
   */
  public function cssOptimizedAppendSource(string $url, ?string $media = null): void
  {
    $this->cssSources[] = ['href'  => $url,
                           'media' => $media,
                           'rel'   => 'stylesheet',
                           'type'  => 'text/css'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Pushes an optimized CCS file at the beginning of the list of CSS files in the header of the page.
   *
   * Do not use this method directly. Use {@link cssPushSource} instead.
   *
   * @param string      $url   The URL to the CSS source.
   * @param string|null $media The media for which the CSS source is optimized for. Note: use null for 'all'
   *                           devices; null is preferred over 'all'.
   */
  public function cssOptimizedPushSource(string $url, ?string $media = null): void
  {
    array_unshift($this->cssSources, ['href'  => $url,
                                      'media' => $media,
                                      'rel'   => 'stylesheet',
                                      'type'  => 'text/css']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Pushes a line with a CSS snippet at the beginning of the internal CSS.
   *
   * @param string|null $cssLine The line with CSS snippet.
   *
   * @api
   * @since 2.0.0
   */
  public function cssPushLine(?string $cssLine): void
  {
    array_unshift($this->css, $cssLine);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Pushes a CCS file at the beginning of the list of CSS files in the header of the page.
   *
   * @param string      $location The location to the CSS source. One of:
   *                              <ul>
   *                              <li> A relative of absolute URL.
   *                              <li> The __CLASS__ or __TRAIT__ magical constant.
   *                              <li> Name of a class with specified by the ::class resolution operator.
   *                              </ul>
   *                              When a class name is given, backslashes will be translated to forward slashes and
   *                              extension .css will be added to construct the filename relative to the resource
   *                              root.
   * @param string|null $media    The media for which the CSS source is optimized for. Note: use null for 'all'
   *                              devices;
   *                              null is preferred over 'all'.
   *
   * @api
   * @since 2.0.0
   */
  public function cssPushSource(string $location, ?string $media = null): void
  {
    $uri = $this->cssResolveLocation($location, $media, '.css');
    $this->assertUriExists($uri);
    $this->cssOptimizedPushSource($uri, $media);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Pushes a CCS list of files at the beginning of the list of CSS files in the header of the page.
   *
   * @param string      $location The location to the CSS source. One of:
   *                              <ul>
   *                              <li> A filename relative to the resource root with extension .txt.
   *                              <li> The __CLASS__ or __TRAIT__ magical constant.
   *                              <li> Name of a class with specified by the ::class resolution operator.
   *                              </ul>
   *                              When a class name is given, backslashes will be translated to forward slashes and
   *                              extension .txt will be added to construct the filename relative to the resource root.
   * @param string|null $media    The media for which the CSS sources are optimized for. Note: use null for 'all'
   *                              devices; null is preferred over 'all'.
   *
   * @api
   * @since 2.0.0
   */
  public function cssPushSourcesList(string $location, ?string $media = null): void
  {
    [$path, $lines] = $this->cssReadListLocation($location, $media);
    $lines = array_reverse($lines, true);
    foreach ($lines as $i => $line)
    {
      $line = trim($line);
      if ($line!=='' && $line[0]!=='#')
      {
        $uri = $this->cssResolveListItem($line, $path, $i + 1);
        $this->cssOptimizedPushSource($uri, $media);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Echos the links to external and internal CSS.
   *
   * @api
   * @since 1.0.0
   */
  public function echoCascadingStyleSheets(): void
  {
    // Echo links to external CSS.
    foreach ($this->cssSources as $css_source)
    {
      echo Html::generateVoidElement('link', $css_source);
    }

    // Echos internal CSS.
    if (!empty($this->css))
    {
      echo '<style media="all">', implode('', $this->css), '</style>';
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Echos JavaScript code that will be executed using RequireJS.
   *
   * @api
   * @since 1.0.0
   */
  public function echoJavaScript(): void
  {
    if ($this->javaScript!==null)
    {
      $js = 'require([],function(){'.$this->javaScript.'});';
      echo '<script>/*<![CDATA[*/php_plaisio_inline_js=';
      echo json_encode($js);
      echo '/*]]>*/</script>';
    }
    if (!empty($this->jsTrailerAttributes))
    {
      echo Html::generateElement('script', $this->jsTrailerAttributes);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Echos the meta tags within the HTML document.
   *
   * @api
   * @since 1.0.0
   */
  public function echoMetaTags(): void
  {
    if (!empty($this->keywords))
    {
      $this->metaAttributes[] = ['name' => 'keywords', 'content' => implode(',', $this->keywords)];
    }

    $this->metaAttributes[] = ['charset' => Html::$encoding];

    foreach ($this->metaAttributes as $metaAttribute)
    {
      echo Html::generateVoidElement('meta', $metaAttribute);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Echos the HTML element for the page title.
   *
   * @see appendPageTitle()
   * @see getPageTitle()
   * @see pushPageTitle()
   * @see setPageTitle()
   *
   * @api
   * @since 1.0.0
   */
  public function echoPageTitle(): void
  {
    if ($this->title==='') return;

    echo '<title>', Html::txt2Html($this->title), '</title>';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the page title.
   *
   * @return string
   *
   * @see appendPageTitle()
   * @see echoPageTitle()
   * @see pushPageTitle()
   * @see setPageTitle()
   *
   * @api
   * @since 2.0.0
   */
  public function getPageTitle(): string
  {
    return $this->title;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Using RequiresJS calls a function in a namespace.
   *
   * @param string $name           One of:
   *                               <ul>
   *                               <li> The namespace as in RequireJS as a single or double quoted string literal.
   *                               <li> The __CLASS__ or __TRAIT__ magical constant.
   *                               <li> Name of a class specified by the ::class resolution operator.
   *                               </ul>
   *                               When a class name is given, backslashes will be translated to forward slashes to
   *                               construct the namespace as in RequireJS.
   * @param string $jsFunctionName The function name inside the namespace.
   * @param array  $args           The optional arguments for the function.
   *
   * @api
   * @since 1.0.0
   */
  public function jsAdmFunctionCall(string $name, string $jsFunctionName, array $args = []): void
  {
    if (strpos($name, '\\')!==false)
    {
      $namespace = $this->jsClassNameToNamespace($name);
    }
    else
    {
      $namespace = $name;
    }

    $fullPath = $this->rootRelativeUrlToFullPath($this->jsNamespaceToRootRelativeUrl($namespace));
    if (!file_exists($fullPath))
    {
      throw new LogicException("JavaScript file '%s' does not exists", $fullPath);
    }

    $this->jsAdmOptimizedFunctionCall($namespace, $jsFunctionName, $args);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Do not use this function, use {@link jsAdmFunctionCall} instead.
   *
   * @param string $namespace      The namespace as in RequireJS.
   * @param string $jsFunctionName The function name inside the namespace.
   * @param array  $args           The optional arguments for the function.
   */
  public function jsAdmOptimizedFunctionCall(string $namespace, string $jsFunctionName, array $args = []): void
  {
    $this->javaScript .= 'require(["';
    $this->javaScript .= $namespace;
    $this->javaScript .= '"],function(page){\'use strict\';page.';
    $this->javaScript .= $jsFunctionName;
    $this->javaScript .= '(';
    $this->javaScript .= implode(',', array_map('json_encode', $args));
    $this->javaScript .= ');});';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Do not use this function, use {@link jsAdmSetPageSpecificMain} instead.
   * ```
   * $this->jsAdmSetPageSpecificMain(__CLASS__);
   * ```
   *
   * @param string $mainJsScript The main script for RequireJS.
   */
  public function jsAdmOptimizedSetMain(string $mainJsScript): void
  {
    $this->jsTrailerAttributes = ['src' => $mainJsScript];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets a page specific main for RequireJS. Example:
   * ```
   * $this->jsAdmSetPageSpecificMain(__CLASS__);
   * ```
   *
   * @param string $className The PHP cass name, i.e. __CLASS__. Backslashes will be translated to forward slashes to
   *                          construct the namespace.
   *
   * @api
   * @since 1.0.0
   */
  public function jsAdmSetMain(string $className): void
  {
    // Convert PHP class name to root relative URL.
    $url = $this->jsClassNameToMainRootRelativeUrl($className);

    // Test JS file actually exists.
    $fullPath = $this->rootRelativeUrlToFullPath($url);
    if (!file_exists($fullPath))
    {
      throw new LogicException("JavaScript file '%s' does not exists", $fullPath);
    }

    $this->jsTrailerAttributes = ['src' => $this->jsNamespaceToRootRelativeUrl('require'), 'data-main' => $url];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $name
   */
  public function jsAdmSetPageSpecificMain(string $name): void
  {
    $this->jsAdmSetMain($name);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a meta element to this page.
   *
   * @param array $attributes The attributes of the meta element.
   *
   * @api
   * @since 1.0.0
   */
  public function metaAddElement(array $attributes): void
  {
    $this->metaAttributes[] = $attributes;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a keyword to the keywords to be included in the keyword meta element of this page.
   *
   * @param string $keyword The keyword.
   *
   * @api
   * @since 1.0.0
   */
  public function metaAddKeyword(string $keyword): void
  {
    $this->keywords[] = $keyword;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds keywords to the keywords to be included in the keyword meta element of the page.
   *
   * @param string[] $keywords The keywords.
   *
   * @api
   * @since 1.0.0
   */
  public function metaAddKeywords(array $keywords): void
  {
    $this->keywords = array_merge($this->keywords, $keywords);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Pushes with a separator a string to the page title.
   *
   * @param string|null $prefix The string to be prepended to the page title.
   *
   * @see appendPageTitle()
   * @see echoPageTitle()
   * @see getPageTitle()
   * @see setPageTitle()
   *
   * @api
   * @since 2.0.0
   */
  public function pushPageTitle(?string $prefix): void
  {
    if ($prefix===null || $prefix==='' || $prefix==='-') return;

    if ($this->title!=='')
    {
      $this->title = $prefix.self::$separator.$this->title;
    }
    else
    {
      $this->title = $prefix;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the title of the page.
   *
   * @param string|null $title The new title of the page.
   *
   * @see appendPageTitle()
   * @see echoPageTitle()
   * @see getPageTitle()
   * @see pushPageTitle()
   *
   * @api
   * @since 2.0.0
   */
  public function setPageTitle(?string $title): void
  {
    $this->title = ($title!==null) ? $title : '';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * If a URI is a relative tests the file exists.
   *
   * @param string $uri The URI.
   */
  private function assertUriExists(string $uri): void
  {
    if (Url::isRelative($uri))
    {
      $fullPath = $this->rootRelativeUrlToFullPath($uri);
      if (!file_exists($fullPath))
      {
        throw new LogicException("CSS file '%s' does not exists", $fullPath);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the relative URL for a class specific CSS file.
   *
   * @param string      $className The PHP class name, i.e. __CLASS__. Backslashes will be translated to forward
   *                               slashes to construct the filename relative to the resource root of the CSS source.
   * @param string|null $media     The media for which the CSS source is optimized for. Note: use null for 'all'
   *                               devices; null is preferred over 'all'.
   * @param string      $extension The extension of the filename. Either .css or .txt.
   *
   * @return string
   */
  private function cssClassNameToRootRelativeUrl(string $className, ?string $media, string $extension): string
  {
    $url = self::$cssRootRelativeUrl.$this->jsClassNameToNamespace($className);
    if ($media!==null) $url .= '.'.$media;
    $url .= $extension;

    return $url;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads the CSS list file and an array with the full path the the list file and the content of the list file as an
   * array of lines.
   *
   * @param string      $location  The location to the CSS source. One of:
   *                               <ul>
   *                               <li> A relative of absolute URL.
   *                               <li> The __CLASS__ or __TRAIT__ magical constant.
   *                               <li> Name of a class with specified by the ::class resolution operator.
   *                               </ul>
   *                               When a class name is given, backslashes will be translated to forward slashes and
   *                               the extension will be added to construct the filename relative to the resource
   *                               root.
   * @param string|null $media     The media for which the CSS source is optimized for. Note: use null for 'all'
   *                               devices;
   *                               null is preferred over 'all'.
   *
   * @return array
   */
  private function cssReadListLocation(string $location, ?string $media): array
  {
    $uri = $this->cssResolveLocation($location, $media, '.txt');

    $path = Path::join([$this->nub->dirs->assetsDir(), $uri]);
    if (!file_exists($path))
    {
      throw new LogicException("CSS list file '%s' does not exists", $path);
    }

    $content = file_get_contents($path);
    $lines   = explode(PHP_EOL, $content);

    if (!isset($lines[0]) || !preg_match('/^#.* plaisio-css-list/', $lines[0]))
    {
      throw new LogicException("CSS list file '%s' doesn't start with '# plaisio-css-list'", $path);
    }

    return [$path, $lines];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the path relative to resources root of an item in a CSS list file.
   *
   * @param string $cssPath  The item found in the list file, the path of a CSS file.
   * @param string $listPath The full path to the list file.
   * @param int    $lineno   The line number in $listPath.
   *
   * @return string
   */
  private function cssResolveListItem(string $cssPath, string $listPath, int $lineno): string
  {
    if ($cssPath[0]==='/')
    {
      $fullPathCssFile = Path::join([$this->nub->dirs->assetsDir(), $cssPath]);
    }
    else
    {
      $baseDir         = Path::getDirectory($listPath);
      $fullPathCssFile = Path::makeAbsolute($cssPath, $baseDir);
    }

    if (!file_exists($fullPathCssFile))
    {
      throw new LogicException("CSS file '%s' specified at %s:%d does not exists",
                               $fullPathCssFile,
                               $listPath,
                               $lineno);
    }

    $uri = Path::makeRelative($fullPathCssFile, $this->nub->dirs->assetsDir());
    $uri = '/'.$uri;

    return $uri;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Resolves a location to an URI.
   *
   * @param string      $location  The location to the CSS source. One of:
   *                               <ul>
   *                               <li> A relative of absolute URL.
   *                               <li> The __CLASS__ or __TRAIT__ magical constant.
   *                               <li> Name of a class with specified by the ::class resolution operator.
   *                               </ul>
   *                               When a class name is given, backslashes will be translated to forward slashes and
   *                               the extension will be added to construct the filename relative to the resource
   *                               root.
   * @param string|null $media     The media for which the CSS source is optimized for. Note: use null for 'all'
   *                               devices;
   *                               null is preferred over 'all'.
   * @param string      $extension The extension of the filename. Either .css or .txt.
   *
   * @return string
   */
  private function cssResolveLocation(string $location, ?string $media, string $extension): string
  {
    if (strpos($location, '\\')!==false)
    {
      $uri = $this->cssClassNameToRootRelativeUrl($location, $media, $extension);
    }
    else
    {
      $uri = $location;
    }

    return Url::combine(self::$cssRootRelativeUrl, $uri);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the relative URL for a class specific main JS file.
   *
   * @param string $className The PHP class name, i.e. __CLASS__. Backslashes will be translated to forward
   *                          slashes to construct the relative URL to the JS source.
   *
   * @return string
   */
  private function jsClassNameToMainRootRelativeUrl($className)
  {
    return self::$jsRootRelativeUrl.$this->jsClassNameToNamespace($className).'.main.js';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the namespace that corresponds with a class name.
   *
   * @param string $className The class name.
   *
   * @return string
   */
  private function jsClassNameToNamespace($className)
  {
    return str_replace('\\', '/', $className);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the root-relative URL for a class specific CSS file.
   *
   * @param string $namespace The namespace as in RequireJS.
   *
   * @return string
   */
  private function jsNamespaceToRootRelativeUrl($namespace)
  {
    return self::$jsRootRelativeUrl.$namespace.'.js';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the full path to a web asset based on its relative URL.
   *
   * @param string $url The relative URL.
   *
   * @return string
   */
  private function rootRelativeUrlToFullPath($url)
  {
    return $this->nub->dirs->assetsDir().$url;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
