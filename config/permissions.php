<?php

/**
 * CricZone Permission Registry
 * ─────────────────────────────
 * Add new permissions here, then run:
 *   php artisan permissions:sync
 *
 * Format:  'module' => ['action', 'action', ...]
 * Stored as: module.action  (e.g. articles.create)
 */

return [
    'articles'    => ['view', 'create', 'edit', 'delete'],
    'videos'      => ['create', 'edit', 'delete'],
    'pages'       => ['create', 'edit', 'delete'],
    'categories'  => ['create', 'edit', 'delete'],
    'users'       => ['view', 'create', 'edit', 'delete'],
    'roles'       => ['view', 'create', 'edit', 'delete'],
    'settings'    => ['view', 'edit'],
    'live_scores' => ['manage'],
    'menus'       => ['manage'],
    'newsletter'  => ['view', 'send'],
    'polls'       => ['manage'],
];
