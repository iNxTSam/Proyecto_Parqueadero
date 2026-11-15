<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Error de negocio que debe mostrarse al usuario sin romper la aplicación.
 */
class BusinessRuleException extends RuntimeException {}
