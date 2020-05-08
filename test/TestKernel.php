<?php
declare(strict_types=1);

namespace Plaisio\WebAssets\Test;

use Plaisio\Dirs\CoreDirs;
use Plaisio\Dirs\Dirs;
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
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the object for getting directory names.
   *
   * @return Dirs
   */
  protected function getDirs(): Dirs
  {
    return new CoreDirs(__DIR__);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
