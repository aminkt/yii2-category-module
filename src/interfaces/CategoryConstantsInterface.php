<?php

namespace saghar\category\interfaces;

/**
 * Interface CategoryConstantsInterface
 * This interface can use to implement some nessessary constants for category active record but if you want
 * change the default values you can use your own interface.
 *
 * @package saghar\category\interfaces
 */
interface CategoryConstantsInterface {
    const STATUS_ACTIVE = 1;
    const STATUS_REMOVED = 2;
}