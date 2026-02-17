<?php
// helpers/date_helper.php

/**
 * Format a date string into a readable format
 *
 * @param string|null $date       The date string
 * @param string      $format     Desired format, default 'd M Y'
 * @return string                 Formatted date or '-' if null/invalid
 */
function format_date(?string $date, string $format = 'd M Y'): string {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }

    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '-';
    }

    return date($format, $timestamp);
}

/**
 * Format a datetime string with time
 *
 * @param string|null $datetime
 * @param string $format
 * @return string
 */
function format_datetime(?string $datetime, string $format = 'd M Y H:i') {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }

    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return '-';
    }

    return date($format, $timestamp);
}

/**
 * Convert timestamp to "time ago" format
 *
 * @param string|null $datetime
 * @return string
 */
function time_ago(?string $datetime): string {
    if (empty($datetime)) return '-';
    
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
    if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';
    
    return floor($diff / 31536000) . ' years ago';
}
