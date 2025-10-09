<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'report/history_student_ai:view' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
    ],
];
