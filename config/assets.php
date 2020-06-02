<?php

return [

    "debug" => true,

    "less" => [
        "enabled" => true,
        "minify" => true,
        "compiler" => "lessc", // mix or lessc
        "minify_all" => false,
        "minify_selected" => [
            resource_path("less/reset")
        ],
        "sources" => resource_path("less"),
        "compiled" => public_path("assets/css"),
    ],

	"svg" => [
        "enabled" => true,
        "compiler" => "php", // mix or php
		"sources" => resource_path("svg"),
		"http_path" => "/assets/images/svg",
		"compiled" => public_path("assets/images/svg"),
	],

    "scripts" => [
        "enabled" => true,
        "compiler" => "php", // mix or php
        "sources" => resource_path("js"),
        "compiled" => public_path("assets/js"),
    ]
];
