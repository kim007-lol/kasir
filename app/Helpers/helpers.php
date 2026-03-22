<?php

if (!function_exists('isDemo')) {
    /**
     * Check if the application is running in 'demo' mode.
     */
    function isDemo(): bool
    {
        return config('app.mode') === 'demo';
    }
}

if (!function_exists('isDemoUser')) {
    /**
     * Check if the currently authenticated user is a demo account.
     * Identifying demo accounts by the 'demo_' prefix in their username.
     */
    function isDemoUser(): bool
    {
        // Sangat aman: hanya dibatasi jika APP_MODE=demo DAN username diawali demo_
        return isDemo() && auth()->check() && str_starts_with(auth()->user()->username ?? '', 'demo_');
    }
}
