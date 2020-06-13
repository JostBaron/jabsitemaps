<?php

// Add columns to the TCA
$newColumns = [
    'tx_ddgooglesitemap_lastmod' => [
        'exclude' => 1,
        'label' => '',
        'config' => [
            'type' => 'passthrough',
        ]
    ],
    'tx_ddgooglesitemap_priority' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority',
        'displayCond' => 'FIELD:no_search:=:0',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.0', 0],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.1', 1],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.2', 2],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.3', 3],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.4', 4],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.5', 5],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.6', 6],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.7', 7],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.8', 8],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.9', 9],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_priority.10', 10],
            ]
        ]
    ],
    'tx_ddgooglesitemap_change_frequency' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency',
        'displayCond' => 'FIELD:no_search:=:0',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.calculate', ''],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.always', 'always'],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.hourly', 'hourly'],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.daily', 'daily'],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.weekly', 'weekly'],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.monthly', 'monthly'],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.yearly', 'yearly'],
                ['LLL:EXT:jbsitemaps/locallang.xml:pages.tx_ddgooglesitemap_change_frequency.never', 'never'],
            ]
        ]
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $newColumns);
