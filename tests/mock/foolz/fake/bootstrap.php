<?php

\Foolz\Plugin\Event::forge('the.bootstrap.was.loaded')
    ->setCall(function($result) {
        $result->set('success');
    });

\Foolz\Plugin\Event::forge('foolz\plugin\plugin.execute.foolz/fake')
    ->setCall(function($result) {
        $result->set('success');
    });

\Foolz\Plugin\Event::forge('foolz\plugin\plugin.install.foolz/fake')
    ->setCall(function($result) {
        $result->set('success');
    });

\Foolz\Plugin\Event::forge('foolz\plugin\plugin.uninstall.foolz/fake')
    ->setCall(function($result) {
        $result->set('success');
    });

\Foolz\Plugin\Event::forge('foolz\plugin\plugin.upgrade.foolz/fake')
    ->setCall(function($result) {
        $result->set('success');
    });
    