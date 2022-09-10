<?php

    foreach (glob(__DIR__ . '/app/*.php') as $file) {
        require_once $file;
    }
	foreach (glob(__DIR__.'/admin/*.php') as $file)
	{
		require_once $file;
	}
