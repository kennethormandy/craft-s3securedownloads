<?php
/**
 *
 * @since 2.3.0
 */

namespace kennethormandy\s3securedownloads\events;

use craft\events\ModelEvent;

class SignUrlEvent extends ModelEvent
{
  public $asset;
}
