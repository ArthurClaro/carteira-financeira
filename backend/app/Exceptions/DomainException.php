<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Base para erros de regra de negócio. Carregam mensagem amigável (PT-BR) e são
 * capturadas na camada de apresentação (componentes Livewire) para virar feedback.
 */
class DomainException extends RuntimeException {}
