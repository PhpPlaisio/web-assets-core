<?php
declare(strict_types=1);

namespace Plaisio\WebAssets\Test;

use Plaisio\Dirs\CoreDirs;
use Plaisio\Dirs\Dirs;
use Plaisio\PlaisioKernel;

/**
 * Kernel for test purposes.
 */
class TestKernel extends PlaisioKernel
{
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
