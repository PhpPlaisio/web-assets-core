<?php
declare(strict_types=1);

namespace Plaisio\WebAssets\Test;

use Plaisio\Dirs\CoreDirs;
use Plaisio\Kernel\Nub;

/**
 * Kernel for test purposes.
 */
class TestKernel extends Nub
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    parent::__construct();

    self::$dirs = new CoreDirs(__DIR__);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
