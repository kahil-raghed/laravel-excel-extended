<?php

namespace LaravelExcelExtended\Concerns;

interface WithMaxWidth {

    /**
     * Get Max Width.
     * @return float | array<float>
     */
    public function maxWidth(): float | array;
}