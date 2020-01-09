<?php

return [

    "less" => [
        "enabled" => true,
        "compiler" => "lessc",
        "minify_all" => false,
        "minify_selected" => [
            resource_path("less/reset")
        ],
        "sources" => resource_path("less"),
        "compiled" => public_path("assets/css"),
    ],

	"svg" => [
        "enabled" => true,
		"sources" => resource_path("svg"),
		"http_path" => "/assets/images/svg",
		"compiled" => public_path("assets/images/svg"),
	],

    "scripts" => [
        "enabled" => true,
        "sources" => resource_path("js"),
        "compiled" => public_path("assets/js"),
    ]
];