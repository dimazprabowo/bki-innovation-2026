<?php

if (!function_exists('email_logo_url')) {
    /**
     * Get the public URL for the email logo
     * Logo is hosted on DigitalOcean Spaces (S3) for email client compatibility
     */
    function email_logo_url(): string
    {
        return 'https://statutoria-monitoring-bucket.sgp1.digitaloceanspaces.com/assets/bki-logo.png';
    }
}

if (!function_exists('get_max_upload_size')) {
    /**
     * Get maximum upload size in KB for a specific field
     * 
     * @param string|null $fieldName Field name from config/file_upload.php
     * @return int Size in KB
     */
    function get_max_upload_size(?string $fieldName = null): int
    {
        $config = config('file_upload.fields.' . $fieldName, config('file_upload.default'));
        return (int) $config['max_size'];
    }
}

if (!function_exists('get_allowed_mimes')) {
    /**
     * Get allowed MIME types for a specific field
     * Returns comma-separated string
     * 
     * @param string|null $fieldName Field name from config/file_upload.php
     * @return string
     */
    function get_allowed_mimes(?string $fieldName = null): string
    {
        $config = config('file_upload.fields.' . $fieldName, config('file_upload.default'));
        return implode(',', $config['mimes']);
    }
}

if (!function_exists('get_allowed_mimes_array')) {
    /**
     * Get allowed MIME types as array for a specific field
     * 
     * @param string|null $fieldName Field name from config/file_upload.php
     * @return array
     */
    function get_allowed_mimes_array(?string $fieldName = null): array
    {
        $config = config('file_upload.fields.' . $fieldName, config('file_upload.default'));
        return $config['mimes'];
    }
}

if (!function_exists('file_upload_validation_rule')) {
    /**
     * Get complete file upload validation rule string for a specific field
     * Example: "nullable|file|max:2048|mimes:jpg,jpeg,png,pdf"
     * 
     * @param string|null $fieldName Field name from config/file_upload.php (e.g., 'photo-config', 'file-config')
     * @param bool $required Whether file is required
     * @return string
     */
    function file_upload_validation_rule(?string $fieldName = null, bool $required = false): string
    {
        $rules = [];
        
        $rules[] = $required ? 'required' : 'nullable';
        $rules[] = 'file';
        $rules[] = 'max:' . get_max_upload_size($fieldName);
        $rules[] = 'mimes:' . get_allowed_mimes($fieldName);
        
        return implode('|', $rules);
    }
}

if (!function_exists('get_upload_config_display')) {
    /**
     * Get human-readable upload configuration for display
     * Example: "Max: 2 MB | Types: JPG, JPEG, PNG, PDF"
     * 
     * @param string|null $fieldName Field name from config/file_upload.php
     * @return string
     */
    function get_upload_config_display(?string $fieldName = null): string
    {
        $maxSizeKB = get_max_upload_size($fieldName);
        $maxSizeMB = $maxSizeKB / 1024;
        $mimes = strtoupper(str_replace(',', ', ', get_allowed_mimes($fieldName)));
        
        return "Max: {$maxSizeMB} MB | Types: {$mimes}";
    }
}

// ============================================================================
// System Configuration Helpers
// ============================================================================

if (!function_exists('app_name')) {
    /**
     * Get application name from system configuration
     * 
     * @return string
     */
    function app_name(): string
    {
        return \App\Helpers\ConfigHelper::getAppName();
    }
}

if (!function_exists('system_config')) {
    /**
     * Get system configuration value
     * 
     * @param string $key Configuration key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    function system_config(string $key, $default = null)
    {
        return \App\Helpers\ConfigHelper::get($key, $default);
    }
}

if (!function_exists('is_registration_open')) {
    /**
     * Check if registration is currently open
     * 
     * @return bool
     */
    function is_registration_open(): bool
    {
        return \App\Helpers\ConfigHelper::isRegistrationOpen();
    }
}

if (!function_exists('registration_deadline')) {
    /**
     * Get registration deadline as Carbon instance
     * 
     * @return \Carbon\Carbon|null
     */
    function registration_deadline(): ?\Carbon\Carbon
    {
        return \App\Helpers\ConfigHelper::getRegistrationDeadline();
    }
}

if (!function_exists('registration_closed_message')) {
    /**
     * Get registration closed message
     * 
     * @return string
     */
    function registration_closed_message(): string
    {
        return \App\Helpers\ConfigHelper::getRegistrationClosedMessage();
    }
}
