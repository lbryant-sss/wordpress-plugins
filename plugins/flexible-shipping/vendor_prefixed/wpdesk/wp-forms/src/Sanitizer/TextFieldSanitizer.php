<?php

namespace FSVendor\WPDesk\Forms\Sanitizer;

use FSVendor\WPDesk\Forms\Sanitizer;
class TextFieldSanitizer implements Sanitizer
{
    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
