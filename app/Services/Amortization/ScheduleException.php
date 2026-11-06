<?php

namespace App\Services\Amortization;

use DomainException;

/**
 * A schedule that cannot be built with the given terms. The message is meant for the user.
 */
class ScheduleException extends DomainException {}
